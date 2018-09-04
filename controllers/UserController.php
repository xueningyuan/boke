<?php
namespace controllers;

// 引入模型类
use models\User;

class UserController
{
    public function register(){
        view('users.add');
    }
    public function store(){
        $email = $_POST['email'];
        $password = md5($_POST['password']);

        // 生成激活码
        $code = md5(rand(1,99999));
        $redis = \libs\Redis::gitInstance();
        // 序列化
        $values = json_encode([
            'email'=>$email,
            'password'=>$password,
        ]);
        $key = "temp_user:{$code}";
        $redis->setex($key,300,$values);


        // 从邮箱地址中取出姓名     
        $name = explode('@',$email);
         // 构造收件人地址
        $from = [$email,$name[0]];

        // 构造信息数组
        $message = [
            'title'=>'欢迎加入全栈一班',
            'content'=>"点击以下链接进行激活：\r\n <br>
            <a href='http://localhost:9999/user/active_user?code={$code}'>
            http://localhost:9999/user/active_user?code={$code}</a>\r\n<br>如果不能点击，请复制地址",
            'from'=>$from,
        ];
         // 把消息转成字符串(JSON ==> 序列化)
         $message = json_encode($message);
         $redis = \libs\Redis::gitInstance();
        // 收入信息队列
        $redis->lpush('email',$message);
        echo "ok";
    }

    public function active_user(){
        $code = $_GET['code'];
        $redis = \libs\redis::gitInstance();
        $key = 'temp_user:'.$code;
        $data = $redis->get($key);
        if($data){
            $redis->del($key);
            $data = json_decode($data,true);

            $user = new User;
            $user->add($data['email'],$data['password']);
            // die("激活成功！");
            header('Location:/user/login');

        }
        else
        {
            die('激活码无效！');
        }
    }

    public function login(){
        view('users.login');
    }







}