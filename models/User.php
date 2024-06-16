<?php
// This is the model file for User which interacts with the users table in the database

class User{
    private $conn;
    private $table_name = "users";
    
    public function __construct($con,$db){
        // Set the database connection for the model
        $this->conn = $con;
        $this->conn->exec("USE $db;");
    }

    public function getAllUsers(){
        // Get all users from the database
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }

    public function login($email, $password) {
        // Check if the user exists in the database
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // If the user exists, check if the password is correct
        if ($user && (crypt($password, $user['user_password']) === $user['user_password'])) {
            return $user;
        } else {
            return false;
        }
    }

    public function register($full_name, $email, $password) {
        // Check if the user already exists in the database
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return false;
        }

        // Register the user
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
        // Delete the user from the database
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $ans = $stmt->execute();
        return $ans;
    }

    public function makeAdmin($id) {
        // Update the user-type to admin
        $query = "UPDATE " . $this->table_name . " SET user_type = 'admin' WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $id);
        $ans = $stmt->execute();
        return $ans;
    }

    public function userAccess($id, $type) {
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