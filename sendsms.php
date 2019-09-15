<?php
/**
 * 处理完抢购数据之后，发送短信 需要定时任务 十分钟执行一次.
 * User: admin
 * Date: 2019/6/4
 * Time: 11:51
 */

if (!defined("APP_PATH")) {
    define('APP_PATH', __DIR__ . '/application/');
    // 加载框架引导文件
    require __DIR__ . '/thinkphp/start.php';
}

function sendSms(){
    echo '开始执行的时间：'.date('Y-m-d H:i:s')."\r\n";
    //查询状态为待付款的订单，并且抢购成功时间大于今天的开始时间
    $redis = new \Redis();
    $redis->connect(config('redis.host'),config('redis.port'));

    $orders = db('ore_order')->where('status',0)->where('status2',0)->where('status3',0)->where('status4',0)->where('status5',0)->field('periods,buy_username,sell_username')->select();
    if ($orders) {
        $udb = db('user');
        //当天的结束时间
        $sms = new \app\api\controller\Sms();
        echo '开始发送';
        $expireTime = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
        foreach ($orders as $val) {
            $buy = $udb->where('username',$val['buy_username'])->field('mobile')->find();
            $sell = $udb->where('username',$val['sell_username'])->field('mobile')->find();
            //&& !$redis->hGet('sms',$val['periods'].'_buy_'.$buy['mobile'])
            if ($buy && !$redis->hGet('sms',$val['periods'].'_buy_'.$buy['mobile'])) {
                //买家短信
                $sms->send($buy['mobile'],'buy');
                //action('Sms/sendc',$buy['mobile']);
                //记录该用户已经发短信了
                $redis->hSet('sms',$val['periods'].'_buy_'.$buy['mobile'],1);
                $redis->expireAt('sms',$expireTime);
            }
            //&& !$redis->hGet('sms',$val['periods'].'_sell_'.$sell['mobile'])
            if ($sell && !$redis->hGet('sms',$val['periods'].'_sell_'.$sell['mobile'])) {
                //卖家短信
                $sms->send($sell['mobile'],'sell');
                //action('Sms/sendb',$sell['mobile']);
                //记录该用户已经发短信了
                $redis->hSet('sms',$val['periods'].'_sell_'.$sell['mobile'],1);
                $redis->expireAt('sms',$expireTime);
            }
        }
        echo "发送完成\r\n";
    }
    $redis->close();
    echo '执行结束的时间：'.date('Y-m-d H:i:s')."\r\n";
}

sendSms();