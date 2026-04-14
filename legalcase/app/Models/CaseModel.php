<?php

class CaseModel extends Model {
    protected static string $table = 'cases';

    public static function allWithRelations(int $limit = 20, int $offset = 0, array $filters = []): array {
        $db = Database::connect();
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'c.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['type'])) {
            $where[] = 'c.case_type = ?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(c.title LIKE ? OR c.case_number LIKE ?)';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['lawyer_id'])) {
            $where[] = 'c.lawyer_id = ?';
            $params[] = $filters['lawyer_id'];
        }

        $whereStr = implode(' AND ', $where);
        $sql = "SELECT c.*, 
                    cl.full_name AS client_name,
                    u.name AS lawyer_name
                FROM cases c
                LEFT JOIN clients cl ON cl.id = c.client_id
                LEFT JOIN users u    ON u.id  = c.lawyer_id
                WHERE $whereStr
                ORDER BY c.created_at DESC
                LIMIT ? OFFSET ?";

        $params[] = $limit;
        $params[] = $offset;
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function findWithRelations(int $id): ?array {
        $db   = Database::connect();
        $stmt = $db->prepare("
            SELECT c.*,
                cl.full_name AS client_name,
                cl.phone     AS client_phone,
                u.name       AS lawyer_name,
                u.email      AS lawyer_email
            FROM cases c
            LEFT JOIN clients cl ON cl.id = c.client_id
            LEFT JOIN users u    ON u.id  = c.lawyer_id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int {
        $db   = Database::connect();
        $stmt = $db->prepare("
            INSERT INTO cases (case_number, title, case_type, status, description, lawyer_id, client_id, court_name, judge_name, filing_date, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['case_number'], $data['title'], $data['case_type'],
            $data['status'] ?? 'filed', $data['description'] ?? '',
            $data['lawyer_id'] ?: null, $data['client_id'] ?: null,
            $data['court_name'] ?? '', $data['judge_name'] ?? '',
            $data['filing_date'] ?: null, $data['created_by'],
        ]);
        return (int)$db->lastInsertId();
    }

    public static function updateCase(int $id, array $data): bool {
        $db   = Database::connect();
        $stmt = $db->prepare("
            UPDATE cases SET title=?, case_type=?, status=?, description=?, lawyer_id=?, client_id=?,
            court_name=?, judge_name=?, filing_date=?, closing_date=?, updated_at=NOW()
            WHERE id=?
        ");
        return $stmt->execute([
            $data['title'], $data['case_type'], $data['status'],
            $data['description'] ?? '', $data['lawyer_id'] ?: null,
            $data['client_id'] ?: null, $data['court_name'] ?? '',
            $data['judge_name'] ?? '', $data['filing_date'] ?: null,
            $data['closing_date'] ?: null, $id,
        ]);
    }

    public static function getNotes(int $caseId): array {
        $db   = Database::connect();
        $stmt = $db->prepare("
            SELECT cn.*, u.name AS author
            FROM case_notes cn
            LEFT JOIN users u ON u.id = cn.user_id
            WHERE cn.case_id = ?
            ORDER BY cn.created_at DESC
        ");
        $stmt->execute([$caseId]);
        return $stmt->fetchAll();
    }

    public static function addNote(int $caseId, string $note, int $userId): void {
        $db   = Database::connect();
        $stmt = $db->prepare("INSERT INTO case_notes (case_id, user_id, note) VALUES (?,?,?)");
        $stmt->execute([$caseId, $userId, $note]);
    }

    public static function countByStatus(): array {
        $db  = Database::connect();
        $res = $db->query("SELECT status, COUNT(*) as total FROM cases GROUP BY status")->fetchAll();
        $out = [];
        foreach ($res as $r) $out[$r['status']] = $r['total'];
        return $out;
    }

    public static function countFiltered(array $filters = []): int {
        $db = Database::connect();
        $where = ['1=1'];
        $params = [];
        if (!empty($filters['status'])) { $where[] = 'status = ?'; $params[] = $filters['status']; }
        if (!empty($filters['type']))   { $where[] = 'case_type = ?'; $params[] = $filters['type']; }
        if (!empty($filters['search'])) {
            $where[] = '(title LIKE ? OR case_number LIKE ?)';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        $stmt = $db->prepare("SELECT COUNT(*) FROM cases WHERE " . implode(' AND ', $where));
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
}
