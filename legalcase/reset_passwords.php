<?php

// ─── One-time password reset script ───────────────────────────────
// 1. Place this file in your project root (same folder as router.php)
// 2. Run: php reset_passwords.php
// 3. DELETE this file immediately after running it
// ──────────────────────────────────────────────────────────────────

define('BASE_PATH', __DIR__);
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

$db = Database::connect();

$users = [
    'admin@legalcase.ug'  => 'Admin@1234',
    'lawyer@legalcase.ug' => 'Lawyer@1234',
    'clerk@legalcase.ug'  => 'Clerk@1234',
];

foreach ($users as $email => $newPassword) {
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$hash, $email]);
    $rows = $stmt->rowCount();
    if ($rows) {
        echo "✅ Password updated for: $email  →  new password: $newPassword\n";
    } else {
        echo "❌ User not found: $email\n";
    }
}

echo "\n✅ Done. DELETE this file now!\n";
