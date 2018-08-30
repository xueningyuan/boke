<?php
$host = '127.0.0.1';
$dbname = 'bock';
$user = 'root';
$pass = '123';

$pdo = new PDO("mysql:host={$host};dbname={$dbname}",$user,$pass);
$pdo->exec('SET NAMES utf8');

// $res = $pdo->prepare('insert into blogs(title,content) values(?,?)');
// $res->execute([
//     '标题？',
//     '内容'
// ]);
// echo $pdo->lastInsertId();


// $res = $pdo->prepare('select * from blogs where id = :id');
// $data = $res->execute([':id'=>'2']);
// $data=$res->fetch(PDO::FETCH_ASSOC);
// var_dump($data);


// $res = $pdo->query('select * from blogs limit 10');
// $data=$res->fetchAll(PDO::FETCH_ASSOC);
// for ($i=0; $i < count($data); $i++) { 
//     echo $data[$i]['title'];
// }



for ($i=0; $i < 100; $i++) { 
     $title = getChar(rand(20,100) ); 
     $content = getChar(rand(20,100)); 
    $pdo->exec("insert into blogs(title,content) values('{$title}','{$content}')");
}

// $res = $pdo->exec("insert into blogs(title,content) values('标题','hehe')");
// $res = $pdo->exec("update blogs set title='标题2' where id =1");
// $res = $pdo->exec("delete from blogs");
// $res = $pdo->exec("truncate blogs");
// if($res=false){
//     echo '出错了';
// }

 function getChar($num)  // $num为生成汉字的数量
    {
        $b = '';
        for ($i=0; $i<$num; $i++) {
            // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
            $a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
            // 转码
            $b .= iconv('GB2312', 'UTF-8', $a);
        }
        return $b;
    }

?>