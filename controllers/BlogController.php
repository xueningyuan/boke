<?php
namespace controllers;

use models\Blog;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BlogController{

        // 获取最新的10个日志
    public function makeExcel()
    {
            // 获取当前标签页
            $spreadsheet = new Spreadsheet();
            // 获取当前工作
            $sheet = $spreadsheet->getActiveSheet();
    
            // 设置第1行内容
            $sheet->setCellValue('A1', '标题');
            $sheet->setCellValue('B1', '内容');
            $sheet->setCellValue('C1', '发表时间');
            $sheet->setCellValue('D1', '是发公开');
    
            // 取出数据库中的日志
            $model = new \models\Blog;
            // 获取最新的20个日志
            $blogs = $model->indexTop20();
    
            $i=2; // 第几行
            foreach($blogs as $v)
            {
                $sheet->setCellValue('A'.$i, $v['title']);
                $sheet->setCellValue('B'.$i, $v['content']);
                $sheet->setCellValue('C'.$i, $v['created_at']);
                $sheet->setCellValue('D'.$i, $v['is_show']);
                $i++;
            }
    
            $date = date('Ymd');
    
            // 生成 excel 文件
            $writer = new Xlsx($spreadsheet);
            $writer->save(ROOT . 'excel/'.$date.'.xlsx');
    
            // 调用 header 函数设置协议头，告诉浏览器开始下载文件
    
            // 下载文件路径
            $file = ROOT . 'excel/'.$date.'.xlsx';
            // 下载时文件名
            $fileName = '最新的20条日志-'.$date.'.xlsx';
    
            // 告诉浏览器这是一个二进程文件流    
            Header ( "Content-Type: application/octet-stream" ); 
            // 请求范围的度量单位  
            Header ( "Accept-Ranges: bytes" );  
            // 告诉浏览器文件尺寸    
            Header ( "Accept-Length: " . filesize ( $file ) );  
            // 开始下载，下载时的文件名
            Header ( "Content-Disposition: attachment; filename=" . $fileName );    
    
            // 读取服务器上的一个文件并以文件流的形式输出给浏览器
            readfile($file);
    
    
    }

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