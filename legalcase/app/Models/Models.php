<?php

// ═══════════════════════════════════════════
// CLIENT MODEL
// ═══════════════════════════════════════════
class ClientModel extends Model {
    protected static string $table = 'clients';

    public static function create(array $d): int {
        $db   = Database::connect();
        $stmt = $db->prepare("INSERT INTO clients (full_name,email,phone,address,id_number,date_of_birth,notes,created_by) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$d['full_name'],$d['email']??'',$d['phone']??'',$d['address']??'',$d['id_number']??'',$d['date_of_birth']?:null,$d['notes']??'',$d['created_by']]);
        return (int)$db->lastInsertId();
    }

    public static function updateClient(int $id, array $d): bool {
        $db   = Database::connect();
        $stmt = $db->prepare("UPDATE clients SET full_name=?,email=?,phone=?,address=?,id_number=?,date_of_birth=?,notes=?,updated_at=NOW() WHERE id=?");
        return $stmt->execute([$d['full_name'],$d['email']??'',$d['phone']??'',$d['address']??'',$d['id_number']??'',$d['date_of_birth']?:null,$d['notes']??'',$id]);
    }

    public static function getCases(int $clientId): array {
        $db   = Database::connect();
        $stmt = $db->prepare("SELECT c.*, u.name AS lawyer_name FROM cases c LEFT JOIN users u ON u.id=c.lawyer_id WHERE c.client_id=? ORDER BY c.created_at DESC");
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }

    public static function search(string $q): array {
        $db   = Database::connect();
        $stmt = $db->prepare("SELECT * FROM clients WHERE full_name LIKE ? OR email LIKE ? OR phone LIKE ? LIMIT 20");
        $stmt->execute(["%$q%","%$q%","%$q%"]);
        return $stmt->fetchAll();
    }

    public static function allPaginated(int $limit, int $offset, string $search = ''): array {
        $db = Database::connect();
        if ($search) {
            $stmt = $db->prepare("SELECT * FROM clients WHERE full_name LIKE ? OR phone LIKE ? ORDER BY full_name LIMIT ? OFFSET ?");
            $stmt->execute(["%$search%","%$search%",$limit,$offset]);
        } else {
            $stmt = $db->prepare("SELECT * FROM clients ORDER BY full_name LIMIT ? OFFSET ?");
            $stmt->execute([$limit,$offset]);
        }
        return $stmt->fetchAll();
    }
}

// ═══════════════════════════════════════════
// USER MODEL
// ═══════════════════════════════════════════
class UserModel extends Model {
    protected static string $table = 'users';

    public static function findByEmail(string $email): ?array {
        $db   = Database::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $d): int {
        $db   = Database::connect();
        $stmt = $db->prepare("INSERT INTO users (name,email,password,role,phone) VALUES (?,?,?,?,?)");
        $stmt->execute([$d['name'],$d['email'],password_hash($d['password'],PASSWORD_DEFAULT),$d['role'],$d['phone']??'']);
        return (int)$db->lastInsertId();
    }

    public static function updateUser(int $id, array $d): bool {
        $db   = Database::connect();
        $stmt = $db->prepare("UPDATE users SET name=?,email=?,role=?,phone=?,updated_at=NOW() WHERE id=?");
        return $stmt->execute([$d['name'],$d['email'],$d['role'],$d['phone']??'',$id]);
    }

    public static function updatePassword(int $id, string $password): bool {
        $db   = Database::connect();
        $stmt = $db->prepare("UPDATE users SET password=?,updated_at=NOW() WHERE id=?");
        return $stmt->execute([password_hash($password,PASSWORD_DEFAULT),$id]);
    }

    public static function toggleActive(int $id): bool {
        $db   = Database::connect();
        $stmt = $db->prepare("UPDATE users SET is_active = NOT is_active WHERE id=?");
        return $stmt->execute([$id]);
    }

    public static function getLawyers(): array {
        $db = Database::connect();
        return $db->query("SELECT id,name FROM users WHERE role='lawyer' AND is_active=1 ORDER BY name")->fetchAll();
    }
}

// ═══════════════════════════════════════════
// DOCUMENT MODEL
// ═══════════════════════════════════════════
class DocumentModel extends Model {
    protected static string $table = 'documents';

