<?php
require_once 'config.php';

class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function register($email, $password, $role = 'moderator') {
        $password = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (email, password, role) VALUES ('{$this->db->escape($email)}', '$password', '$role')";
        return $this->db->query($sql);
    }

    public function login($email, $password) {
        $sql = "SELECT * FROM users WHERE email = '{$this->db->escape($email)}'";
        $result = $this->db->query($sql);
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                return true;
            }
        }
        return false;
    }

    public function requestPasswordReset($email) {
        $token = bin2hex(random_bytes(16));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $sql = "UPDATE users SET reset_token = '{$this->db->escape($token)}', reset_expiry = '$expiry' WHERE email = '{$this->db->escape($email)}'";
        return $this->db->query($sql) ? $token : false;
    }

    public function resetPassword($token, $password) {
        $sql = "SELECT * FROM users WHERE reset_token = '{$this->db->escape($token)}' AND reset_expiry > NOW()";
        $result = $this->db->query($sql);
        if ($result->num_rows > 0) {
            $password = password_hash($password, PASSWORD_BCRYPT);
            $sql = "UPDATE users SET password = '$password', reset_token = NULL, reset_expiry = NULL WHERE reset_token = '{$this->db->escape($token)}'";
            return $this->db->query($sql);
}
     return false;
    }
}
?>