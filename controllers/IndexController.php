<?php
namespace controllers;

use models\Blog;

class IndexController{
    public function index(){
        $blog = new Blog;
        $blogs = $blog->indexTop20();
        view('index.index',[
            'blogs'=>$blogs,
        ]);
    }
}