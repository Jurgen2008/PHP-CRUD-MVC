# Voorraadbeheer

Met deze PHP-app beheer je de prijs, verpakkingsinhoud en voorraad van artikelen. Het overzicht laat alle vier de velden zien. Zoek bijvoorbeeld op `papier` om Kopieerpapier A4 terug te vinden.

## Lokaal starten

Gebruik PHP 8.4 met `pdo_mysql` en `mbstring` en een lokale MySQL-server (8.0 of hoger). De webroot is `public/`.

1. Start MySQL. Maak voor een nieuwe installatie in phpMyAdmin een lege database `voorraadbeheer` met tekenset `utf8mb4`.
2. Importeer [de SQL-export](database/voorraadbeheer.sql) in die lege database. De export bevat de tabel en drie voorbeeldartikelen. Staat de projecttabel al klaar, ga dan direct naar stap 3.
3. Start vanuit de projectmap `php -S 127.0.0.1:8000 -t public` en open `http://127.0.0.1:8000`. In een geparkeerde Herd-map werkt ook `http://voorraadbeheer.test`.

De lokale standaardverbinding gebruikt MySQL op 127.0.0.1, poort 3306, gebruiker root en een leeg wachtwoord. Voor andere instellingen leest [database.php](config/database.php) de omgevingsvariabelen `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` en `DB_PASSWORD`. Zet ze in de omgeving van het PHP-proces.

## Hoe een wijziging wordt opgeslagen

[public/index.php](public/index.php) kiest de actie en controleert bij POST het sessietoken. De [controller](app/controllers/ItemController.php) zoekt het artikel op, controleert de velden en geeft de waarden door aan het [model](app/models/Item.php). Het model bindt de waarden aan een PDO-statement. Na het opslaan stuurt de controller de browser terug naar het overzicht. De [views](app/views/items/) tonen tekst via de HTML-helper `h()`.

Het [DrawSQL-ontwerp](database/drawSQL-image-export-2026-11-18.jpg) volgt de bestaande tabel `voorraad`. De prijs staat in `DECIMAL(6,2)`, zodat MySQL bedragen met twee decimalen bewaart. De gebruiker mag een punt of komma invoeren. Nul stuks is een geldige voorraad.

## Expertchallenges

| Actie | Wat je kunt testen |
| --- | --- |
| Toevoegen | Validatie per veld, behoud van ingevulde waarden, een numeriek voorraadveld en bescherming met een sessietoken. |
| Tonen | Zoeken op een deel van de artikelnaam. |
| Bewerken | Dezelfde validatie als bij toevoegen, melding bij een ontbrekend ID en annuleren zonder opslaan. |
| Verwijderen | Een bevestigingspagina noemt het artikel. De definitieve POST vereist een geldig token. Een ontbrekend artikel krijgt een eigen melding. |

## Testen en reviewen

Voor de automatische test heb je ook Python 3 en een lokale MySQL-gebruiker met rechten om een testdatabase aan te maken en op te ruimen nodig. Voer vanuit de projectmap `python tests/test_crud.py` uit. Het script maakt een eigen database met een unieke naam, importeert de export en test via HTTP. Na afloop ruimt het de testdatabase op.

De [schoolformulieren](inlevering/) bevatten de verwachte uitkomsten en aanknopingspunten in de code. Gebruik voor iedere tester een eigen kopie van de database en zet `DB_NAME` vÃƒÂ³ÃƒÂ³r het starten van PHP op de naam van die kopie. Zo kan ook de lege voorraad worden getest. Twee verschillende testers vullen hun eigen resultaten in. Een medestudent voert de codereview uit en noteert bevindingen en verbeterpunten.

Lever de ingevulde formulieren samen met de link naar [de GitHub-repository](https://github.com/Jurgen2008/PHP-CRUD-MVC) in.
