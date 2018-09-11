<?php
namespace controllers;

// 引入模型类
use models\User;
use models\Order;

class UserController
{   
    public function money()
    {
        $user = new User;
        echo $user->getMoney();
    }
    public function charge(){
        view('users.charge');
    }
    public function docharge(){
        $money = $_POST['money'];
        $model = new Order;
        $model->create($money);
        message('充值订单已生成，请立即支付',2,'/user/orders');
    }

    public function orders(){
        $order = new Order;
        $data = $order->search();
        view('users.order',$data);
    }
    // 注册页面
    public function register(){
        view('users.add');
    }
    // 注册
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
            <a href='http://xue.tunnel.echomod.cn/user/active_user?code={$code}'>
            http://xue.tunnel.echomod.cn/user/active_user?code={$code}\r\n<br></a>如果不能点击，请复制地址",
            'from'=>$from,
        ];
         // 把消息转成字符串(JSON ==> 序列化)
         $message = json_encode($message);
         $redis = \libs\Redis::gitInstance();
        // 收入信息队列
        $redis->lpush('email',$message);
        message('邮件发送成功！', 1, '/user/login');

    }
    // 激活
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
    // 登陆页面
    public function login(){
        view('users.login');
    }
    // 登陆
    public function dologin(){
        $email = $_POST['email'];
        $password =md5($_POST['password']);

        $user = new User;
        if($user->login($email,$password)){
            message('登录成功！', 2, '/blog/index');
        }
        else
        {
            message('账号或者密码错误', 1, '/user/login');
        }

    }

    public function logout()
        {
            // 清空 SESSION
            $_SESSION = [];
    
            // 跳转
            message('退出成功', 2, '/');
        }







}