<?php
namespace libs;

class Redis
{
    private static $redis = null;
    private  function __clone(){}
    private  function __construct(){}
    
    public static function gitInstance(){
        $config = config('redis');
        if(self::$redis===null){
            self::$redis = new \Predis\Client($config);
        }
        return self::$redis;
    }
}