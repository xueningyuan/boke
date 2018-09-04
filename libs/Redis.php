<?php
namespace libs;

class Redis
{
    private static $redis = null;
    private  function __clone(){}
    private  function __construct(){}
    
    public static function gitInstance(){
        if(self::$redis===null){
            self::$redis = new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
            ]);
        }
        return self::$redis;
    }
}