<?php
// 定义常量
define('ROOT',dirname(__FILE__).'/../');
//自动加载类
function autoload($class){
    $path = str_replace('\\','/',$class);
    // echo ROOT.$path.'.php';
    require(ROOT.$path.'.php');
}
spl_autoload_register('autoload');
//添加路由：解析URL上的路径：控制器/方法
// 获取URL上的路径
if(isset($_SERVER['PATH_INFO'])){
    $pathInfo = $_SERVER['PATH_INFO'];
    $pathInfo = explode('/',$pathInfo);
    $controller = ucfirst($pathInfo[1]).'Controller';
    $action = $pathInfo[2];
}else{
    //默认控制器
    $controller = 'IndexController';
    $action = 'index';
}

$fullController = 'controllers\\'.$controller;
$_C = new $fullController;
$_C->$action();


// 加载视图
// 参数一，加载的视图的文件名
// 参数二，向视图中传的数据
function view($viewFileName,$data = []){
    extract($data);
    $path = str_replace('.','/',$viewFileName).'.html';
    require(ROOT.'views/'.$path);
}

// 获取当前 URL 上所有的参数，并且还能排除掉某些参数
// 参数：要排除的变量
function getUrlParams($except = [])
{
    // ['odby','odway']
    // 循环删除变量
    foreach($except as $v)
    {
        unset($_GET[$v]);
    }
    $str = '';
    foreach($_GET as $k => $v)
    {
        $str .= "$k=$v&";
    }

    return $str;

}