<?php
namespace controllers;

use PDO;

class BlogController{
    public function index(){
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=bock",'root','123');
        $pdo->exec('set names utf8');

        $where = 1;

        $value = [];
        if(isset($_GET['keyword']) && $_GET['keyword'] ){
            $where .= " and (title like ? or content like ?) ";
            $value[] ='%'.$_GET['keyword'].'%';
            $value[] ='%'.$_GET['keyword'].'%';
        }

        if(isset($_GET['start_date']) && $_GET['start_date'] ){
            $where .= " and created_at >= ? ";
            $value[] = $_GET['start_date'];
        }

        if(isset($_GET['end_date']) && $_GET['end_date'] ){
            $where .= " and created_at <= ? ";
            $value[] = $_GET['end_date'];
        }

        if(isset($_GET['is_show'])&& ($_GET['is_show']==1 || $_GET['is_show']==='0') ){
            $where .= " and is_show = ? ";
            $value[] = $_GET['is_show'];
        }
        // 排序
        $odby = 'created_at';
        $odway = 'desc';

        if(isset($_GET['odby']) && $_GET['odby'] == 'display'){
            $odby = 'display';
        }

        if(isset($_GET['odway']) && $_GET['odway'] == 'asc'){
            $odway = 'asc';
        }

        // 翻页
        $perpage = 15;

        $page = isset($_GET['page']) ? max(1,(int)$_GET['page']):1;

        $offset = ($page-1)*$perpage;
        // 制作按钮
        // 总记录数
        $stmt = $pdo->prepare("select count(*) from blogs where $where");
        $stmt->execute($value);
        $count = $stmt->fetch(PDO::FETCH_COLUMN);
        $pageCount = ceil( $count / $perpage);
        $btns = '';
        for($i=1;$i<=$pageCount;$i++){
            $params = getUrlParams(['page']);// 先获取之前的参数
            
            $class = $page==$i ? 'active' : '';
            $btns .= "<a class='$class' href='?{$params}page=$i'>$i</a>";
            
        }

        // 执行sql 
        $stmt = $pdo->prepare("select * from blogs where $where ORDER BY $odby $odway limit $offset,$perpage");
        $stmt->execute($value);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        view('blogs.index',[
            'data'=>$data,
            'btns'=>$btns,
        ]);
    }
    public function content_to_html(){
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=bock",'root','123');
        $pdo->exec('set names utf8');

        $stmt = $pdo->query('select * from blogs');
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // 开启缓冲区
        ob_start();
        foreach($blogs as $v){
            view('blogs.content',[
                'blog'=>$v,
            ]);
            // 取出缓存区内容
            $str = ob_get_contents();
            // 输出到文件中
            file_put_contents(ROOT.'public/contents/'.$v['id'].'.html',$str);
            // 清空缓存区
            ob_clean();
        }
    }
}
?>