    public static function create(array $d): int {
        $db   = Database::connect();
        $stmt = $db->prepare("INSERT INTO documents (case_id,title,doc_type,file_name,file_path,file_size,mime_type,uploaded_by) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$d['case_id'],$d['title'],$d['doc_type'],$d['file_name'],$d['file_path'],$d['file_size'],$d['mime_type'],$d['uploaded_by']]);
        return (int)$db->lastInsertId();
    }

    public static function getByCaseId(int $caseId): array {
        $db   = Database::connect();
        $stmt = $db->prepare("SELECT d.*, u.name AS uploader FROM documents d LEFT JOIN users u ON u.id=d.uploaded_by WHERE d.case_id=? ORDER BY d.created_at DESC");
        $stmt->execute([$caseId]);
        return $stmt->fetchAll();
    }
}

// ═══════════════════════════════════════════
// HEARING MODEL
// ═══════════════════════════════════════════
class HearingModel extends Model {
    protected static string $table = 'hearings';

    public static function create(array $d): int {
        $db   = Database::connect();
        $stmt = $db->prepare("INSERT INTO hearings (case_id,title,hearing_date,hearing_time,court_room,court_name,judge_name,notes,created_by) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$d['case_id'],$d['title'],$d['hearing_date'],$d['hearing_time']??null,$d['court_room']??'',$d['court_name']??'',$d['judge_name']??'',$d['notes']??'',$d['created_by']]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $d): bool {
        $db   = Database::connect();
        $stmt = $db->prepare("UPDATE hearings SET title=?,hearing_date=?,hearing_time=?,court_room=?,court_name=?,judge_name=?,status=?,notes=?,updated_at=NOW() WHERE id=?");
        return $stmt->execute([$d['title'],$d['hearing_date'],$d['hearing_time']??null,$d['court_room']??'',$d['court_name']??'',$d['judge_name']??'',$d['status'],$d['notes']??'',$id]);
    }

    public static function allWithCase(int $limit = 20, int $offset = 0): array {
        $db   = Database::connect();
        $stmt = $db->prepare("SELECT h.*, c.case_number, c.title AS case_title FROM hearings h LEFT JOIN cases c ON c.id=h.case_id ORDER BY h.hearing_date ASC LIMIT ? OFFSET ?");
        $stmt->execute([$limit,$offset]);
        return $stmt->fetchAll();
    }

    public static function upcoming(int $limit = 10): array {
        $db   = Database::connect();
        $stmt = $db->prepare("SELECT h.*, c.case_number, c.title AS case_title FROM hearings h LEFT JOIN cases c ON c.id=h.case_id WHERE h.hearing_date >= CURDATE() AND h.status='scheduled' ORDER BY h.hearing_date ASC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public static function getByCaseId(int $caseId): array {
        $db   = Database::connect();
        $stmt = $db->prepare("SELECT * FROM hearings WHERE case_id=? ORDER BY hearing_date DESC");
        $stmt->execute([$caseId]);
        return $stmt->fetchAll();
    }
}

// ═══════════════════════════════════════════
// NOTIFICATION MODEL
// ═══════════════════════════════════════════
class NotificationModel extends Model {
    protected static string $table = 'notifications';

    public static function getForUser(int $userId, int $limit = 10): array {
        $db   = Database::connect();
        $stmt = $db->prepare("SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$userId,$limit]);
        return $stmt->fetchAll();
    }

    public static function unreadCount(int $userId): int {
        $db   = Database::connect();
        $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    public static function markAllRead(int $userId): void {
        $db   = Database::connect();
        $stmt = $db->prepare("UPDATE notifications SET is_read=1 WHERE user_id=?");
        $stmt->execute([$userId]);
    }

    public static function create(int $userId, string $title, string $message, string $type = 'system', string $link = ''): void {
        $db   = Database::connect();
        $stmt = $db->prepare("INSERT INTO notifications (user_id,title,message,type,link) VALUES (?,?,?,?,?)");
        $stmt->execute([$userId,$title,$message,$type,$link]);
    }
}
