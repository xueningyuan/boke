<?php
namespace controllers;

// 引入模型类
use models\User;
use models\Order;
use Intervention\Image\ImageManagerStatic as Image;

class UserController
{   
    public function setActiveUsers(){
        $user = new User;
        $user->computeActiveUsers();
    }
    public function uploadbig(){
        $count = $_POST['count'];
        $i = $_POST['i'];
        $size = $_POST['size'];
        $name = 'big_img_'.$_POST['img_name'];

        $img = $_FILES['img'];
        $url = ROOT.'tmp/'.$_SESSION['id'].'/';
        if(!is_dir($url))
        {
            // 创建目录（第二个参数：有写的权限（只对 Linux 系统)）
            mkdir($url, 0777);
        }
        move_uploaded_file($img['tmp_name'],$url.$i);
        $redis = \libs\Redis::gitInstance();
        $uploadedCount = $redis->incr($name);
        if($uploadedCount == $count){
             // 以追回的方式创建并打开最终的大文件
             $fp = fopen(ROOT.'public/uploads/big/'.$name.'.png', 'a');
             // 循环所有的分片
             for($i=0; $i<$count; $i++)
             {
                 // 读取第 i 号文件并写到大文件中
                 fwrite($fp, file_get_contents($url.$i));
                 // 删除第 i 号临时文件
                 unlink($url.$i);
            }
            rmdir(ROOT.'tmp/'.$_SESSION['id']);            
            fclose($fp);
            $redis->del($name);
        }
    }

    public function uploadall(){
        $root= ROOT.'public/uploads/';
        $date = date('Y-m-d');
        if(!is_dir($root .$date)){
            mkdir($root.$date,0777);
        }
        foreach($_FILES['images']['name'] as $k=>$v){
            $name = md5(time().rand(1,9999));
            $ext = strrchr($v,'.');
            $name = $name.$ext;
            move_uploaded_file($_FILES['images']['tmp_name'][$k],$root.$date.'/'.$name);
            echo $root.$date.'/'.$name.'<hr>';
        }
     
    }
    public function album(){
        view('users.album');
    }
    public function setavatar(){
        $upload = \libs\Uploader::make();
        $path = $upload->upload('avatar', 'avatar');


        // 裁切图片
        $image = Image::make(ROOT . 'public/uploads/'.$path);
        // 注意：Crop 参数必须是整数，所以需要转成整数：(int)
        $image->crop((int)$_POST['w'], (int)$_POST['h'], (int)$_POST['x'], (int)$_POST['y']);
        // 保存时覆盖原图
        $image->save(ROOT . 'public/uploads/'.$path);

        $model =new \models\User;
        $model->setAvatar('/uploads/'.$path);
        // echo $path;
        @unlink(ROOT.'public'.$_SESSION['avatar']);

        $_SESSION['avatar'] = '/uploads/'.$path;
        message('设置成功',2,'/blog/index');
    }
    public function avatar(){
        view('users.avatar');
    }
    public function test()
    {
        sleep(100);
    }
    public function orderStatus()
    {
        $sn = $_GET['sn'];
        // 获取的次数
        $try = 10;
        $model = new Order;
        do
        {
            $info = $model->findBySn($sn);
            if($info['status'] == 0)
            {
                sleep(1);
                $try--;
            }
            else
                break;
        }while($try>0);
        echo $info['status'];
    }

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