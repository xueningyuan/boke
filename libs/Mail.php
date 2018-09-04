<?php
namespace libs;

class Mail{
    public $mailer;
    public function __construct(){
        $config = config('email');
         // 设置邮件服务器账号
         $transport = (new \Swift_SmtpTransport($config['host'], $config['prot']))  // 邮件服务器IP地址和端口号
         ->setUsername($config['name'])       // 发邮件账号
         ->setPassword($config['pass']);      // 授权码
         // 创建发邮件对象
         $this->mailer = new \Swift_Mailer($transport);
    }

    public function send($title,$content,$to){
        $config = config('email');
         // 创建邮件消息
         $message = new \Swift_Message();
         $message->setSubject($title)   // 标题
                 ->setFrom([ $config['from_email'] => $config['from_name']])   // 发件人
                 ->setTo([
                     $to[0], 
                     $to[0] => $to[1]
                 ])   // 收件人
                 ->setBody($content, 'text/html');     // 邮件内容及邮件内容类型
         // 发送邮件
         if($config['mode'] == 'debug'){
            // 把邮件内容发送到日志中
            // 读取邮件内容
            $mess = $message->toString();
            $log = new Log('email');
            $log->log( $mess );
         }else{
             $this->mailer->send($message);
         }
         

    }
}