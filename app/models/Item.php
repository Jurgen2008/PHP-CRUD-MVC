<?php
final class Item
{
    public function __construct(private PDO $db) {}

    public function all(string $search): array
    {
        $sql = 'SELECT id, naam, prijs, inhoud, voorraad FROM voorraad';
        $params = [];
        if ($search !== '') {
            $sql .= ' WHERE LOCATE(?, naam) > 0';
            $params[] = $search;
        }
        $statement = $this->db->prepare($sql . ' ORDER BY naam, id');
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM voorraad WHERE id = ?');
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public function create(array $values): void
    {
        $statement = $this->db->prepare('INSERT INTO voorraad (naam, prijs, inhoud, voorraad)
            VALUES (:naam, :prijs, :inhoud, :voorraad)');
        $statement->execute($values);
    }

    public function update(int $id, array $values): bool
    {
        $statement = $this->db->prepare('UPDATE voorraad SET naam = :naam, prijs = :prijs,
            inhoud = :inhoud, voorraad = :voorraad WHERE id = :id');
        $statement->execute($values + ['id' => $id]);
        return $statement->rowCount() > 0 || $this->find($id) !== null;
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare('DELETE FROM voorraad WHERE id = ?');
        $statement->execute([$id]);
        return $statement->rowCount() === 1;
    }
}
