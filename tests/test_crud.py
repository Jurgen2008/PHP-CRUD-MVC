"""Voer uit met Python 3, de PHP CLI en een lokale MySQL-server."""
import os
import re
import socket
import subprocess
import time
import uuid
from http.cookiejar import CookieJar
from pathlib import Path
from urllib.error import HTTPError, URLError
from urllib.parse import urlencode
from urllib.request import build_opener, HTTPCookieProcessor, ProxyHandler

ROOT = Path(__file__).resolve().parents[1]
NAME = 'voorraad_test_' + uuid.uuid4().hex
ENV = {**os.environ, 'DB_NAME': NAME}


def php(code):
    return subprocess.check_output(['php', '-r', code], cwd=ROOT, env=ENV, text=True)


def main():
    admin = """$db = new PDO("mysql:host=".(getenv("DB_HOST") ?: "127.0.0.1").
        ";port=".(getenv("DB_PORT") ?: "3306"), getenv("DB_USER") ?: "root", getenv("DB_PASSWORD") ?: "");"""
    php(admin + '$db->exec("CREATE DATABASE " . getenv("DB_NAME") . " CHARACTER SET utf8mb4");')
    server = None
    try:
        php('require "config/database.php"; database()->exec(file_get_contents("database/voorraadbeheer.sql"));')
        with socket.socket() as sock:
            sock.bind(('127.0.0.1', 0))
            port = sock.getsockname()[1]
        server = subprocess.Popen(['php', '-S', f'127.0.0.1:{port}', '-t', 'public'],
                                  cwd=ROOT, env=ENV, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        browser = build_opener(ProxyHandler({}), HTTPCookieProcessor(CookieJar()))

        def page(query='', data=None, expected=200):
            body = urlencode(data).encode() if data is not None else None
            try:
                response = browser.open(f'http://127.0.0.1:{port}/?{query}', body, timeout=5)
            except HTTPError as error:
                response = error
            with response:
                assert response.status == expected, (query, response.status, expected)
                return response.read().decode()

        for attempt in range(50):
            try:
                overview = page()
                break
            except URLError:
                if attempt == 49 or server.poll() is not None:
                    raise
                time.sleep(0.1)
        assert all(name in overview for name in ('Kopieerpapier A4', 'Whiteboardmarkers', 'Verpakkingstape'))
        form = page('action=create')
        token = re.search(r'name="token" value="([a-f0-9]+)"', form)[1]
        article = dict(naam="Papier & karton", inhoud="<wit>", prijs="2,75", voorraad="12", token=token)
        empty = page('action=create', {'token': token}, 422)
        assert empty.count('aria-invalid="true"') == 4
        page('action=create', {**article, 'token': ''}, 403)
        for name in ('Testmap', 'Testpen', article['naam']):
            assert 'Artikel toegevoegd.' in page('action=create', {**article, 'naam': name})
        results = page('q=Papier')
        assert 'Papier &amp; karton' in results and '&lt;wit&gt;' in results and 'Testpen' not in results
        assert 'Geen artikelen gevonden' in page('q=onvindbaar')
        item_id = re.search(r'action=edit&amp;id=(\d+)" aria-label="Bewerken: Papier &amp; karton', results)[1]
        edit = 'action=edit&id=' + item_id
        delete = 'action=delete&id=' + item_id
        assert 'value="2.75"' in page(edit)
        invalid = page(edit, {**article, 'prijs': '12,345', 'voorraad': '-1'}, 422)
        assert 'error-prijs' in invalid and 'error-voorraad' in invalid and 'value="12,345"' in invalid
        assert 'value="12"' in page(edit)
        assert 'Wijzigingen opgeslagen.' in page(edit, {**article, 'voorraad': '0'})
        after = page(edit)
        for value in ('0', '2.75', 'Papier &amp; karton', '&lt;wit&gt;'):
            assert f'value="{value}"' in after
        assert 'Definitief verwijderen' in page(delete)
        page(delete, {}, 403)
        assert 'Papier &amp; karton' in page()
        assert 'Artikel verwijderd.' in page(delete, {'token': token})
        page(edit, expected=404)
        page(delete, {'token': token}, 404)
        overview = page()
        assert 'Testpen' in overview and 'Testmap' in overview and 'Papier &amp; karton' not in overview
        for remaining in re.findall(r'action=delete&amp;id=(\d+)', overview):
            page('action=delete&id=' + remaining, {'token': token})
        assert 'De voorraad is leeg' in page()
        print('Geslaagd: SQL-import, vier CRUD-acties, validatie, zoekfunctie, formulierbeveiliging en lege voorraad.')
    finally:
        if server is not None:
            server.terminate()
            server.wait()
        php(admin + '$db->exec("DROP DATABASE " . getenv("DB_NAME"));')


if __name__ == '__main__':
    main()
