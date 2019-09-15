<?php
/**
 * 检测订单中今天到期的矿，修改矿的状态，中午十二点结算订单收益之后执行 需要定时任务.
 * User: admin
 * Date: 2019/6/4
 * Time: 11:02
 */

if (!defined("APP_PATH")) {
    define('APP_PATH', __DIR__ . '/application/');
    // 加载框架引导文件
    require __DIR__ . '/thinkphp/start.php';
}


function check_ore(){
    echo "\r\n开始时间：".date('Y-m-d H:i:s')."\r\n";
    //获取今天的开始时间和结束时间
    $stime = strtotime(date('Y-m-d'));
    $etime = strtotime(date('Y-m-d 23:59:59'));
    $tm = time();
    $db = db('ore_order');
    //判断执行的时间是否在 12点
    //if (date('H') == 12) {
        //到期时间是今天的，并且订单的状态是为2的
        $fieldname = 'id,orecode,ordersn,buy_username,periods,level,total_money,credit2,credit4,credit5,due_time';
        //去掉大于今天开始时间的判断 ->where('due_time','>=',$stime)
        $data = $db->where('due_time','<=',$etime)->where('status',2)->where('status2',0)->where('status3',0)->where('status4',0)->where('status5',0)->field($fieldname)->select();

        if (empty($data)) {
            echo "没有到期的矿\r\n";
        }else{
            //把到期的订单改为合约到期收益完成待转让的状态，对应的矿改为可预约的状态
            $ldb = db('block_ore_level');
            $odb = db('block_ore');

            //获取系统设置的最大值
            $ore_max = config('site.ore_max');
            //最大值拆分为的数值字符串
            $ore_ms = config('site.ore_ms');
            $data = collection($data)->toArray();

            //记录批量订单表的数据
            $odb_arr = [];
            //记录批量修改矿列表的数据
            $ore_arr = [];
            //记录批量插入订单
            $odb_inster_arr = [];
            //记录批量升级
            $lv_arr = [];
            //$lv_arr2 = [];
            //记录奖金信息
            $jj_arr = [];
            //用户信息
            $users = db('user')->where('id','>',0)->column('id','username');
            //记录拆分的信息
            $cf_order_arr = [];
            $cf_ore_arr = [];

            //记录批量修改用户信息
            $user_arr = [];
            //记录循环次数
            $number = 0;

            foreach ($data as $val) {
                $number++;
                //把订单的状态改为收益完成，转让的状态改为1
                //$db->where('id',$val['id'])->update(['status' => 3,'status4' => 1]);
                $odb_arr[] = 'update fa_ore_order set status = 3,status4 = 1 where id = '.$val['id'];

                //$odb->where('orecode',$val['orecode'])->update(['status2' => 0]);
                $ore_arr[] = 'update fa_block_ore set status2 = 0 where orecode = "'.$val['orecode'].'"';


                //判断矿是否达到升级的条件
                $lv = $ldb->where('max_price','>',$val['total_money'])->where('status',1)->field('level')->order('max_price asc')->find();
                //if ($lv['level'] > $val['level']) {
                    //升级
                    //$odb->where('level','<',$lv['level'])->where('orecode',$val['orecode'])->update(['level' => $lv['level'],'price' => $val['total_money']]);
                    $lv_arr[] = 'update fa_block_ore set level = '.($lv['level']?$lv['level']:$val['level']).',price = '.floatval($val['total_money']).' where orecode = "'.$val['orecode'].'"';
                //}else{
                    //$odb->where('orecode',$val['orecode'])->update(['price' => $val['total_money']]);
                    //$lv_arr2[] = 'update fa_block_ore set price = '.$val['total_money'].' where orecode = "'.$val['orecode'].'"';
                //}
                //最大值拆分
                if ($val['total_money'] >= $ore_max) {
                    //1000,2100,3200,1500,500,600,25
                    $mx = $val['total_money'];
                    //$code = $odb->where('id','>',0)->field('orecode')->order('orecode desc')->find();

                    //最大值拆分，提前把区块Mine、doge，法币收益累加给用户
                    $user_arr[] = 'update fa_user set credit2 = credit2 + '.floatval($val['credit2']).',credit4 = credit4 + '.floatval($val['credit4']).',credit5 = credit5 + '.floatval($val['credit5']).' where username="'.$val['buy_username'].'"';

                    foreach ($ore_ms as $v) {
                        if ($mx > 0 && $mx > $v) {
                            $cd = getOreCode();
                            $ordersn = getOrderSn($val['periods'],$cd);
                            //获取当前金额对应的等级
                            $level = $ldb->where('max_price','>',$v)->where('status',1)->field('level,money,money2')->order('max_price asc')->find();

                            //插入矿记录表
                            $odb->insert(['orecode' => $cd,'price' => $v,'level' => $level['level'],'ap_ordersn' => $ordersn,'ap_username' => $val['buy_username'],'createtime' => $tm]);

                            //生成新的订单
                            //插入订单表
                            $datas = [
                                'periods'   => $val['periods'], // 期数
                                'ordersn'   => $ordersn,// 订单编号
                                'level'   => $level['level'],// 矿等级
                                'orecode'   => $cd,//矿编号
                                'pcp'   => $v,//本金，矿的初始价格
                                'total_money'   => $v,//本金，矿的初始价格
                                'buy_username'   => $val['buy_username'], // 买家用户编号
                                'sell_username'   => $val['buy_username'], // 卖家用户编号
                                'sell_ordersn'   => $val['ordersn'], // 卖家订单编号
                                'days'  => 0, // 智能合约的收益天数
                                'per'   => 0,//智能合约的收益百分比
                                'money' => $level['money'],//预约矿机
                                'money2' => $level['money2'],//非预约矿机
                                'credit2'   => 0,//可挖区块mine
                                'credit4_per'   => 0,//可挖DOGE收益百分比 n%/天
                                'success_time'   => $tm,//抢购成功的时间
                                'pay_etime'   => $tm,//剩余付款时间，抢购成功时间 + 2小时
                                'due_time'  => $val['due_time'], // 矿的智能合约的到期时间
                                'status'    => 3, //改为收益完成状态
                                'status4'    => 1 //改为待转让状态
                            ];
                            //生成订单
                            //$this->db->insert($datas);
                            if ($datas) {
                                $odb_inster_arr[] = $datas;
                            }

                            $mx -= $v;
                        }
                        else{
                            //如果剩余的金额不够指定的拆分金额，结束循环，把剩余的金额
                            break;
                        }
                    }
                    //最后一个拆分矿
                    if ($mx > 0) {
                        //把剩余的金额作为一个矿
                        $cd = getOreCode();
                        $ordersn = getOrderSn($val['periods'],$cd);
                        //获取当前金额对应的等级
                        $level = $ldb->where('max_price','>',$mx)->where('status',1)->field('level,money,money2')->order('max_price asc')->find();
                        //插入矿记录表
                        $odb->insert(['orecode' => $cd,'price' => $mx,'level' => $level['level'],'ap_ordersn' => $ordersn,'ap_username' => $val['buy_username'],'createtime' => $tm]);

                        //生成新的订单
                        //插入订单表
                        $datas = [
                            'periods'   => $val['periods'], // 期数
                            'ordersn'   => $ordersn,// 订单编号
                            'level'   => $level['level'],// 矿等级
                            'orecode'   => $cd,//矿编号
                            'pcp'   => $mx,//本金，矿的初始价格
                            'total_money'   => $mx,//本金，矿的初始价格
                            'buy_username'   => $val['buy_username'], // 买家用户编号
                            'sell_username'   => $val['buy_username'], // 卖家用户编号
                            'sell_ordersn'   => $val['ordersn'], // 卖家订单编号
                            'days'  => 0, // 智能合约的收益天数
                            'per'   => 0,//智能合约的收益百分比
                            'money' => $level['money'],//预约矿机
                            'money2' => $level['money2'],//非预约矿机
                            'credit2'   => 0,//可挖区块mine
                            'credit4_per'   => 0,//可挖DOGE收益百分比 n%/天
                            'success_time'   => $tm,//抢购成功的时间
                            'pay_etime'   => $tm,//剩余付款时间，抢购成功时间 + 2小时
                            'due_time'  => $val['due_time'], // 矿的智能合约的到期时间
                            'status'    => 3, //改为收益完成状态
                            'status4'    => 1 //改为待转让状态
                        ];
                        //生成订单
                        //$db->insert($datas);
                        if ($datas) {
                            $odb_inster_arr[] = $datas;
                        }

                    }

                    //修改原订单的拆分状态改为 1
                    //$db->where('id',$val['id'])->update(['status5' => 1]);
                    $cf_order_arr[] = 'update fa_ore_order set status5=1 where id='.$val['id'];
                    //把原来的矿修改为 关闭
                    //$odb->where('orecode',$val['orecode'])->update(['status' => 0]);
                    $cf_ore_arr[] = 'update fa_block_ore set status=2 where orecode="'.$val['orecode'].'"';

                    //进行最大值拆分前，把用户的推荐收益清算
                    // 这里要调用触发奖金
                    $jj_arr[] = [
                        'uid' => $users[$val['buy_username']],
                        'amount' => $val['credit5'],
                        'ordersn' => $val['ordersn']
                    ];
                }

                //如果数据达到1000条，执行批量处理，处理完成再继续循环
                if ($number == 1000) {
                    //批量处理
                    if ($odb_arr) {
                        echo "====================开始执行到期的订单修改：".date('Y-m-d H:i:s')."\r\n";
                        db('ore_order')->batchQuery($odb_arr);
                    }
                    if ($ore_arr) {
                        echo "开始执行对应矿状态的修改：".date('Y-m-d H:i:s')."\r\n";
                        db('block_ore')->batchQuery($ore_arr);
                    }
                    if ($lv_arr) {
                        echo "针对升级矿信息的修改：".date('Y-m-d H:i:s')."\r\n";
                        db('block_ore')->batchQuery($lv_arr);
                    }
                    if ($cf_order_arr) {
                        echo "针对拆分订单的状态修改：".date('Y-m-d H:i:s')."\r\n";
                        db('ore_order')->batchQuery($cf_order_arr);
                    }
                    if ($cf_ore_arr) {
                        echo "针对拆分矿的状态修改：".date('Y-m-d H:i:s')."\r\n";
                        db('block_ore')->batchQuery($cf_ore_arr);
                    }
                    if ($odb_inster_arr) {
                        echo "最大拆分新增订单的插入：".date('Y-m-d H:i:s')."\r\n";
                        db('ore_order')->insertAll($odb_inster_arr);
                    }
                    if ($user_arr) {
                        db('block_ore')->batchQuery($user_arr);
                    }
                    echo "开始执行奖金：".date('Y-m-d H:i:s')."\r\n";
                    //批量处理奖金，这个是最大值拆分的奖金
                    if ($jj_arr) {
                        \app\common\core\Procevent::dsell_event($jj_arr,'tgj');
                        \app\common\core\Procevent::dsell_event($jj_arr,'tdj');
                    }
                    //重置数组
                    //记录批量订单表的数据
                    $odb_arr = [];
                    //记录批量修改矿列表的数据
                    $ore_arr = [];
                    //记录批量插入订单
                    $odb_inster_arr = [];
                    //记录批量升级
                    $lv_arr = [];
                    //$lv_arr2 = [];
                    //记录奖金信息
                    $jj_arr = [];
                    //记录拆分的信息
                    $cf_order_arr = [];
                    $cf_ore_arr = [];

                    //记录批量修改用户信息
                    $user_arr = [];
                    //记录循环次数
                    $number = 0;

                    echo "====================处理完成\r\n";
                }
            }

            //批量处理
            if ($odb_arr) {
                echo "====================开始执行到期的订单修改：".date('Y-m-d H:i:s')."\r\n";
                db('ore_order')->batchQuery($odb_arr);
            }
            if ($ore_arr) {
                echo "开始执行对应矿状态的修改：".date('Y-m-d H:i:s')."\r\n";
                db('block_ore')->batchQuery($ore_arr);
            }
            if ($lv_arr) {
                echo "针对升级矿信息的修改：".date('Y-m-d H:i:s')."\r\n";
                db('block_ore')->batchQuery($lv_arr);
            }
            if ($cf_order_arr) {
                echo "针对拆分订单的状态修改：".date('Y-m-d H:i:s')."\r\n";
                db('ore_order')->batchQuery($cf_order_arr);
            }
            if ($cf_ore_arr) {
                echo "针对拆分矿的状态修改：".date('Y-m-d H:i:s')."\r\n";
                db('block_ore')->batchQuery($cf_ore_arr);
            }
            if ($odb_inster_arr) {
                echo "最大拆分新增订单的插入：".date('Y-m-d H:i:s')."\r\n";
                db('ore_order')->insertAll($odb_inster_arr);
            }
            if ($user_arr) {
                db('block_ore')->batchQuery($user_arr);
            }
            echo "开始执行奖金：".date('Y-m-d H:i:s')."\r\n";
            //批量处理奖金，这个是最大值拆分的奖金
            if ($jj_arr) {
                \app\common\core\Procevent::dsell_event($jj_arr,'tgj');
                \app\common\core\Procevent::dsell_event($jj_arr,'tdj');
            }

            echo "====================处理完成\r\n";
        }
    echo '执行结束时间：'.date('Y-m-d H:i:s')."\r\n";
    //}

}

check_ore();