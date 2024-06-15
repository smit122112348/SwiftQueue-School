<?php
// session_start();

class User{
    private $conn;
    private $table_name = "users";
    
    public function __construct($db){
        $this->conn = $db;
    }

    public function getAllUsers(){
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users;
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

    public function deleteAccount($user_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $ans = $stmt->execute();
        return $ans;
    }

    public function makeAdmin($id) {

        $query = "UPDATE " . $this->table_name . " SET user_type = 'admin' WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $id);
        $ans = $stmt->execute();
        return $ans;
    }

    public function blockUser($id, $type) {
        // if user-type is blocked, then update it to basic
        // if user-type is basic, then update it to blocked
        
        if ($type === 'blocked') {
            $query = "UPDATE " . $this->table_name . " SET user_type = 'basic' WHERE user_id = :user_id";
        } else {
            $query = "UPDATE " . $this->table_name . " SET user_type = 'blocked' WHERE user_id = :user_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $id);
        $ans = $stmt->execute();
        return $ans;
    }
}