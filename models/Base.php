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
        self::$pdo = new PDO("mysql:host=127.0.0.1;dbname=bock",'root','123');
        self::$pdo->exec('set names utf8');
        }
    }









}