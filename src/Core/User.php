<?php
// Core/User.php
namespace Core;

use Core\Database;

class User
{
    protected $id;
    protected $username;
    protected $email;

    public function __construct($id, $username, $email)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
    }

    public static function find($id)
    {
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        if ($user) {
            return new self($user['id'], $user['username'], $user['email']);
        }
        return null;
    }

    public function id()
    {
        return $this->id;
    }

    public function username()
    {
        return $this->username;
    }

    public function email()
    {
        return $this->email;
    }
}
