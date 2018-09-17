<?php
namespace controllers;

class RedbagController{

    // 队列输入
    public function rob(){
        // 判断今天是否已经抢过
        $key = 'redbag_'.date('Ymd');
        $redis = \libs\Redis::gitInstance();
        $exists = $redis->sismember($key,$_SESSION['id']);
        if($exists){
            echo json_encode([
                'status_code' => '403',
                'message' => '今天抢过了'
            ]);
            exit;
        }
        // 减少库存量（-1），并返回 减完之后的值
        $stock = $redis->decr('redbag_stock');
        if($stock < 0)
        {
            echo json_encode([
                'status_code' => '403',
                'message' => '今天的红包已经减完了~'
            ]);
            exit;
        }

        // 下单（放到队列）
        $redis->lpush('redbag_orders', $_SESSION['id']);

        // 把ID放到集合中（代表已经抢过了）
        $redis->sadd($key, $_SESSION['id']);

        echo json_encode([
            'status_code' => '200',
            'message' => '恭喜你~抢到了本站的红包~'
        ]); 
    }
    
    public function rob_view()
    {
        // 显示一个磁面
        view('redbag/rob');
    }
    // 初始化【任务调度-每天8:59执行】
    public function init(){
        $redis = \libs\Redis::gitInstance();

        $redis->set('redbag_stock',20);
        $key = 'redbag_'.date('Ymd');
        $redis->sadd($key,'-1');
        // 过期时间
        $redis->expire($key,3900);
    }
    // 监听队列，生成订单
    public function makeOrder(){
        $redis = \libs\Redis::gitInstance();
        $model = new \models\Redbag;
        
        ini_set('default_socket_timeout',-1);
        echo "开始监听红包队列... \r\n";

        while(true)
        {
            $data = $redis->brpop('redbag_orders',0);

            $userId = $data[1];
            $model->create($userId);

            echo "======有人抢了红包！\r\n";

        }
    }





}