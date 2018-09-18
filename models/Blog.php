<?php
namespace models;

use PDO;

class Blog extends Base {
    // 取出点赞过这个日志的用户信息
    public function agreeList($id)
    {
        $sql = 'SELECT b.id,b.email,b.avatar
                 FROM blog_agrees a
                  LEFT JOIN users b ON a.user_id = b.id
                   WHERE a.blog_id=?';                   
    
       $stmt = self::$pdo->prepare($sql);
    
       $stmt->execute([
            $id
        ]);
    
       return $stmt->fetchAll( PDO::FETCH_ASSOC );
    }
    public function agree($id){
        $stmt = self::$pdo->prepare('select count(*) from blog_agrees where user_id=? and blog_id=?');
        $stmt->execute([
            $_SESSION['id'],
            $id
        ]);
        $count = $stmt->fetch(PDO::FETCH_COLUMN);
        if($count == 1){
            return FALSE;
        }
        // 点赞
        $stmt = self::$pdo->prepare("INSERT INTO blog_agrees(user_id,blog_id) VALUES(?,?)");
        $ret = $stmt->execute([
            $_SESSION['id'],
            $id
        ]);

        // 更新点赞数
        if($ret)
        {
            $stmt = self::$pdo->prepare('UPDATE blogs SET agree_count=agree_count+1 WHERE id=?');
            $stmt->execute([
                $id
            ]);
        }

        return $ret;
    }
    public function update($title,$content,$is_show,$id){
        $stmt = self::$pdo->prepare("update blogs set title=?,content=?,is_show=? where id=?");
        $ret = $stmt->execute([
            $title,
            $content,
            $is_show,
            $id,
        ]);
    } 
    // 单独生成静态页面
    public function makeHtml($id){
       $blog = $this->find($id);
       ob_start();
           view('blogs.content',[
               'blog'=>$blog,
           ]);
           // 取出缓存区内容
           $str = ob_get_clean();
           // 输出到文件中
           file_put_contents(ROOT.'public/contents/'.$id.'.html',$str);
    }
    // 删除一个静态页
    public function deleteHtml($id){
        @unlink(ROOT.'public/contents/'.$id.'.html');
    }

    public function find($id){
        $stmt = self::$pdo->prepare('select * from blogs where id =?');
        $stmt->execute([
            $id
        ]);
        return $stmt->fetch();
    }
    public function delete($id){
        $stmt = self::$pdo->prepare("delete from blogs where id = ? and user_id=? ");
        $stmt->execute([
            $id,
            $_SESSION['id']
        ]);
    }
    public function add($title,$content,$is_show)
    {
        $stmt = self::$pdo->prepare("INSERT INTO blogs(title,content,is_show,user_id) VALUES(?,?,?,?)");
        $ret = $stmt->execute([
            $title,
            $content,
            $is_show,
            $_SESSION['id'],
        ]);
        if(!$ret)
        {
            echo '失败';
            // 获取失败信息
            $error = $stmt->errorInfo();
            echo '<pre>';
            var_dump( $error); 
            exit;
        }
        // 返回新插入的记录的ID
        return self::$pdo->lastInsertId();
    }
    public function search(){
        
        $id = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

        $where = 'user_id ='.$id;

        $value = [];
        if(isset($_GET['keyword']) && $_GET['keyword'] ){
            $where .= " and (title like ? or content like ?) ";
            $value[] ='%'.$_GET['keyword'].'%';
            $value[] ='%'.$_GET['keyword'].'%';
        }

        if(isset($_GET['start_date']) && $_GET['start_date'] ){
            $where .= " and created_at >= ? ";
            $value[] = $_GET['start_date'];
        }

        if(isset($_GET['end_date']) && $_GET['end_date'] ){
            $where .= " and created_at <= ? ";
            $value[] = $_GET['end_date'];
        }

        if(isset($_GET['is_show'])&& ($_GET['is_show']==1 || $_GET['is_show']==='0') ){
            $where .= " and is_show = ? ";
            $value[] = $_GET['is_show'];
        }
        // 排序
        $odby = 'created_at';
        $odway = 'desc';

        if(isset($_GET['odby']) && $_GET['odby'] == 'display'){
            $odby = 'display';
        }

        if(isset($_GET['odway']) && $_GET['odway'] == 'asc'){
            $odway = 'asc';
        }

        // 翻页
        $perpage = 15;

        $page = isset($_GET['page']) ? max(1,(int)$_GET['page']):1;

        $offset = ($page-1)*$perpage;
        // 制作按钮
        // 总记录数
        $stmt = self::$pdo->prepare("select count(*) from blogs where $where");
        $stmt->execute($value);
        $count = $stmt->fetch(PDO::FETCH_COLUMN);
        $pageCount = ceil( $count / $perpage);
        $btns = '';
        for($i=1;$i<=$pageCount;$i++){
            $params = getUrlParams(['page']);// 先获取之前的参数
            
            $class = $page==$i ? 'active' : '';
            $btns .= "<a class='$class' href='?{$params}page=$i'>$i</a>";
            
        }

        // 执行sql 
        $stmt = self::$pdo->prepare("select * from blogs where $where ORDER BY $odby $odway limit $offset,$perpage");
        $stmt->execute($value);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'btns'=>$btns,
            'data'=>$data,
        ];
    } 
    public function content2html(){

        $stmt = self::$pdo->query('select * from blogs');
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // 开启缓冲区
        ob_start();
        foreach($blogs as $v){
            view('blogs.content',[
                'blog'=>$v,
            ]);
            // 取出缓存区内容
            $str = ob_get_contents();
            // 输出到文件中
            file_put_contents(ROOT.'public/contents/'.$v['id'].'.html',$str);
            // 清空缓存区
            ob_clean();
        }
    }
    public function index2html(){
        $stmt = self::$pdo->query("select * from blogs where is_show=1 order by id desc limit 20");
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        view('index.index',[
            'blogs'=>$blogs,
        ]);
        $str = ob_get_contents();
        file_put_contents(ROOT.'public/index.html',$str);
        ob_clean();
    }
    public function indexTop20(){
        $stmt = self::$pdo->query("select * from blogs where is_show=1 order by id desc limit 20");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        // 获取日志的浏览量
    // 参数：日志ID
    public function getDisplay($id)
    {
        // 使用日志ID拼出键名
        $key = "blog-{$id}";

        // 连接 Redis
        $redis = \libs\Redis::gitInstance();

        // 判断 hash 中是否有这个键，如果有就操作内存，如果没有就从数据库中取
        // hexists：判断有没有键
        if($redis->hexists('blog_displays', $key))
        {
            // 累加 并且 返回添加完之后的值
            // hincrby ：把值加1
            $newNum = $redis->hincrby('blog_displays', $key, 1);
            return $newNum;
        }
        else
        {
            // 从数据库中取出浏览量
            $stmt = self::$pdo->prepare('SELECT display FROM blogs WHERE id=?');
            $stmt->execute([$id]);
            $display = $stmt->fetch( PDO::FETCH_COLUMN );
            $display++;
            // 保存到 redis
            // hset：保存到  Redis
            $redis->hset('blog_displays', $key, $display);
            return $display;
        }
    }
    
    public function displayToDb(){
        $redis = \libs\Redis::gitInstance();
        $data = $redis->hgetall("blog_displays");
        foreach($data as $k => $v){
            $id = str_replace("blog-",'',$k);
            $sql = "update blogs set display={$v} where id ={$id}";
            self::$pdo->exec($sql);
        }
    }
}