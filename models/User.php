<?php
namespace models;

use PDO;


class User extends Base{
    public function setAvatar($path){
        $stmt = self::$pdo->prepare('update users set avatar=? where id =?');
        $stmt->execute([
            $path,
            $_SESSION['id']
        ]);
    }
    public function add($email,$password){
        $stmt = self::$pdo->prepare("insert into users (email,password) values(?,?)");
        return $stmt->execute([
            $email,
            $password,
        ]);
    }
    public function login($email,$password){
        $stmt = self::$pdo->prepare("select * from users where email=? and password=? ");
        $stmt->execute([
            $email,
            $password,
        ]);
        $user = $stmt->fetch();
        if($user){
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['money'] = $user['money'];
            $_SESSION['avatar']= $user['avatar'];
            return true;
        }
        else
        {
            return false;
        }
    }

    // 为用户增加金额
    public function addMoney($money, $userId)
    {
        $stmt = self::$pdo->prepare("UPDATE users SET money=money+? WHERE id=?");
        return $stmt->execute([
            $money,
            $userId
        ]);

    }
    public function abbMoney($money, $userId)
    {
        $stmt = self::$pdo->prepare("UPDATE users SET money=money-? WHERE id=?");
        return $stmt->execute([
            $money,
            $userId
        ]);

    }
    // 获取余额
    public function getMoney(){
        $id = $_SESSION['id'];
        $stmt = self::$pdo->prepare('select money from users where id = ?');
        $stmt->execute([$id]);
        $money = $stmt->fetch(PDO::FETCH_COLUMN);
        $_SESSION['money']=$money;
        return $money;

    }

    public function getAll()
    {
        $stmt = self::$pdo->query('SELECT * FROM users');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    









}