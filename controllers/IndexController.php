<?php
namespace controllers;

use models\Blog;

class IndexController{
    public function index(){
        $blog = new Blog;
        $blogs = $blog->indexTop20();

        // 取出活跃用户
        $user = new \models\User;
        $users = $user->getActiveUsers();

        view('index.index',[
            'blogs'=>$blogs,
            'users'=>$users
        ]);
    }
}