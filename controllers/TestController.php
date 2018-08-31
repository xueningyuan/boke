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
}