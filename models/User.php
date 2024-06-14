<?php

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

    public function comparePassword($password){
        return password_verify($password, $this->user_password);
    }
}