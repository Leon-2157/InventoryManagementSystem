<?php

class AuthController
{
    public function loginForm(): void
    {
        if (isLoggedIn()) {
            redirect('/dashboard');
        }

        $pageTitle = 'Login';
        ob_start();
        include __DIR__ . '/../views/auth/login.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/auth.php';
    }

    public function login(): void
    {
        if (!validateCsrfToken()) {
            setFlash('error', 'Token keamanan tidak valid. Silakan coba lagi.');
            redirect('/login');
        }

        $username = inputString('username');
        $password = $_POST['password'] ?? '';
        $errors = [];

        if (empty($username)) {
            $errors[] = 'Username wajib diisi.';
        }
        if (empty($password)) {
            $errors[] = 'Password wajib diisi.';
        }

        if (!empty($errors)) {
            setFlash('error', implode(' ', $errors));
            redirect('/login');
        }

        $db = getDB();
        $stmt = $db->prepare('
            SELECT u.id, u.username, u.password_hash, u.role, up.full_name
            FROM users u
            LEFT JOIN user_profiles up ON up.user_id = u.id
            WHERE u.username = :username
            LIMIT 1
        ');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            setFlash('error', 'Username atau password salah.');
            redirect('/login');
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'        => $user['id'],
            'username'  => $user['username'],
            'role'      => $user['role'],
            'full_name' => $user['full_name'],
        ];

        setFlash('success', 'Selamat datang, ' . ($user['full_name'] ?? $user['username']) . '!');
        redirect('/dashboard');
    }

    public function logout(): void
    {
        session_destroy();
        // Start a new session briefly for flash
        session_start();
        setFlash('success', 'Anda telah logout.');
        redirect('/login');
    }
}
