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
}
?>