<?php

class AuthController {

    public static function showLogin(): void {
        AuthMiddleware::guest();
        view('auth.login', ['title' => 'Login']);
    }

    public static function login(): void {
        AuthMiddleware::guest();
        if (!verify_csrf()) { flash('error', 'Invalid request.', 'error'); redirect('/auth/login'); }

        $email    = input('email');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            flash('error', 'Email and password are required.', 'error');
            redirect('/auth/login');
        }

        $user = UserModel::findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            flash('error', 'Invalid credentials. Please try again.', 'error');
            auditLog('login_failed', 'users', 0, "Failed login for email: $email");
            redirect('/auth/login');
        }

        $_SESSION['user'] = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ];
        $_SESSION['last_activity'] = time();

        auditLog('login', 'users', $user['id'], 'User logged in');
        redirect('/dashboard');
    }

    public static function logout(): void {
        auditLog('logout', 'users', auth()['id'] ?? 0, 'User logged out');
        session_destroy();
        redirect('/auth/login');
    }
}
