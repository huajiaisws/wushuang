<?php
/**
 * 买家已付款，但是卖家2小时内都没有点击确认，自动确认，每15分钟执行一次 需要定时任务 .
 * User: admin
 * Date: 2019/6/4
 * Time: 11:04
 */
if (!defined("APP_PATH")) {
    define('APP_PATH', __DIR__ . '/application/');
    // 加载框架引导文件
    require __DIR__ . '/thinkphp/start.php';
}

function check_finish(){
    echo "\r\n开始时间：".date('Y-m-d H:i:s')."\r\n";
    //首先获取买家订单中，确认付款时间小于当前时间减去2个小时的订单，同时订单状态为待确认的 2
    //测试暂时 把2小时改为 1分钟 7200 改为 60
    $db = db('ore_order');

    //获取自动确认时长 单位小时
    $qr_time = config('site.qr_time') > 0 ? config('site.qr_time') : 2;
    $qr_time = $qr_time * 3600;

    $info = $db->field('id,ordersn,orecode,buy_username,sell_username,sell_ordersn')
        ->where('pay_time','<',time() - $qr_time) //测试暂时 + 7200
        ->where('status',1)
        ->where('status2',0)
        ->where('status3',0)
        ->where('status4',0)
        ->where('status5',0)->select();

    if ($info) {
        $info = collection($info)->toArray();
        $tm = time();

        //卖家交易完成信息
        $sell_finish_arr = null;
        //卖家用户信息
        $sell_user_arr = null;
        //日志
        $log_arr = null;
        //买家订单信息
        $buy_order_arr = null;
        //矿信息
        $ore_arr = null;
        //表头
        $prefix = config('database.prefix');
        $jj_arr = null;
        //用户信息
        $users = db('user')->where('id','>',0)->column('id','username');

        //记录循环次数
        $number = 0;

        foreach ($info as $val) {
            $number++;
            //确认交易完成，对应的收益要发放给卖家
            $info2 = $db->field('ordersn,orecode,credit2,credit4,credit5')->where('status',3)->where('status2',0)->where('status3',0)->where('status4',3)->where('status5',0)->where('ordersn',$val['sell_ordersn'])->where('buy_username',$val['sell_username'])->find();

            //卖家确认交易完成
            //$db->where('status',3)->where('status2',0)->where('status3',0)->where('status4',3)->where('status5',0)->where('ordersn',$val['sell_ordersn'])->where('buy_username',$val['sell_username'])->update(['status4' => 4,'sell_time' => $tm]);
            $sell_finish_arr[] = 'update '.$prefix.'ore_order set status4=4,sell_time='.$tm.' where status=3 and status2=0 and status3=0 and status4=3 and status5=0 and ordersn="'.$val['sell_ordersn'].'" and buy_username="'.$val['sell_username'].'"';

            //发放卖家收益
            if ($info2['credit2'] > 0) {
                //setCc($val['sell_username'],'credit2',$info2['credit2'],config('site.ore_text').'到期，交易完成获得'.config('site.credit2_text').'：'.$info2['credit2']);
                $sell_user_arr[] = 'update '.$prefix.'user set credit2=credit2+'.$info2['credit2'].' where username="'.$val['sell_username'].'"';
                $log_arr[] = [
                    'username' => $val['sell_username'],
                    'type' => 'credit2',
                    'num' => $info2['credit2'],
                    'remark' => config('site.ore_text').'到期，交易完成获得'.config('site.credit2_text').'：'.$info2['credit2'],
                    'createtime' => $tm,
                    'updatetime' => $tm
                ];
            }
            if ($info2['credit4'] > 0) {
                //setCc($val['sell_username'],'credit4',$info2['credit4'],config('site.ore_text').'到期，交易完成获得'.config('site.credit4_text').'：'.$info2['credit4']);
                $sell_user_arr[] = 'update '.$prefix.'user set credit4=credit4+'.$info2['credit4'].' where username="'.$val['sell_username'].'"';
                $log_arr[] = [
                    'username' => $val['sell_username'],
                    'type' => 'credit4',
                    'num' => $info2['credit4'],
                    'remark' => config('site.ore_text').'到期，交易完成获得'.config('site.credit4_text').'：'.$info2['credit4'],
                    'createtime' => $tm,
                    'updatetime' => $tm
                ];
            }
            /*if ($info['credit5'] > 0) {
                setCc($this->auth->username,'credit5',$info['credit5'],'矿到期，交易完成获得法币收益：'.$info['credit5']);
            }*/


            // 卖家确认交易完成，修改买家的订单状态
            //$db->where('sell_ordersn',$val['sell_ordersn'])->where('status',1)->where('status2',0)->where('status3',0)->where('status4',0)->where('status5',0)->update(['status'=>2,'wc_time' => $tm]);
            $buy_order_arr[] = 'update '.$prefix.'ore_order set status=2,wc_time='.$tm.' where sell_ordersn="'.$val['sell_ordersn'].'" and status=1 and status2=0 and status3=0 and status4=0 and status5=0';

            //修改矿的状态
            //获取买家的信息
            //$buy_info = $this->db->where('sell_ordersn',$ordersn)->where('status',2)->where('status2',0)->where('status3',0)->where('status4',0)->where('status5',0)->find();

            //$odb->where('orecode',$val['orecode'])->update(['status2' => 2,'ap_username' => $val['buy_username'],'ap_ordersn' => $val['ordersn']]);
            $ore_arr[] = 'update '.$prefix.'block_ore set status2=2,ap_username="'.$val['buy_username'].'",ap_ordersn="'.$val['ordersn'].'" where orecode="'.$val['orecode'].'"';

            // 这里要调用触发奖金
            $jj_arr[] = [
                'uid' => $users[$val['sell_username']],
                'amount' => $info2['credit5'],
                'ordersn' => $info2['ordersn']
            ];

            if ($number == 1000) {
                //批量处理
                if ($sell_finish_arr) {
                    db('ore_order')->batchQuery($sell_finish_arr);
                }
                if ($sell_user_arr) {
                    db('user')->batchQuery($sell_user_arr);
                }
                if ($buy_order_arr) {
                    db('ore_order')->batchQuery($buy_order_arr);
                }
                if ($ore_arr) {
                    db('block_ore')->batchQuery($ore_arr);
                }
                if ($log_arr) {
                    db('cc_detail_log')->insertAll($log_arr);
                }

                if ($jj_arr) {
                    // 这里要调用触发奖金
                    \app\common\core\Procevent::dsell_event($jj_arr,'tgj');
                    \app\common\core\Procevent::dsell_event($jj_arr,'tdj');
                }

                //卖家交易完成信息
                $sell_finish_arr = null;
                //卖家用户信息
                $sell_user_arr = null;
                //日志
                $log_arr = null;
                //买家订单信息
                $buy_order_arr = null;
                //矿信息
                $ore_arr = null;
                $jj_arr = null;
            }
        }

        //批量处理
        if ($sell_finish_arr) {
            db('ore_order')->batchQuery($sell_finish_arr);
        }
        if ($sell_user_arr) {
            db('user')->batchQuery($sell_user_arr);
        }
        if ($buy_order_arr) {
            db('ore_order')->batchQuery($buy_order_arr);
        }
        if ($ore_arr) {
            db('block_ore')->batchQuery($ore_arr);
        }
        if ($log_arr) {
            db('cc_detail_log')->insertAll($log_arr);
        }

        if ($jj_arr) {
            // 这里要调用触发奖金
            \app\common\core\Procevent::dsell_event($jj_arr,'tgj');
            \app\common\core\Procevent::dsell_event($jj_arr,'tdj');
        }

        echo "处理完成\r\n";
    }else{
        echo "没有数据\r\n";
    }
    echo "执行结束时间：".date('Y-m-d H:i:s')."\r\n";
}

check_finish();