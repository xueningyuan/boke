<?php
/* 支付宝支付的控制器 */
namespace controllers;

use Yansongda\Pay\Pay;

class AlipayController
{
    // 配置
    public $config = [
        'app_id' => '2016091700531214',
        // 支付宝公钥
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA3MB5RPixTIK2AlHUGigJBwgwXO5WHJtoVtyOyLsakCVOy+nY9Ij0efgN8cV/NCDGCKL7u7nbaWfG2yyRexFwYbyhNHkzGh/Qpnl/2Vu5nMmDyOluP8fFs1GzblTupEilQ/zfb2veLUIzSp4QWn7EQB6a08ub+7s/oBttoQpSSxdVK240nPk9Kaugk0uIyMufeGvXcK9Bg+bBSEu7b39E0OR/ieCbDvb6k0A/BG4j6ubYiBL+TRbGE28y/TbUkzdqIR4xaWlVerFW5IwJvmrPoCYIVp9EC8RMoMdrcT2CW4rBgh2Ybr6+VHQVJZwhBBJgSQg0y8vZqugEzpP4yl/d6wIDAQAB',
        // 商户应用密钥
        'private_key' => 'MIIEowIBAAKCAQEA4xspCIhE+hMLKjJc19y4psoqupXsLXlP9CUDOsYvppkDLp2E3LBrVSVogw2coh2KqeMjAQ/BB+PJ8IpkeAk1wE4KAdmA0FnCGmq8qZlESzf73zLKmCqRoX5QwElz2cBEhQrk3UnArZ2GTj3jfCZUdbusce6uEdI9DW9Agw1zouTQmckuKo59IEF6hXUivMFL6eh6V7z9gFUn82Wt62E1jKp7kYjpYHRLO8mQUHOQ4HC6ThASIiQgMNXGOGULRsXgKIK2DeV9fnL+JkN7VfL9GZ1UhuKwjbvtCCpX8OKhbFEm91wu/SbcaajRIP3fe4aY4zK+2k+wvIpOjcZKTheFswIDAQABAoIBACA838NFTL1O7LvNsF44B8ItWoln9MGzwcS/aEj0jxkQCWKZm52UMXhBuic4TG660M8y3eotqVIMZMMPchmT/RxSN5txm5Z311TWp/dPOWGQDeHuHNIi4M9S1fWlt5tGbrOQC1LaQE6k2MbMhDlAW6bmwCDgJ7eB54a7ryWrSCnsGrekfmQz9joIM/1Xl4MdZlORigR3pcmvPCUMA2xB1QOX1qn2R1sYJXg7D6uJBbEw2TPYj81hjpa6Sal/Y6ZX4cInI6UasYv8ShcFKbous401FrsdbA/NgFsuXQOD/I4aDJcBpV9sAyf4ngCGLJ+QWKQE+aSQOrSIYXMS7ZioUNkCgYEA8kVkxOcxzavw1ha6CVdYtJcQHHD0bHKC9y+GvDSPHz5M0UKyYedw5v2u9hhHfw6sYNyj91jPe+4NrKBvam/pLuvbHf9tbW1+5M6B+A5hJ8SOnXzTtu4Ry+LXN3dgGrd7XCULYo9n4mvotZiisqgliGLB5iGN9ciXV8SKZdwJRw0CgYEA7/nFBqWefyIGO6gP9/9RtiUIChTbmiAz1D3orDI7XRIbAnQgYPb6EzESwzRwK6AcEOLwx1NvATV3YQI7jCczNMWkYULvv4ekpwZEpJ8z5Ojy+oIBWUwv3NPL7O/PTKsh1T/UrpHd1fh5I6yQo3aIxG6Qjn2mz27Qihreln+Yz78CgYBKs/sOe/tvX8UzPm6+0qAXjzz4iBvWFLktXwo8njhDegJVxCsc5TB7CV4ZpALnuq6Mb3xfmJLhs9WjlRTFzRwpy7AU393uEAVAqCyLQGPUz1bqWMMvdNkn9RpHkBeiJVF0aDfKfE2cE4n99MK2NALeuxTu0Qnk76U6+u9x2RdDQQKBgQCwmHmpXo+4tu0nUZIOylDzXWUBJkBEt5XshnKG5aBR6VT/BT4enSGCpgZMqHYzZGvC8X6G8JsrpJDpTp9LkD1ahGdnO776j3NXhoFVM+MYfWTxfGJJuIswUpwrDH7cyMLpD0QQAz/gii17Vy5JXJ1hEIxIj6cF12KXfxZ2YgeuOQKBgHa8cCBr34/2iGUw1jQlL1xkIQpqMiso+lurt2NlaBHzrU1E546QPUU6FdoBVjznpQtDSTijCg3SRW0dZBLviToIjTevMY9G7W2X9kTcmnQtEPAcRw3aWGZPtGeZ6menSsYzZ5Kcpg3zrsHITZBPW3jkHJ5BWHb93rWLTyLyci/g',
        
        // 通知地址
        'notify_url' => 'http://requestbin.fullcontact.com/ru73wrru',
        // 跳回地址
        'return_url' => 'http://localhost:9999/alipay/return',
        
        // 沙箱模式（可选）
        'mode' => 'dev',
    ];

    // 跳转到支付宝
    public function pay()
    {
        // 先在本地的数据库中生成一个订单（支付的金额、支付状态等信息、订单号）
        // 模拟一个假的订单
        $order = [
            'out_trade_no' => time(),    // 本地订单ID
            'total_amount' => '0.01',    // 支付金额（单位：元）
            'subject' => 'test subject', // 支付标题
        ];

        // 跳转到支付宝
        $alipay = Pay::alipay($this->config)->web($order);
        $alipay->send();
    }
    // 支付完成跳回
    public function return()
    {
        // 验证数据是否是支付宝发过来
        $data = Pay::alipay($this->config)->verify();


        echo '<h1>支付成功！</h1> <hr>';

        var_dump( $data->all() );

    }
    // 接收支付完成的通知
    public function notify()
    {
        $alipay = Pay::alipay($this->config);
        try{
            $data = $alipay->verify(); // 是的，验签就这么简单！
            // 这里需要对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            
            echo 'din_ID:'.$data->out_trade_no ."\r\n"; //订单ID：
            echo 'NUM:'.$data->total_amount ."\r\n";    //支付总金额：
            echo 'type:'.$data->trade_status ."\r\n";   //支付状态：
            echo 'mai_ID:'.$data->seller_id ."\r\n";    //商户ID：
            echo 'app_id:'.$data->app_id ."\r\n";       //app_id：

        } catch (\Exception $e) {
            echo '失败：';
            var_dump($e->getMessage()) ;
        }

        // 回应支付宝服务器（如何不回应，支付宝会一直重复给你通知）
        $alipay->success()->send();
    }

    // 退款
    public function refund()
    {
        // 生成唯一退款订单号（以后使用这个订单号，可以到支付宝中查看退款的流程）
        $refundNo = md5( rand(1,99999) . microtime() );

        try{
            $order = [
                'out_trade_no' => '1536312583',    // 退款的本地订单号
                'refund_amount' => 0.01,              // 退款金额，单位元
                'out_request_no' => $refundNo,     // 生成 的退款订单号
            ];

            // 退款
            $ret = Pay::alipay($this->config)->refund($order);

            if($ret->code == 10000)
            {
                echo '退款成功！';
            }
            else
            {
                echo '失败' ;
                var_dump($ret);
            }
        }
        catch(\Exception $e)
        {
            var_dump( $e->getMessage() );
        }
    }
}