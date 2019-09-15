<?php
/**
 * 中午十二点结算订单收益 需要定时任务.
 * User: admin
 * Date: 2019/6/4
 * Time: 10:57
 */

if (!defined("APP_PATH")) {
    define('APP_PATH', __DIR__ . '/application/');
    // 加载框架引导文件
    require __DIR__ . '/thinkphp/start.php';
}

function getProfit(){
    echo "\r\n开始时间：".date('Y-m-d H:i:s')."\r\n";
    //获取今天的开始时间
    $stime = strtotime(date('Y-m-d'));
    $tm = time();
    $rdb = db('ore_order');
    $udb = db('user');
    //查询收益中的订单
    $fieldname = 'id,pcp,total_money,days,per,credit2,credit4_per,credit4,credit5,buy_username';

    //暂时去掉这个条件判断 ->where('updatetime','<',$stime)
    $data = $rdb->where('status',2)->where('status2',0)->where('status3',0)->where('status4',0)->where('status5',0)->where('updatetime','<',$stime)->field($fieldname)->select();

    if (!empty($data)) {
        $data = collection($data)->toArray();
        $res = null;
        $log = null;
        $user = null;

        //记录循环次数
        $number = 0;

        foreach ($data as $val) {
            $number++;
            if ($val['days'] > 0) {
                // 法币收益
                $credit5 = round($val['pcp'] * ($val['per']/$val['days']) /100,2) ;
                $credit5 = $credit5 > 0 ? $credit5 : 0;

                // 可挖DOGE
                $credit4 = round($credit5 * $val['credit4_per'] / 100,2);
                $credit4 = $credit4 > 0 ? $credit4 : 0;

                $res[] = 'update fa_ore_order set credit5='.($val['credit5'] + $credit5).',credit4='.($val['credit4'] + $credit4).',total_money='.($val['pcp'] + $credit5 + $val['credit5']).',updatetime='.$tm.' where id = '.$val['id'];
                $log[] = ['username' => $val['buy_username'],'type' => 'credit5','num' => $credit5,'remark' => '收益：'.$credit5,'createtime' => time(),'updatetime' =>time()];
                $user[] = 'update fa_user set credit5 = credit5+'.$credit5.' where username = "'.$val['buy_username'].'"';

            }else{
                //主要是针对最大值拆分生成的订单
                $arr = [
                    'updatetime' => $tm
                ];
                $rdb->where('id',$val['id'])->update($arr);
            }

            if ($number == 1000) {
                if ($user) {
                    //批量修改用户表 的法币收益
                    db('user')->batchQuery($user);
                }
                if ($res) {
                    //批量修改订单表数据
                    db('ore_order')->batchQuery($res);
                }
                if ($log) {
                    //批量插入记录表
                    db('cc_detail_log')->insertAll($log);
                }

                $res = null;
                $log = null;
                $user = null;

                //记录循环次数
                $number = 0;
            }

        }
        if ($user) {
            //批量修改用户表 的法币收益
            db('user')->batchQuery($user);
        }
        if ($res) {
            //批量修改订单表数据
            db('ore_order')->batchQuery($res);
        }
        if ($log) {
            //批量插入记录表
            db('cc_detail_log')->insertAll($log);
        }
        echo "处理完成\r\n";
    }else{
        echo "没有数据\r\n";
    }
    echo '执行结束时间：'.date('Y-m-d H:i:s')."\r\n";
}

getProfit();