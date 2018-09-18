<?php
namespace controllers;

class CommentController 
{
    // 发表评论
    public function comments()
    {   
        $data = file_get_contents('php://input');

        $_POST = json_decode($data,true);
        // 1. 检查用户是否登录
        if(!isset($_SESSION['id']))
        {
            echo json_encode([
                'status_code' => '401',
                'message' => '必须先登录',
            ]);
            exit;
        }
        // 接收表单中的数据
        $content = e($_POST['content']);
        $blog_id = $_POST['blog_id'];

        // 插入到评论表中
        $model = new \models\Comment;
        $model->add($content,$blog_id);

        // 返回新发表的评论
        echo json_encode([
            'status_code'=>'200',
            'message'=>'发表成功',
            'data'=>[
                'content'=>$content,
                'avatar'=>$_SESSION['avatar'],
                'email'=>$_SESSION['email'],
                'created_at'=>date('Y-m-d H:i:s')
            ]
        ]);
        exit;

    }
    // 获取评论列表
    public function comment_list()
    {
        // 1. 接收日志ID
        $blogId = $_GET['id'];

        // 2. 获取日志的评论
        $model = new \models\Comment;
        $data = $model->getComments($blogId);

        // 3. 转成JSON
        echo json_encode([
            'status_code' => 200,
            'data' => $data,
        ]);
    }





}