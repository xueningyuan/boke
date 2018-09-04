<?php
namespace controllers;
use libs\Mail;

class MailController{
    public function send(){
        $redis = \libs\Redis::gitInstance();

        $mailer = new Mail;
        // 设置 PHP 永不超时
        ini_set('default_socket_timeout', -1);
        echo "发邮件队列启动成功..\r\n";

        
        while(true){
            // 导出信息队列冰转回数组
            $data = $redis->brpop('email',0);
            $message = json_decode($data[1],TRUE);
            // 发送邮件
            $mailer->send($message['title'],$message['content'],$message['from']);
            echo "发送邮件成功！继续等待下一个。\r\n";
        }
    }
}