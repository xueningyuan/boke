<?php
/* 支付宝支付的控制器 */
namespace controllers;

use Yansongda\Pay\Pay;

class AlipayController
{
    // 配置
    // qjssfp4397@sandbox.com
    public $config = [
        'app_id' => '2016091700531214',
        // 支付宝公钥
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA3MB5RPixTIK2AlHUGigJBwgwXO5WHJtoVtyOyLsakCVOy+nY9Ij0efgN8cV/NCDGCKL7u7nbaWfG2yyRexFwYbyhNHkzGh/Qpnl/2Vu5nMmDyOluP8fFs1GzblTupEilQ/zfb2veLUIzSp4QWn7EQB6a08ub+7s/oBttoQpSSxdVK240nPk9Kaugk0uIyMufeGvXcK9Bg+bBSEu7b39E0OR/ieCbDvb6k0A/BG4j6ubYiBL+TRbGE28y/TbUkzdqIR4xaWlVerFW5IwJvmrPoCYIVp9EC8RMoMdrcT2CW4rBgh2Ybr6+VHQVJZwhBBJgSQg0y8vZqugEzpP4yl/d6wIDAQAB',
        // 商户应用密钥
        'private_key' => 'MIIEowIBAAKCAQEAuL68qz087qVXPzGENUZyXtgi/dhEZuUIgRm+HjXKOZhhV3XXyqNNauZwozNdAVGgY43FlIWYhIrbcSdLJsWfDR7gSiP84W10Al1rKiRWmfIYc6y+FEK7EDvRMvTG7a/IDk16HlXfSbG4BHv2+0DqtqnwWOlc7sToJLoLc/FdF3EHmADVziNkTyLVhVKZDwtrgAsQJ/izzIPL1YZa/h3wY8dmYgT+7l9K4TvBHL2qtdFScKzYtEXYPGbtnW1ymMYMdH2B0UiKl53J9Zusi9YayTHUazDg712/uQ40lbzdf4OGVxiJ6xJyASVYRT1vMVL40uuoDZ8CLTHAEI4ZutHdawIDAQABAoIBAG3p1PgQPANDzY+kVyMXIY56Cv95vuB03UVp2mnA2OrBVRCi5NsDo1i5d0Qxl+Dj/oecXnPJs/8PWhWNKjIMG1/EAe86UAaShxWtHMf6zKdUmOWhXYlSVlcHL6SgawYYse6Ie+I2dt0yZegNFNlROxOoMX3EgWzxK6hGI/A8JoV9z7tFNS8b3FC9uwJKI4iSkxhcwv57rWIQCrtNp3qRy9GOTmz4nlEmn0UCm+yvCkfnli3zGedllQvrlARTNiFEEZlHMOnDMnWr0s6VNIYbxYopTuAmp4CRmcs9J/3G3SXMovRgRFrnWFNHJwWcUHQWXNpJ7vSnG+jmyEbeqgPaUkkCgYEA5cbiFyK2BFi6j7OcASLB31vnhYxdBOX6W99MuRQCVyV0A4c/UXKZOkxCxKMEFnEALnNqFbIXICCOZDICblRZFWcVqPpAh6OJE1V9MvOrijUWxVugVuPlo+oD9tgeVfF1Z21gMN6v6wHlbmqmh3UwoCdXH51HPIkjfD4hS3aK5+8CgYEAzdQ2gLbiSucTiC2BlXTK1vn7YJEL1NagJjAybnH0h9eec3NdCHBCDBEzkljPzCYuwlk13irOrlRK/TLGYyuPctTo7rDOODHk87I4aj+dJMcxmtwHEGVENiu2ymjU8iMEbnfuEBwcivwguz1SLraRVqnXeyBhOQttRKvVLG1qRkUCgYEAx93dX95zkQhVDxZeRqajGNRqaMIVT2N0CGIN7Jc5CfCsHz2PmBskqY8YLM7XiWW1kLIXvtNwRiPLd+AAOVPpQTKvppI3e0SGwWiFRMKrncZcDkOLDsmhQJkX5alLidpEEzkSiK/LOZImrYrbW8xWBZjysa8u/bsUQMgSUf/FB8UCgYAtKAOVFYpr+Go2lBU73tWpeEqIEwpPdY7JEgXeaS3Gp61hksu0UDyNPTDdSJK+LRpRFRVWWnnhkSiqh/syQppEDSXVSADWH0wlktIBrVcifHkLin4aQL1ITSrbGUiunrQYMEOQUTqJ22qq6XcgPHmCU9ysbJwn0bP1PuWTy6VnKQKBgF6/DchDlbIRrmkdhAmNw/r/N1Pr8ZJdupznBX+dZu89DVKMtzUeyzi6j7IZgDwecnGimIs0GDUXkBomoqfK3aHoqciSwxve3a+wBVABhX6odmRmCBh+HxZw8JP4UbDkchVBHamS/EjmnnZEFxZ/SBltRUBK09y6QbrU7sBwvcgK',
        
        // 通知地址
        'notify_url' => 'http://a2a2a0de.ngrok.io/alipay/notify',
        // 跳回地址
        'return_url' => 'http://a2a2a0de.ngrok.io/alipay/return',
        
        // 沙箱模式（可选）
        'mode' => 'dev',
    ];

