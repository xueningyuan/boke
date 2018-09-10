<?php
namespace models;

use PDO;


class User extends Base{
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
        $stmt->execute([
            $money,
            $userId
        ]);

        // // 更新 Redis
        // $redis = \libs\Redis::gitInstance();

        // // 拼出 redis 中的键
        // $key = 'user_money:'.$userId;

        // // 增加余额
        // $ret = $redis->incrby($key, $money);

        // echo $ret;
    }
    // 获取余额
    public function getMoney(){
        $id = $_SESSION['id'];
        // $redis = \libs\Redis::gitInstance();
        // $key = 'user_money:'.$id;

        // $money = $redis->get($key);
        // if($money){
        //     return $money;
        // }else{
            $stmt = self::$pdo->prepare('select money from users where id = ?');
            $stmt->execute([$id]);
            $money = $stmt->fetch(PDO::FETCH_COLUMN);
            // $redis->set($key,$money);
            return $money;
        // }
    }












}