<?php
/**
 * 订单是否超过了付款时间，如果超过了，买家订单报废，卖家订单延期一天，每15分钟执行一次 需要定时任务.
 * User: admin
 * Date: 2019/6/4
 * Time: 11:12
 */

if (!defined("APP_PATH")) {
    define('APP_PATH', __DIR__ . '/application/');
    // 加载框架引导文件
    require __DIR__ . '/thinkphp/start.php';
}

function check_paytime(){
    echo "\r\n开始时间：".date('Y-m-d H:i:s')."\r\n";
    //获取今天的开始时间和结束时间
    $stime = strtotime(date('Y-m-d'));
    $etime = strtotime(date('Y-m-d 23:59:59'));
    $tm = time();
    $db = db('ore_order');

    //如果超过了付款时间，进行冻结
    $data = $db
        ->where('pay_etime','<',$tm)
        ->where('status',0)
        ->where('status2',0)
        ->where('status3',0)
        ->where('status4',0)
        ->where('status5',0)
        ->field('id,orecode,buy_username,sell_ordersn,sell_username')
        ->select();
    if (empty($data)) {
        echo "今天没有交易成功的订单\r\n";
    }else{
        $udb = db('user');
        $data = collection($data)->toArray();

        // 记录修改的数据
        //记录卖家订单信息
        $sell_arr = [];
        //记录买家订单信息
        $buy_order_arr = [];
        //记录买家信息
        $buy_user_arr = [];
        //修改矿的状态
        $ore_arr = [];
        
        $prefix = config('database.prefix');

        //记录循环次数
        $number = 0;

        foreach ($data as $val) {
            $number++;
            //卖家的订单改为延期状态，延期一天，  把到期时间延长一天
            /*$db->where('ordersn',$val['sell_ordersn'])
                ->where('status',3)
                ->where('status2',0)
                ->where('status3',0)
                ->where('status4',2)
                ->where('status5',0)
                ->update(['status' => 2,'status4' => 0,'due_time' =>Db::raw('due_time+86400'),'delay_time' => $tm,'delay_etime' => ($tm + 86400)]);*/
            $sell_arr[] = 'update '.$prefix.'ore_order set status=2,status4=0,due_time=due_time+86400,delay_time=due_time,delay_etime=due_time+86400 where ordersn="'.$val['sell_ordersn'].'" and status=3 and status2=0 and status3=0 and status4=2 and status5=0';

            //买家的订单状态改为失效
            //$db->where('id',$val['id'])->update(['status' => 99]);
            $buy_order_arr[] = 'update '.$prefix.'ore_order set status=99 where id='.$val['id'];

            //冻结买家的账号
            //$udb->where('username',$val['buy_username'])->update(['status' => 'hidden','lock_time' => $tm,'lock_etime' => ($tm + 86400)]);
            $buy_user_arr[] = 'update '.$prefix.'user set status="hidden",lock_time='.$tm.',lock_etime='.($tm + 86400).' where username="'.$val['buy_username'].'"';

            // 修改矿的状态 收益中
            //$bldb->where('orecode',$val['orecode'])->update(['status2' => 2]);
            $ore_arr[] = 'update '.$prefix.'block_ore set status2 = 2 where orecode="'.$val['orecode'].'"';

            if ($number == 1000) {
                //批量处理数据
                if ($sell_arr) {
                    db('ore_order')->batchQuery($sell_arr);
                }
                if ($ore_arr) {
                    db('block_ore')->batchQuery($ore_arr);
                }
                if ($buy_order_arr) {
                    db('ore_order')->batchQuery($buy_order_arr);
                }
                if ($buy_user_arr) {
                    db('user')->batchQuery($buy_user_arr);
                }

                // 记重置数组
                //记录卖家订单信息
                $sell_arr = [];
                //记录买家订单信息
                $buy_order_arr = [];
                //记录买家信息
                $buy_user_arr = [];
                //修改矿的状态
                $ore_arr = [];
                //记录循环次数
                $number = 0;
            }
        }

        //批量处理数据
        if ($sell_arr) {
            db('ore_order')->batchQuery($sell_arr);
        }
        if ($ore_arr) {
            db('block_ore')->batchQuery($ore_arr);
        }
        if ($buy_order_arr) {
            db('ore_order')->batchQuery($buy_order_arr);
        }
        if ($buy_user_arr) {
            db('user')->batchQuery($buy_user_arr);
        }

        echo "操作成功\r\n";
    }
    echo '执行结束时间：'.date('Y-m-d H:i:s')."\r\n";
}

check_paytime();