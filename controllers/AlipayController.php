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
        'private_key' => 'MIIEowIBAAKCAQEA4xspCIhE+hMLKjJc19y4psoqupXsLXlP9CUDOsYvppkDLp2E3LBrVSVogw2coh2KqeMjAQ/BB+PJ8IpkeAk1wE4KAdmA0FnCGmq8qZlESzf73zLKmCqRoX5QwElz2cBEhQrk3UnArZ2GTj3jfCZUdbusce6uEdI9DW9Agw1zouTQmckuKo59IEF6hXUivMFL6eh6V7z9gFUn82Wt62E1jKp7kYjpYHRLO8mQUHOQ4HC6ThASIiQgMNXGOGULRsXgKIK2DeV9fnL+JkN7VfL9GZ1UhuKwjbvtCCpX8OKhbFEm91wu/SbcaajRIP3fe4aY4zK+2k+wvIpOjcZKTheFswIDAQABAoIBACA838NFTL1O7LvNsF44B8ItWoln9MGzwcS/aEj0jxkQCWKZm52UMXhBuic4TG660M8y3eotqVIMZMMPchmT/RxSN5txm5Z311TWp/dPOWGQDeHuHNIi4M9S1fWlt5tGbrOQC1LaQE6k2MbMhDlAW6bmwCDgJ7eB54a7ryWrSCnsGrekfmQz9joIM/1Xl4MdZlORigR3pcmvPCUMA2xB1QOX1qn2R1sYJXg7D6uJBbEw2TPYj81hjpa6Sal/Y6ZX4cInI6UasYv8ShcFKbous401FrsdbA/NgFsuXQOD/I4aDJcBpV9sAyf4ngCGLJ+QWKQE+aSQOrSIYXMS7ZioUNkCgYEA8kVkxOcxzavw1ha6CVdYtJcQHHD0bHKC9y+GvDSPHz5M0UKyYedw5v2u9hhHfw6sYNyj91jPe+4NrKBvam/pLuvbHf9tbW1+5M6B+A5hJ8SOnXzTtu4Ry+LXN3dgGrd7XCULYo9n4mvotZiisqgliGLB5iGN9ciXV8SKZdwJRw0CgYEA7/nFBqWefyIGO6gP9/9RtiUIChTbmiAz1D3orDI7XRIbAnQgYPb6EzESwzRwK6AcEOLwx1NvATV3YQI7jCczNMWkYULvv4ekpwZEpJ8z5Ojy+oIBWUwv3NPL7O/PTKsh1T/UrpHd1fh5I6yQo3aIxG6Qjn2mz27Qihreln+Yz78CgYBKs/sOe/tvX8UzPm6+0qAXjzz4iBvWFLktXwo8njhDegJVxCsc5TB7CV4ZpALnuq6Mb3xfmJLhs9WjlRTFzRwpy7AU393uEAVAqCyLQGPUz1bqWMMvdNkn9RpHkBeiJVF0aDfKfE2cE4n99MK2NALeuxTu0Qnk76U6+u9x2RdDQQKBgQCwmHmpXo+4tu0nUZIOylDzXWUBJkBEt5XshnKG5aBR6VT/BT4enSGCpgZMqHYzZGvC8X6G8JsrpJDpTp9LkD1ahGdnO776j3NXhoFVM+MYfWTxfGJJuIswUpwrDH7cyMLpD0QQAz/gii17Vy5JXJ1hEIxIj6cF12KXfxZ2YgeuOQKBgHa8cCBr34/2iGUw1jQlL1xkIQpqMiso+lurt2NlaBHzrU1E546QPUU6FdoBVjznpQtDSTijCg3SRW0dZBLviToIjTevMY9G7W2X9kTcmnQtEPAcRw3aWGZPtGeZ6menSsYzZ5Kcpg3zrsHITZBPW3jkHJ5BWHb93rWLTyLyci/g',
        
        // 通知地址
        'notify_url' => 'http://dfce8b3a.ngrok.io/alipay/notify',
        // 跳回地址
        'return_url' => 'http://dfce8b3a.ngrok.io/alipay/return',
        
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