    // 跳转到支付宝
    public function pay()
    {   
        $sn = $_POST['sn'];
        $order = new \models\Order;
        $data = $order->findBySn($sn); 

        // 跳转到支付宝
        if($data['status'] == 0){
            $alipay = Pay::alipay($this->config)->web([
                'out_trade_no' => $sn,    // 本地订单ID
                'total_amount' => $data['money'],    // 支付金额（单位：元）
                'subject' => '知鸟系统用户充值 ：'.$data['money'].'元', // 支付标题
            ]);
            $alipay->send();
        }else{
            die('订单状态不允许支付~');
        }
        
    }
    // 支付完成跳回
    public function return()
    {
        // 验证数据是否是支付宝发过来
        $data = Pay::alipay($this->config)->verify();


        echo '<h1>支付成功！</h1> <hr>';

        var_dump( $data->all() );
        sleep(3);
        echo '<script>window.close();</script>';

    }
    // 接收支付完成的通知
    public function notify()
    {
        $alipay = Pay::alipay($this->config);
        try{
            $data = $alipay->verify(); // 是的，验签就这么简单！
            
            if($data->trade_status == 'TRADE_SUCCESS' || $data->trade_status == 'TRADE_FINISHED'){
                $order = new \models\Order;
                $orderInfo = $order->findBySn($data->out_trade_no);
                if($orderInfo['status']==0){
                    $order->startTrans();
                    $ret1 = $order->setPaid($data->out_trade_no);
                    
                    $user = new \models\User;
                    $ret2 = $user->addMoney($orderInfo['money'],$orderInfo['user_id']);
                    
                    if($ret1 && $ret2){
                        $order->commit();
                    }else{
                        $order->rollback();
                    }
                }
            }
        } catch (\Exception $e) {
            die("0");
        }

        // 回应支付宝服务器（如何不回应，支付宝会一直重复给你通知）
        $alipay->success()->send();
    }

    // 退款
    public function refund()
    {
        $sn = $_POST['sn'];
        $money = $_POST['money'];
        // 生成唯一退款订单号（以后使用这个订单号，可以到支付宝中查看退款的流程）
        $refundNo = md5( rand(1,99999) . microtime() );

        try{
            $order = [
                'out_trade_no' => $sn,    // 退款的本地订单号
                'refund_amount' => $money,              // 退款金额，单位元
                'out_request_no' => $refundNo,     // 生成 的退款订单号
            ];

            // 退款
            $ret = Pay::alipay($this->config)->refund($order);

            if($ret->code == 10000)
            {
                $orders = new \models\Order;
                $orders->startTrans();


                $ret1 = $orders->setpayout($sn);
                $user = new \models\User;
                $ret2 = $user->abbMoney($money,$_SESSION['id']);

                if($ret1 && $ret2){
                    $orders->commit();
                }else{
                    $orders->rollback();
                }
                echo '退款成功！信息更新成功';    
            }
            else
            {
                echo '信息更新失败' ;
                var_dump($ret);
            }
        }
        catch(\Exception $e)
        {
            var_dump( $e->getMessage() );
        }
    }
}