<?php

abstract class Model {
    protected static string $table = '';
    protected PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public static function all(string $orderBy = 'created_at DESC'): array {
        $db = Database::connect();
        return $db->query("SELECT * FROM " . static::$table . " ORDER BY $orderBy")->fetchAll();
    }

    public static function find(int $id): ?array {
        $db   = Database::connect();
        $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function where(string $column, mixed $value): array {
        $db   = Database::connect();
        $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE $column = ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    public static function count(string $where = '', array $params = []): int {
        $db   = Database::connect();
        $sql  = "SELECT COUNT(*) FROM " . static::$table;
        if ($where) $sql .= " WHERE $where";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public static function delete(int $id): bool {
        $db   = Database::connect();
        $stmt = $db->prepare("DELETE FROM " . static::$table . " WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
