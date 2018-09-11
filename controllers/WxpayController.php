<?php
namespace controllers;

use Yansongda\Pay\Pay;
use Endroid\QrCode\QrCode;

class WxpayController
{
    protected $config = [
        'app_id' => 'wx426b3015555a46be', // 公众号 APPID
        'mch_id' => '1900009851',
        'key' => '8934e7d15453e97507ef794cf7b0519d',

        // 通知的地址
        'notify_url' => 'http://xue.tunnel.echomod.cn/wxpay/notify',
    ];

    // 调用微信接口进行支付
    public function pay()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **单位：分**
            'body' => 'test body - 测试',
        ];

        // 调用接口
        $pay = Pay::wechat($this->config)->scan($order);

        // 打印返回值 
        // echo $pay->return_code , '<hr>';
        // echo $pay->return_msg , '<hr>';
        // echo $pay->appid , '<hr>';
        // echo $pay->result_code , '<hr>';
        // echo $pay->code_url , '<hr>';     // 支付码
        self::qrcode($pay->code_url);
    }

    public function notify()
    {
        $pay = Pay::wechat($this->config);

        try{
            $data = $pay->verify(); // 是的，验签就这么简单！

            if($data->result_code == 'SUCCESS' && $data->return_code == 'SUCCESS')
            {
                echo '共支付了：'.$data->total_fee.'分';
                echo '订单ID：'.$data->out_trade_no;
            }

        } catch (Exception $e) {
            var_dump( $e->getMessage() );
        }
        
        $pay->success()->send();
    }
    // 二维码
    public function qrcode($imgs)
    {
        $qrCode = new QrCode($imgs);
        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();
    }











}