<?php
// 动态的修改 php.ini 配置文件
ini_set('session.save_handler', 'redis');   // 使用 redis 保存 SESSION
ini_set('session.save_path', 'tcp://127.0.0.1:6379?database=15');  // 设置 redis 服务器的地址、端口、使用的数据库
// ini_set('session.gc_maxlifetime',600);
session_start();

// 如果用户以POST方式访问网站时，需要验证 csrf令牌
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(!isset($_POST['_token']))
        die('违规操作');
    if($_POST['_token'] != $_SESSION['token'])
        die('违规操作');
}



// 定义常量
define('ROOT',dirname(__FILE__).'/../');

// 引入 composer 自动加载文件
require(ROOT.'vendor/autoload.php');
//自动加载类
function autoload($class){
    $path = str_replace('\\','/',$class);
    // echo ROOT.$path.'.php';
    require(ROOT.$path.'.php');
}
spl_autoload_register('autoload');
//添加路由：解析URL上的路径：控制器/方法
if(php_sapi_name() == 'cli'){
    $controller = ucfirst($argv[1]).'Controller';
    $action = $argv[2];
}else{
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
// 配置文件
function config($name){
    static $config = null;
    if($config === null){
        $config = require(ROOT.'config.php');
    }
    return $config[$name];
}
function redirect($url)
{
    header('Location:' . $url);
    exit;
}

// 跳回上一个页面
function back()
{
    redirect( $_SERVER['HTTP_REFERER'] );
}


// 提示消息的函数
// type 0:alert   1:显示单独的消息页面  2：在下一个页面显示
// 说明：$seconds 只有在 type=1时有效，代码几秒自动跳动
function message($message, $type, $url, $seconds = 5)
{
    if($type == 0)
    {
        echo "<script>alert('{$message}');location.href='{$url}';</script>";
        exit;

    }
    else if($type == 1)
    {
        // 加载消息页面
        view('common.success', [
            'message' => $message,
            'url' => $url,
            'seconds' => $seconds
        ]);
    }
    else if($type==2)
    {
        // 把消息保存到 SESSION
        $_SESSION['_MESS_'] = $message;
        // 跳转到下一个页面
        redirect($url);
    }
}
// xss
function e($content)
{
    return htmlspecialchars($content);
}
// htmlpurifer过滤
// 使用 htmlpurifer 过滤（因为性能慢，这个函数只用在，
// 使用在线编辑器填写内容的字段上，其它字段使用上面的 e 函数过滤）
function hpe($content)
{
    // 一直保存在内存中（直到脚本执行结束）
    static $purifier = null;
    // 只有第一次调用时才会创建新的对象
    if($purifier === null)
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Core.Encoding', 'utf-8');
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        $config->set('Cache.SerializerPath', ROOT.'cache');
        $config->set('HTML.Allowed', 'div,b,strong,i,em,a[href|title],ul,ol,ol[start],li,p[style],br,span[style],img[width|height|alt|src],*[style|class],pre,hr,code,h2,h3,h4,h5,h6,blockquote,del,table,thead,tbody,tr,th,td');
        $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,margin,width,height,font-family,text-decoration,padding-left,color,background-color,text-align');
        $config->set('AutoFormat.AutoParagraph', TRUE);
        $config->set('AutoFormat.RemoveEmpty', TRUE);
        $purifier = new \HTMLPurifier($config);
    }
    return $purifier->purify($content);
}

// 令牌
function csrf(){
    if(!isset($_SESSION['token']))
    {
        $token = md5(rand(1,99999).microtime());
        $_SESSION['token'] = $token;
    }
    return $_SESSION['token'];
}

