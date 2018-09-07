<?php
namespace controllers;

use models\Blog;

class BlogController{

    public function content(){
        $id = $_GET['id'];
        $blog = new Blog;
        $data = $blog->find($id);
        if($_SESSION['id']!=$data['user_id']){
            die('无权访问');
        }
        view('blogs.content',[
            'blog'=>$data
        ]);
    }
    public function update(){
        $id = $_POST['id'];
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];

        $blog = new Blog;
        $data = $blog->update($title,$content,$is_show,$id);
        if($is_show == 1){
            $blog->makeHtml($id);
        }else{
            $blog->deleteHtml($id);
        }
        message('修改成功', 2, '/blog/index');
    }

    public function edit(){
        $id = $_GET['id'];
        $blog = new Blog;
        $data = $blog->find($id);
        view('blogs.edit',[
            'data'=>$data
        ]);
    }
    public function delete(){
        $id = $_POST['id'];
        $blog = new Blog;
        $blog->delete($id);
        $blog->deleteHtml($id);
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
        $id = $blog->add($title,$content,$is_show);
        if($is_show == 1){
            $blog->makeHtml($id);
        }
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

        $blog = new Blog;

        $display = $blog->getDisplay($id);

        echo json_encode([
            'display' => $display,
            'email' => isset($_SESSION['email']) ? $_SESSION['email'] : ''
        ]);
    }

    public function displayToDb(){
        $blog = new Blog;
        $blog->displayToDb();
    }
}
?>