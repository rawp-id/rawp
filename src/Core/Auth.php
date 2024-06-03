<?php
// app/Auth.php
namespace Core;

use Core\Database;

class Auth
{
    protected static $user = null;

    public static function check()
    {
        return isset($_SESSION['user']);
    }

    public static function username()
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id()
    {
        return isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
    }

    public static function attempt($credentials)
    {
        $username = $credentials['username'];
        $password = $credentials['password'];

        // Query database untuk mencari pengguna dengan kredensial yang diberikan
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Jika kredensial benar, simpan pengguna di sesi
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username']
            ];
            return true;
        }
        return false;
    }

    public static function logout()
    {
        unset($_SESSION['user']);
    }

    public static function user()
    {
        $userId = $_SESSION['user']['id'] ?? null;
        if ($userId) {
            return User::find($userId);
        }
        return null;
    }

}
