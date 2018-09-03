<?php
namespace controllers;

use models\Blog;

class BlogController{
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