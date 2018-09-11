<?php
namespace models;
// 父模型

use PDO;

class Base{
    public static $pdo = null;
    // 数据库链接
    public function __construct(){
        
       if(self::$pdo===null)
       { 
        $config = config('db');
        self::$pdo = new PDO('mysql:host='.$config['host'].';dbname='.$config['dbname'],$config['user'],$config['pass']);
        self::$pdo->exec('set names '.$config['chaeset']);
        }
    }

    public function startTrans(){
        self::$pdo->exec('start transaction');
    }
    public function commit(){
        self::$pdo->exec('commit');
    }
    public function rollback(){
        self::$pdo->exec('rollback');
    }











}