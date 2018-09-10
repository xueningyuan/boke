<?php
namespace controllers;

class TestController
{   
    public function testaddMondey(){
        $user = new \models\User;
        $user->addMoney(2,21);

        // $dara = $user->getMoney();
        // echo $dara;
    }
    public function testLog(){
        $log = new \libs\Log('email');
        $log->log( "完成" );
    }


    public function testRedis(){
        $client = \libs\Redis::gitInstance();

        // $client->set('names','predis');
        // echo $client->get('names');
    }

    public function register(){
        $redis = \libs\Redis::gitInstance();

        $data = [
            'email'=>'xueninyuan@126.com',
            'title'=>'标题',
            'content'=>'内容'
        ];
        $data = json_encode($data);
        $redis->lpush('email',$data);
        echo '注册成功';

    }

    public function mail(){
        ini_set('default_socket_timeout',-1);
        echo "程序已启动";
         $redis = \libs\Redis::gitInstance();

        while(true){
            $data = $redis->brpop("email",0);
            echo "发送邮件";
            var_dump($data);
            self::testMail();
            echo "发送完毕，继续监查";
        }

    }


    public function testMail()
    {
        // 设置邮件服务器账号
        $transport = (new \Swift_SmtpTransport('smtp.126.com', 25))  // 邮件服务器IP地址和端口号
        ->setUsername('xueninyuan@126.com')       // 发邮件账号
        ->setPassword('2298593298a');      // 授权码

        // 创建发邮件对象
        $mailer = new \Swift_Mailer($transport);

        // 创建邮件消息
        $message = new \Swift_Message();

        $message->setSubject('测试标题')   // 标题
                ->setFrom(['xueninyuan@126.com' => '全栈1班'])   // 发件人
                ->setTo(['xueninyuan@126.com', 'xueninyuan@126.com' => '你好'])   // 收件人
                ->setBody('Hello <a href="http://localhost:9999">点击激活</a> World ~', 'text/html');     // 邮件内容及邮件内容类型

        // 发送邮件
        $ret = $mailer->send($message);

        var_dump("ok");
    }
}