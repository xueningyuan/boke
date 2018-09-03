<?php
namespace controllers;

class TestController
{
    public function testRedis(){
        $client = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]);

        // $client->set('names','predis');
        echo $client->get('names');
    }

    public function re(){
        $redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]);

    }

    public function mail(){
        ini_set('default_socket_timeout',-1);
        echo "程序已启动";
        $redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]);

        while(true){
            echo "发送邮件";

            $email=$redis->brpop("email");
            var_dump($email);
            echo "发送完毕，继续监查";
        }

    }
}