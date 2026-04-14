<?php

class AuthMiddleware {

    public static function handle(): void {
        if (!isLoggedIn()) {
            flash('error', 'Please log in to continue.', 'error');
            redirect('/auth/login');
        }
    }

    public static function requireRole(string|array $roles): void {
        self::handle();
        if (!hasRole($roles)) {
            http_response_code(403);
            view('errors.403');
            exit;
        }
    }

    public static function guest(): void {
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
    }
}
