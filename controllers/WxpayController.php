<?php
namespace controllers;

use Yansongda\Pay\Pay;

class WxpayController
{
    // 支付账号：hgnvpu3964@sandbox.com

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
        // 接收订单编号
        $sn = $_POST['sn'];
        // 取出订单信息
        $order = new \models\Order;
        // 根据订单编号取出订单信息
        $data = $order->findBySn($sn);

        if($data['status'] == 0)
        {
            // 调用微信接口
            $ret = Pay::wechat($this->config)->scan([
                'out_trade_no' => $data['sn'],
                'total_fee' => intval($data['money'] * 100), // 单位：分
                'body' => '智聊系统用户充值 ：'.$data['money'].'元',
            ]);

            if($ret->return_code == 'SUCCESS' && $ret->result_code == 'SUCCESS')
            {
                // 加载视图,并把支付码的字符串转到页面中
                view('users.wxpay', [
                    'code' => $ret->code_url,
                    'sn' => $sn,
                ]);
            }
        }
        else
        {
            die('订单状态不允许支付~');
        }
    }

    public function notify()
    {
        // $log = new \libs\Log('wxpay.log');

        // $log->log('接收到微信的消息');

        $pay = Pay::wechat($this->config);

        try{
            $data = $pay->verify(); // 是的，验签就这么简单！

            // $log->log('验证成功，接收的数据是：' . file_get_contents('php://input'));

            if($data->result_code == 'SUCCESS' && $data->return_code == 'SUCCESS')
            {
                // 更新订单状态
                $order = new \models\Order;
                // 获取订单信息
                $orderInfo = $order->findBySn($data->out_trade_no);
                if($orderInfo['status'] == 0)
                {
                    // 开启事务
                    $order->startTrans();

                    // 设置订单为已支付状态
                    $ret1 = $order->setPaid($data->out_trade_no);
                    // 更新用户余额
                    $user = new \models\User;
                    $ret2 = $user->addMoney($orderInfo['money'], $orderInfo['user_id']);

                    // 判断
                    if($ret1 && $ret2)
                    {
                        // 提交事务
                        $order->commit();
                    }
                    else
                    {
                        // 回事事务
                        $order->rollback();
                    }


                }

            }

        } 
        catch (Exception $e) {
            // $log->log('验证失败！' . $e->getMessage());
            var_dump( $e->getMessage() );
        }
        
        $pay->success()->send();
    }
}