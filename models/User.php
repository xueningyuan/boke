<?php
namespace models;

use PDO;


class User extends Base{
    public function getActiveUsers(){
        $redis = \libs\Redis::gitInstance();
        $data = $redis->get('active_users');
        return json_decode($data,true);
    }
    public function computeActiveUsers(){
        echo "<pre>";
        // 日志分值
        $stmt = self::$pdo->query('select user_id,count(*)*5 fz from blogs where created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) group by user_id ');
        $data1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // 评论分值
        $stmt = self::$pdo->query('select user_id,count(*)*3 fz from comments where created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) group by user_id ');
        $data2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // 点赞分值
        $stmt = self::$pdo->query('select user_id,count(*) fz from blog_agrees where created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) group by user_id ');
        $data3 = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 合并数组
        // 第一个数组
        $arr = [];
        foreach($data1 as $v){
            $arr[$v['user_id']]=$v['fz'];
        }
        // 第二个数组
        foreach($data2 as $v){
            if(isset($arr[$v['user_id']]))
                $arr[$v['user_id']]+=$v['fz'];
            else
                $arr[$v['user_id']]=$v['fz'];
        }
        // 第三个数组
        foreach($data3 as $v){
            if(isset($arr[$v['user_id']]))
                $arr[$v['user_id']]+=$v['fz'];
            else
                $arr[$v['user_id']]=$v['fz'];
        }

        arsort($arr);//倒序排序
        var_dump($arr);
        // 截取前20并保存键（第四个参数保留键）
        $data = array_slice($arr,0,20,true);

        // 转换数组中的id为字符串，查询出用户的头像和email
        $userIds = array_keys($data);
        $userIds = implode(',',$userIds);
        var_dump($userIds);
        $stmt = self::$pdo->query("SELECT id,email,avatar FROM users WHERE id IN($userIds)");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // 把结果保存到redis
        $redis = \libs\Redis::gitInstance();
        $redis->set('active_users',json_encode($data));
        var_dump($data);


    }
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