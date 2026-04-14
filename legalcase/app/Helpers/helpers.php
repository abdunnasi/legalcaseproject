<?php

require_once BASE_PATH . '/config/database.php';

// ─── View Renderer ───────────────────────────────────────────────
function view(string $template, array $data = []): void
{
    extract($data);
    $file = VIEW_PATH . '/' . str_replace('.', '/', $template) . '.php';
    if (!file_exists($file)) {
        die("View not found: $file");
    }
    require $file;
}

// ─── Redirect ────────────────────────────────────────────────────
function redirect(string $path): void
{
    header("Location: " . APP_URL . $path);
    exit;
}

// ─── Session Flash Messages ───────────────────────────────────────
function flash(string $key, string $message = '', string $type = 'success'): ?array
{
    if ($message) {
        $_SESSION['flash'][$key] = ['message' => $message, 'type' => $type];
        return null;
    }
    if (isset($_SESSION['flash'][$key])) {
        $flash = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $flash;
    }
    return null;
}

// ─── Auth Helpers ────────────────────────────────────────────────
function auth(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

function hasRole(string|array $roles): bool
{
    $user = auth();
    if (!$user) return false;
    $roles = is_array($roles) ? $roles : [$roles];
    return in_array($user['role'], $roles);
}

// ─── CSRF Protection ─────────────────────────────────────────────
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf(): bool
{
    $token = $_POST['_csrf_token'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

// ─── Input Sanitization ───────────────────────────────────────────
function clean(string $value): string
{
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

function input(string $key, string $default = ''): string
{
    return clean($_POST[$key] ?? $default);
}

function get(string $key, string $default = ''): string
{
    return clean($_GET[$key] ?? $default);
}

// ─── Case Number Generator ────────────────────────────────────────
function generateCaseNumber(): string
{
    $year = date('Y');
    $db = Database::connect();
    $stmt = $db->query("SELECT COUNT(*) FROM cases WHERE YEAR(created_at) = $year");
    $count = (int)$stmt->fetchColumn();
    return 'LC-' . $year . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
}

// ─── File Upload Helper ───────────────────────────────────────────
function uploadFile(array $file, int $caseId): array|false
{
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    if ($file['size'] > MAX_FILE_SIZE) return false;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_TYPES)) return false;

    $dir = UPLOAD_PATH . $caseId . '/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $newName = uniqid('doc_', true) . '.' . $ext;
    $dest    = $dir . $newName;

    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return [
            'file_name' => $file['name'],
            'file_path' => 'uploads/' . $caseId . '/' . $newName,
            'file_size' => $file['size'],
            'mime_type' => mime_content_type($dest),
        ];
    }
    return false;
}

// ─── Pagination ───────────────────────────────────────────────────
function paginate(int $total, int $perPage, int $current): array
{
    $pages = (int)ceil($total / $perPage);
    return [
        'total'    => $total,
        'per_page' => $perPage,
        'current'  => $current,
        'pages'    => $pages,
        'offset'   => ($current - 1) * $perPage,
        'has_prev' => $current > 1,
        'has_next' => $current < $pages,
    ];
}

// ─── Status Badge Helper ──────────────────────────────────────────
function statusBadge(string $status): string
{
    $map = [
        'filed'               => 'bg-blue-100 text-blue-800',
        'under_investigation' => 'bg-yellow-100 text-yellow-800',
        'hearing_scheduled'   => 'bg-purple-100 text-purple-800',
        'in_progress'         => 'bg-orange-100 text-orange-800',
        'closed'              => 'bg-green-100 text-green-800',
        'dismissed'           => 'bg-red-100 text-red-800',
        'scheduled'           => 'bg-purple-100 text-purple-800',
        'completed'           => 'bg-green-100 text-green-800',
        'postponed'           => 'bg-yellow-100 text-yellow-800',
        'cancelled'           => 'bg-red-100 text-red-800',
    ];
    $class = $map[$status] ?? 'bg-gray-100 text-gray-800';
    $label = ucwords(str_replace('_', ' ', $status));
    return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium $class\">$label</span>";
}

// ─── Audit Logger ─────────────────────────────────────────────────
function auditLog(string $action, string $table = '', int $recordId = 0, string $desc = ''): void
{
    try {
        $db = Database::connect();
        $stmt = $db->prepare("INSERT INTO audit_logs (user_id, action, table_name, record_id, description, ip_address) VALUES (?,?,?,?,?,?)");
        $stmt->execute([
            auth()['id'] ?? null,
            $action,
            $table,
            $recordId ?: null,
            $desc,
            $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    } catch (Exception $e) { /* silently fail */
    }
}

// ─── Date Formatting ──────────────────────────────────────────────
function fDate(?string $date): string
{
    return $date ? date('d M Y', strtotime($date)) : '—';
}

function fDateTime(?string $dt): string
{
    return $dt ? date('d M Y, H:i', strtotime($dt)) : '—';
}

// ─── Notification Helpers ─────────────────────────────────────────

/**
 * Notify all active admins + lawyers about a case event.
 * Skips the user who triggered the action (they already know).
 */
function notifyCaseUpdate(int $caseId, string $caseNumber, string $title, string $message, string $type = 'case_update'): void
{
    try {
        $db   = Database::connect();
        $link = APP_URL . '/cases/' . $caseId;
        // Notify all admins and lawyers
        $stmt = $db->query("SELECT id FROM users WHERE role IN ('admin','lawyer') AND is_active = 1");
        $users = $stmt->fetchAll();
        foreach ($users as $u) {
            // Don't notify the person who made the change
            if ((int)$u['id'] === (int)(auth()['id'] ?? 0)) continue;
            NotificationModel::create((int)$u['id'], $title, $message, $type, $link);
        }
    } catch (Exception $e) { /* silently fail */
    }
}

/**
 * Notify a specific user (e.g. the assigned lawyer).
 */
function notifyUser(int $userId, string $title, string $message, string $type = 'system', string $link = ''): void
{
    try {
        if ($userId === (int)(auth()['id'] ?? 0)) return;
        NotificationModel::create($userId, $title, $message, $type, $link);
    } catch (Exception $e) { /* silently fail */
    }
}
