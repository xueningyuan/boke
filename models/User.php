<?php
namespace models;
use PDO;

class User{
    public $pdo;
    public function __construct(){
        $this->pdo = new PDO("mysql:host=127.0.0.1;dbname=bock",'root','123');
        $this->pdo->exec('set names utf8');
    }
    public function getName(){
        return 'Toms';
    }
    public function add($email,$password){
        $stmt = $this->pdo->prepare("insert into users (email,password) values(?,?)");
        return $stmt->execute([
            $email,
            $password,
        ]);
    }
}