<?php
namespace controllers;

use models\Blog;

class BlogController{

    public function delete(){
        $id = $_POST['id'];
        $blog = new Blog;
        $blog->delete($id);
        message('删除成功', 2, '/blog/index');

    }
        // 显示添加日志的表单
    public function create()
    {
        view('blogs.create');
    }
    public function store()
    {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];
        $blog = new Blog;
        $blog->add($title,$content,$is_show);
        // 跳转
        message('发表成功', 2, '/blog/index');
    }
    public function index(){
        $blog = new Blog;

        $data = $blog->search();

        view('blogs.index',$data
        );
    }
    public function content_to_html(){
            $blog = new Blog;
            $blog->content2html();
        }
    public function index2html(){
        $blog = new Blog;
        $blog->index2html();
    }

    public function update_display(){
        $id = (int)$_GET['id'];
        $redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]);
        $blog = new Blog;
        $key = "blog-{$id}";
        if($redis->hexists('blog_displays', $key)){
            $newNum = $redis->hincrby('blog_displays',$key,1);
            echo $newNum;
            
        }else{
            $display = $blog->getDisplay($id);
            $display++;
            $redis->hset('blog_displays',$key,$display);
            echo $display;
        }
    }

    public function displayToDb(){
        $blog = new Blog;
        $blog->displayToDb();
    }
}
?>