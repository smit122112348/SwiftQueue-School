<?php
session_start();

class User{
    private $conn;
    private $table_name = "users";
    private $user_id;
    private $user_email;
    private $user_password;
    private $user_type;
    private $user_full_name;
    
    public function __construct($db){
        $this->conn = $db;
    }

    public function getUserID(){
        return $this->user_id;
    }

    public function getUserType(){
        return $this->user_type;
    }

    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && (crypt($password, $user['user_password']) === $user['user_password'])) {
            return $user;
        } else {
            return false;
        }
    }

    public function register($full_name, $email, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " (user_full_name, user_email, user_password) VALUES (:full_name, :email, :password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', crypt($password, 'some_salt_30'));
        $stmt->execute();

        $query = "SELECT * FROM " . $this->table_name . " WHERE user_email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user;
    }
}