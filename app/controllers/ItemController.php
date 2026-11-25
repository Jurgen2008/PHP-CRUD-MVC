<?php
final class ItemController
{
    public function __construct(private Item $items) {}

    public function index(): void
    {
        $search = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
        $items = $this->items->all($search);
        view('index', 'Voorraadoverzicht', compact('items', 'search'));
    }

    public function form(?int $id): void
    {
        $values = $id === null ? ['naam' => '', 'inhoud' => '', 'prijs' => '', 'voorraad' => ''] : $this->record($id);
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $values = [];
            foreach (['naam', 'prijs', 'inhoud', 'voorraad'] as $field) {
                $values[$field] = is_string($_POST[$field] ?? null) ? trim($_POST[$field]) : '';
            }
            foreach (['naam', 'inhoud'] as $field) {
                if ($values[$field] === '' || !mb_check_encoding($values[$field], 'UTF-8') || mb_strlen($values[$field]) > 100) {
                    $errors[$field] = 'Vul 1 tot 100 tekens in.';
                }
            }
            $price = str_replace(',', '.', $values['prijs']);
            if (!preg_match('/^[0-9]{1,4}(?:[.][0-9]{1,2})?$/D', $price)) {
                $errors['prijs'] = 'Vul een prijs tussen 0 en 9999,99 in, met maximaal twee decimalen.';
            }
            $number = ltrim($values['voorraad'], '0');
            if (!ctype_digit($values['voorraad']) || strlen($number) > 10 || (int)$number > 2147483647) {
                $errors['voorraad'] = 'Vul een geheel aantal tussen 0 en 2147483647 in.';
            }
            if (!$errors) {
                $values['voorraad'] = (int)$number;
                $values['prijs'] = $price;
                if ($id === null) {
                    $this->items->create($values);
                    success('Artikel toegevoegd.');
                }
                if (!$this->items->update($id, $values)) {
                    problem(404, 'Dit artikel bestaat niet meer.');
                }
                success('Wijzigingen opgeslagen.');
            }
            http_response_code(422);
        }
        view('form', $id === null ? 'Artikel toevoegen' : 'Artikel bewerken', compact('values', 'errors', 'id'));
    }

    public function remove(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->items->delete($id)) {
                problem(404, 'Dit artikel bestaat niet meer.');
            }
            success('Artikel verwijderd.');
        }
        view('delete', 'Artikel verwijderen', ['item' => $this->record($id)]);
    }

    private function record(int $id): array
    {
        return $this->items->find($id) ?? problem(404, 'Dit artikel bestaat niet meer.');
    }
}
