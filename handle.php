<?php
/**
 * 处理抢购的数据，抢矿后两分钟执行 需要定时任务.
 * User: admin
 * Date: 2019/6/3
 * Time: 17:31
 */

if (!defined("APP_PATH")) {
    define('APP_PATH', __DIR__ . '/application/');
    // 加载框架引导文件
    require __DIR__ . '/thinkphp/start.php';
}


function handle(){
    echo "\r\n开始时间：".date('Y-m-d H:i:s')."\r\n";
    //矿模型实例
    //$m_ore = model('Ore');
    //订单模型实例
    $m_order = model('Oreorder');

    //获取矿的所有等级
    $lvs = db('block_ore_level')->where('status',1)->column('id,level,stime,etime,money,money2,days,per,credit2,credit4','level');

    //redis 前缀
    $rsp = config('redis.prefix');

    //redis数据出队操作,从redis中将请求取出
    $redis = new \Redis();
    $redis->connect(config('redis.host'),config('redis.port'));

    if ($redis->get($rsp.'_runing') == -1) {
        echo "\r\n正在处理数据";
        $redis->close();
        return;
    }

    //数据正在处理中
    $redis->set($rsp.'_runing',-1);
    //今天结束时间
    $expireTime = mktime(23, 59, 59, date("m"), date("d"), date("Y"));

    try {
        if ($lvs) {
            $lvs = collection($lvs)->toArray();

            //记录抢购成功的数组
            $success_arr = array();

            //获取系统设置的准备抢购时间
            $zb_time = config('site.zbqg_time') * 60;

            //记录预约记录中需要回退的期数
            $bk_per = [];

            //记录抢购的人数
            $mining_arr = [];

            //获取付款时长 单位小时
            $pay_time = config('site.pay_time') > 0 ? config('site.pay_time') : 2;
            $pay_time = $pay_time * 3600;

            $udb = db('user');
            $odb = db('ore_order');
            $oredb = db('block_ore');

            //表头
            $prefix = config('database.prefix');

            //获取用户信息
            $uinfo = $udb->where('id','>',0)->column('id,username,level,credit1','username');

            foreach ($lvs  as $level) {
                //暂时定为开始时间后的2分钟开始处理数据,如果当前时间大于截止时间，不进行数据处理
                //测试暂时定为一分钟 120 改为 60
                if (date('Hi',$level['stime']+120) > date('Hi',time()) || date('Hi',time()) > date('Hi',$level['etime'])) {
                    //时间还没到，不能处理数据
                    continue;
                }

                //记录在准备抢购时间内的会员信息
                $arr = null;
                //记录不在准备抢购时间内的会员信息
                $arr2 = null;

                //预约期数
                $bper = date('Ymd').$level['level'];
                $periods = getPer($level['level']);

                $qg = $redis->hGetAll($periods);
                if ($qg) {
                    $bf = null;
                    foreach ($qg as $val) {
                        $val = json_decode($val,true);
                        $tm = $lvs[$val['level']]['stime'] + $zb_time;
                        if (date('His',$val['times']) <= date('His',$tm)) {
                            //在准备抢购时间内的
                            $arr[] = $val;
                        }else{
                            $arr2[] = $val;
                        }
                        $bf[] = $val;
                    }

                    $mining_arr[] = [
                        'periods' => $periods,
                        'num' => count($bf),
                        'params' => serialize($bf),
                        'createtime' => time()
                    ];

                    //数据拿到后，清楚缓存，清楚缓存前先作备份
                    $redis->hSet('backups','backups_'.$periods,json_encode($bf));
                    $redis->del($periods);
                }

                //最优先处理指派的，指定的直接派给用户，不需要用户再点击，如果用户抢购成功了，也不再做派发处理
                //获取所有可抢的矿，指派的
                $ores2 = db('block_ore')->where('status',1)->where('status2',0)->where('level',$level['level'])->where('username','<>','')->select();

                //记录预约记录要执行回退的期数
                if (!in_array($periods,$bk_per)) {
                    $bk_per[] = $periods;
                }

                if ($ores2) {

                    $ores2 = collection($ores2)->toArray();
                    $sec_time = time();

                    //记录指派的矿信息
                    $zp_ore = [];
                    //记录指派的修改订单
                    $zp_order = [];
                    //记录指派的插入订单信息
                    $zp_insert = [];

                    //记录循环次数
                    $number = 0;

                    foreach ($ores2 as $val) {
                        if (empty($periods)) {
                            $periods = getPer($val['level']);
                        }

                        if ($uinfo[$val['username']]) {
                            $number++;
                            //获取订单编号
                            $sn = getOrderSn($periods,$val['orecode']);
                            //插入订单表
                            $data = [
                                'periods'   => $periods, // 期数
                                'ordersn'   => $sn,// 订单编号
                                'level'   => $val['level'],// 矿等级
                                'orecode'   => $val['orecode'],//矿编号
                                'pcp'   => $val['price'],//本金，矿的初始价格
                                'total_money'   => $val['price'],//本金，矿的初始价格
                                'buy_username'   => $val['username'], // 买家用户编号
                                'sell_username'   => $val['ap_username'], // 卖家用户编号
                                'sell_ordersn'   => $val['ap_ordersn'], // 卖家用户编号
                                'days'  => $lvs[$val['level']]['days'], // 智能合约的收益天数
                                'per'   => $lvs[$val['level']]['per'],//智能合约的收益百分比
                                'money' => $lvs[$val['level']]['money'],//预约矿机
                                'money2' => $lvs[$val['level']]['money2'],//非预约矿机
                                'credit2'   => $lvs[$val['level']]['credit2'],//可挖区块mine
                                'credit4_per'   => $lvs[$val['level']]['credit4'],//可挖DOGE收益百分比 n%/天
                                'success_time'   => $sec_time,//抢购成功的时间
                                'pay_etime'   => $sec_time + $pay_time,//剩余付款时间，抢购成功时间 + 2小时
                                'due_time'  => strtotime(date('Ymd').' '.date('Hi',$lvs[$val['level']]['stime'])) + $lvs[$val['level']]['days'] *  86400, // 矿的智能合约的到期时间
                                'status'    => 0 //待付款状态
                            ];

                            //生成订单
                            //修改矿的状态
                            //$oredb->where('orecode',$val['orecode'])->update(['status2' => 1,'username' => '']);
                            $zp_ore[] = 'update '.$prefix.'block_ore set status2=1,username="" where orecode="'.$val['orecode'].'"';

                            //修改卖家的订单状态
                            //$odb->where('ordersn',$val['ap_ordersn'])->where('status',3)->where('status2',0)->where('status3',0)->where('status4',1)->where('status5',0)->update(['status4' => 2]);
                            $zp_order[] = 'update '.$prefix.'ore_order set status4=2 where ordersn="'.$val['ap_ordersn'].'" and status = 3 and status2 = 0 and status3 = 0 and status4 = 1 and status5 = 0';

                            //插入订单
                            //$odb->insert($data);
                            $zp_insert[] = $data;

                            //记录抢矿成功的人
                            $redis->set('suc_'.$periods.'_'.$data['buy_username'],1);
                            $redis->expireAt('suc_'.$periods.'_'.$data['buy_username'],$expireTime);
                            //记录抢购成功的人
                            $success_arr[] = $data;
                        }

                        if ($number == 1000) {
                            //批量处理
                            if ($zp_ore) {
                                $oredb->batchQuery($zp_ore);
                            }
                            if ($zp_order) {
                                $odb->batchQuery($zp_order);
                            }
                            if ($zp_insert) {
                                $odb->insertAll($zp_insert);
                            }

                            //记录指派的矿信息
                            $zp_ore = [];
                            //记录指派的修改订单
                            $zp_order = [];
                            //记录指派的插入订单信息
                            $zp_insert = [];

                            //记录循环次数
                            $number = 0;
                        }
                    }

                    //批量处理
                    if ($zp_ore) {
                        $oredb->batchQuery($zp_ore);
                    }
                    if ($zp_order) {
                        $odb->batchQuery($zp_order);
                    }
                    if ($zp_insert) {
                        $odb->insertAll($zp_insert);
                    }
                }

                if ($arr) {
                    //优先处理在准备抢购时间内的会员信息，根据排单等级进行排序
                    $arr = array_sort($arr,'weights',1);
                }
                if ($arr2) {
                    //记录不在准备抢购时间内的会员信息，这个直接按顺序来处理就行
                    $arr2 = array_sort($arr2,'times');
                }

                $wg = $arr[0]['weights'];
                $arr3 = array();
                $arr4 = array();
                if (!empty($arr)) {
                    //在准备抢购时间内的会员信息，根据排单等级进行排序后，每个等级再根据时间进行排序
                    foreach($arr as $val) {
                        if ($val['weights'] == $wg) {
                            $arr4[] = $val;
                        }else{
                            $arr4 = array_sort($arr4,'times');

                            $arr3 = array_merge($arr3,$arr4);
                            $arr4 = null;
                            $wg = $val['weights'];
                            $arr4[] = $val;
                        }
                    }
                    $arr3 = array_merge($arr3,$arr4);
                }

                //实例化预约记录表的对象
                $bldb = db('booking_log');

                //优先处理在准备抢购时间内的
                if (!empty($arr3)) {

                    //批量修改矿的状态
                    $pl_ore = null;
                    //批量插入订单
                    $pl_order = null;
                    //批量修改卖家订单状态
                    $pl_sell = null;
                    //批量修改预约记录
                    $pl_booking_arr = [];
                    //记录循环次数
                    $number = 0;

                    //记录预约记录要执行回退的期数
                    if (!in_array($periods,$bk_per)) {
                        $bk_per[] = $periods;
                    }

                    foreach($arr3 as $val){
                        //获取能够分配的矿，不包括属于自己的矿，不能抢到自己的矿
                        $ores = db('block_ore')->where('status',1)->where('status2',0)->where('level',$val['level'])->where('username','')->where('ap_username','<>',$val['username'])->order('updatetime','desc')->find();
                        //订单期数
                        $periods = $val['periods'];

                        //如果已经抢购成功的，不能成功两次
                        $is_success = $redis->get('suc_'.$periods.'_'.$val['username']);
                        if ($is_success) {
                            //如果已经抢购成功的跳过
                            continue;
                        }

                        if (!empty($ores)) {
                            $number++;
                            if (empty($periods)) {
                                $periods = getPer($ores['level']);
                            }
                            //首先判断用户是否预约，如果没有预约，检测用户的矿机是否够扣减的
                            $isbooking = $bldb->where('username',$val['username'])->where('periods',$bper)->where('status',0)->find();
                            if (empty($isbooking)) {
                                // 没有预约，检测用户的矿机是否够扣减的，money为预约扣减矿机，money2为不预约扣减矿机
                                //$ishas = $udb->where('username',$val['username'])->where('credit1','>=',$lvs[$ores['level']]['money2'])->find();
                                if ($lvs[$ores['level']]['money2'] > $uinfo[$val['username']]['credit1']) {
                                    //矿机不够，直接跳过该会员
                                    continue;
                                }else{
                                    // 直接扣除对应的矿机
                                    setCc($val['username'],'credit1',-$lvs[$ores['level']]['money2'],'非预约抢购，扣除'.config('site.credit1_text').'：'.$lvs[$ores['level']]['money2']);
                                }
                            }else{
                                // 抢购成功，修改预约记录为抢购成功
                                //$bldb->where('username',$val['username'])->where('periods',$bper)->where('status',0)->update(['status'=>2]);
                                $pl_booking_arr[] = 'update '.$prefix.'booking_log set status = 2 where username = "'.$val['username'].'" and periods = "'.$bper.'" and status = 0';
                            }

                            if ($val) {
                                //获取订单编号
                                $sn = getOrderSn($periods,$ores['orecode']);
                                //插入订单表
                                $data = [
                                    'periods'   => $periods, // 期数
                                    'ordersn'   => $sn,// 订单编号
                                    'level'   => $ores['level'],// 矿等级
                                    'orecode'   => $ores['orecode'],//矿编号
                                    'pcp'   => $ores['price'],//本金，矿的初始价格
                                    'total_money'   => $ores['price'],//本金，矿的初始价格
                                    'buy_username'   => $val['username'], // 买家用户编号
                                    'sell_username'   => $ores['ap_username'], // 卖家用户编号
                                    'sell_ordersn'   => $ores['ap_ordersn'], // 卖家用户编号
                                    'days'  => $lvs[$ores['level']]['days'], // 智能合约的收益天数
                                    'per'   => $lvs[$ores['level']]['per'],//智能合约的收益百分比
                                    'money' => $lvs[$val['level']]['money'],//预约矿机
                                    'money2' => $lvs[$val['level']]['money2'],//非预约矿机
                                    'credit2'   => $lvs[$ores['level']]['credit2'],//可挖区块mine
                                    'credit4_per'   => $lvs[$ores['level']]['credit4'],//可挖DOGE收益百分比 n%/天
                                    'success_time'   => $val['times'],//抢购成功的时间
                                    'pay_etime'   => $val['times'] + $pay_time,//剩余付款时间，抢购成功时间 + 2小时
                                    'due_time'  => strtotime(date('Ymd').' '.date('Hi',$lvs[$ores['level']]['stime'])) + $lvs[$ores['level']]['days'] *  86400, // 矿的智能合约的到期时间
                                    'status'    => 0 //待付款状态
                                ];
                                //生成订单
                                //修改矿的状态
                                $oredb->where('orecode',$ores['orecode'])->update(['status2' => 1,'username' => '']);
                                /*if ($ores['orecode']) {
                                    $pl_ore[] = ['id' => $ores['id'],'status2' => 1,'username' => ''];
                                }*/
                                //修改卖家的订单状态
                                //$odb->where('ordersn',$ores['ap_ordersn'])->where('status',3)->where('status2',0)->where('status3',0)->where('status4',1)->where('status5',0)->update(['status4' => 2]);
                                if ($ores['ap_ordersn']) {
                                    $ds = $odb->where('ordersn',$ores['ap_ordersn'])->field('id')->find();
                                    if ($ds) {
                                        $pl_sell[] = ['id' =>$ds['id'],'status4' => 2];
                                    }
                                }

                                //插入订单
                                //$odb->insert($data);
                                if ($data) {
                                    $pl_order[] = $data;
                                }
                                //记录抢矿成功的人
                                $redis->set('suc_'.$periods.'_'.$data['buy_username'],1);
                                $redis->expireAt('suc_'.$periods.'_'.$data['buy_username'],$expireTime);
                                //记录抢购成功的人
                                $success_arr[] = $data;
                            }
                        }

                        if ($number == 1000) {
                            //批量修改矿的状态
                            //$m_ore->isUpdate(true)->saveAll($pl_ore);
                            //批量插入订单
                            if ($pl_order) {
                                $m_order->insertAll($pl_order);
                                //数据处理完成，把抢购成功的数据保存到Redis
                                getSucData($pl_order);
                            }
                            //批量修改卖家订单状态
                            if ($pl_sell) {
                                $m_order->isUpdate()->saveAll($pl_sell);
                            }
                            //批量修改预约记录
                            if ($pl_booking_arr) {
                                $bldb->batchQuery($pl_booking_arr);
                            }

                            //批量修改矿的状态
                            $pl_ore = null;
                            //批量插入订单
                            $pl_order = null;
                            //批量修改卖家订单状态
                            $pl_sell = null;
                            //批量修改预约记录
                            $pl_booking_arr = [];
                            //记录循环次数
                            $number = 0;
                        }

                    }

                    //批量修改矿的状态
                    //$m_ore->isUpdate(true)->saveAll($pl_ore);
                    //批量插入订单
                    if ($pl_order) {
                        $m_order->insertAll($pl_order);
                        //数据处理完成，把抢购成功的数据保存到Redis
                        getSucData($pl_order);
                    }
                    //批量修改卖家订单状态
                    if ($pl_sell) {
                        $m_order->isUpdate()->saveAll($pl_sell);
                    }
                    //批量修改预约记录
                    if ($pl_booking_arr) {
                        $bldb->batchQuery($pl_booking_arr);
                    }

                }

                //最后处理不在准备抢购时间内的
                if (!empty($arr2)) {

                    //批量修改矿的状态
                    $pl_ore = null;
                    //批量插入订单
                    $pl_order = null;
                    //批量修改卖家订单状态
                    $pl_sell = null;
                    $pl_booking_arr = [];
                    //记录循环次数
                    $number = 0;

                    //记录预约记录要执行回退的期数
                    if (!in_array($periods,$bk_per)) {
                        $bk_per[] = $periods;
                    }

                    foreach($arr2 as $val){
                        $ores = db('block_ore')->where('status',1)->where('status2',0)->where('level',$val['level'])->where('username','')->where('ap_username','<>',$val['username'])->order('updatetime','desc')->find();
                        $periods = $val['periods'];

                        //如果已经抢购成功的，不能成功两次
                        $is_success = $redis->get('suc_'.$periods.'_'.$val['username']);
                        if ($is_success) {
                            //如果已经抢购成功的跳过
                            continue;
                        }

                        if (!empty($ores)) {
                            $number++;
                            if (empty($periods)) {
                                $periods = getPer($ores['level']);
                            }

                            //首先判断用户是否预约，如果没有预约，检测用户的矿机是否够扣减的
                            $isbooking = $bldb->where('username',$val['username'])->where('periods',$bper)->where('status',0)->find();
                            //首先判断用户是否预约，如果没有预约，检测用户的矿机是否够扣减的
                            if (empty($isbooking)) {
                                // 没有预约，检测用户的矿机是否够扣减的，money为预约扣减矿机，money2为不预约扣减矿机
                                //$ishas = $udb->where('username',$val['username'])->where('credit1','>=',$lvs[$ores['level']]['money2'])->find();

                                if ($lvs[$ores['level']]['money2'] > $uinfo[$val['username']]['credit1']) {
                                    //矿机不够，直接跳过该会员
                                    continue;
                                }else{
                                    // 直接扣除对应的矿机
                                    setCc($val['username'],'credit1',-$lvs[$ores['level']]['money2'],'非预约抢购，扣除'.config('site.credit1_text').'：'.$lvs[$ores['level']]['money2']);
                                }
                            }else{
                                // 抢购成功，修改预约记录为抢购成功
                                //$bldb->where('username',$val['username'])->where('periods',$bper)->where('status',0)->update(['status'=>2]);
                                $pl_booking_arr[] = 'update '.$prefix.'booking_log set status = 2 where username = "'.$val['username'].'" and periods = "'.$bper.'" and status = 0';
                            }

                            if ($val) {
                                //获取订单编号
                                $sn = getOrderSn($periods,$ores['orecode']);
                                //插入订单表
                                $data = [
                                    'periods'   => $periods, // 期数
                                    'ordersn'   => $sn,// 订单编号
                                    'level'   => $ores['level'],// 矿等级
                                    'orecode'   => $ores['orecode'],//矿编号
                                    'pcp'   => $ores['price'],//本金，矿的初始价格
                                    'total_money'   => $ores['price'],//本金，矿的初始价格
                                    'buy_username'   => $val['username'], // 买家用户编号
                                    'sell_username'   => $ores['ap_username'], // 卖家用户编号
                                    'sell_ordersn'   => $ores['ap_ordersn'], // 卖家用户编号
                                    'days'  => $lvs[$ores['level']]['days'], // 智能合约的收益天数
                                    'per'   => $lvs[$ores['level']]['per'],//智能合约的收益百分比
                                    'money' => $lvs[$val['level']]['money'],//预约矿机
                                    'money2' => $lvs[$val['level']]['money2'],//非预约矿机
                                    'credit2'   => $lvs[$ores['level']]['credit2'],//可挖区块mine
                                    'credit4_per'   => $lvs[$ores['level']]['credit4'],//可挖DOGE收益百分比 n%/天
                                    'success_time'   => $val['times'],//抢购成功的时间
                                    'pay_etime'   => $val['times'] + $pay_time,//剩余付款时间，抢购成功时间 + 2小时
                                    'due_time'  => strtotime(date('Ymd').' '.date('Hi',$lvs[$ores['level']]['stime'])) + $lvs[$ores['level']]['days'] *  86400, // 矿的智能合约的到期时间
                                    'status'    => 0 //待付款状态
                                ];
                                //生成订单
                                //修改矿的状态
                                $oredb->where('orecode',$ores['orecode'])->update(['status2' => 1,'username' => '']);
                                /*if ($ores['orecode']) {
                                    $pl_ore[] = ['id' => $ores['id'],'status2' => 1,'username' => ''];
                                }*/
                                //修改卖家的订单状态
                                //$odb->where('ordersn',$ores['ap_ordersn'])->where('status',3)->where('status2',0)->where('status3',0)->where('status4',1)->where('status5',0)->update(['status4' => 2]);
                                if ($ores['ap_ordersn']) {
                                    $ds = $odb->where('ordersn',$ores['ap_ordersn'])->field('id')->find();
                                    if ($ds) {
                                        $pl_sell[] = ['id' =>$ds['id'],'status4' => 2];
                                    }
                                }
                                //插入订单
                                //$odb->insert($data);
                                if ($data) {
                                    $pl_order[] = $data;
                                }
                                //记录抢矿成功的人
                                $redis->set('suc_'.$periods.'_'.$data['buy_username'],1);
                                $redis->expireAt('suc_'.$periods.'_'.$data['buy_username'],$expireTime);
                                //记录抢购成功的人
                                $success_arr[] = $data;
                            }
                        }

                        if ($number == 1000) {
                            //批量修改矿的状态
                            //$m_ore->isUpdate(true)->saveAll($pl_ore);
                            //批量插入订单
                            if ($pl_order) {
                                $m_order->insertAll($pl_order);
                                //数据处理完成，把抢购成功的数据保存到Redis
                                getSucData($pl_order);
                            }
                            //批量修改卖家订单状态
                            if ($pl_sell) {
                                $m_order->isUpdate()->saveAll($pl_sell);
                            }
                            //批量修改预约记录
                            if ($pl_booking_arr) {
                                $bldb->batchQuery($pl_booking_arr);
                            }

                            //批量修改矿的状态
                            $pl_ore = null;
                            //批量插入订单
                            $pl_order = null;
                            //批量修改卖家订单状态
                            $pl_sell = null;
                            $pl_booking_arr = [];
                            //记录循环次数
                            $number = 0;
                        }

                    }

                    //批量修改矿的状态
                    //$m_ore->isUpdate(true)->saveAll($pl_ore);
                    //批量插入订单
                    if ($pl_order) {
                        $m_order->insertAll($pl_order);
                        //数据处理完成，把抢购成功的数据保存到Redis
                        getSucData($pl_order);
                    }
                    //批量修改卖家订单状态
                    if ($pl_sell) {
                        $m_order->isUpdate()->saveAll($pl_sell);
                    }
                    //批量修改预约记录
                    if ($pl_booking_arr) {
                        $bldb->batchQuery($pl_booking_arr);
                    }
                }

                if ($success_arr) {
                    //缓存抢购成功的人的信息
                    $redis->hSet('success_data',$periods,json_encode($success_arr));
                    $redis->expireAt('success_data',$expireTime);
                }
            }

            //记录抢购的人数
            if ($mining_arr) {
                db('mining_log')->insertAll($mining_arr);
            }

            if ($success_arr) {
                echo "处理完成\r\n";
            }else{
                echo "没有数据\r\n";
            }
            echo '结束时间：'.date('Y-m-d H:i:s')."\r\n";
        }
    } catch (\Exception $e) {
        trace('info','处理数据报错：'.$e->getMessage());
        $redis->set($rsp.'_runing',1);
        $redis->expireAt($rsp.'_runing',$expireTime);
    }

    //数据处理完成
    $redis->set($rsp.'_runing',1);
    $redis->expireAt($rsp.'_runing',$expireTime);
    $redis->close();
}

//二维数组排序， $arr是数据，$keys是排序的健值，$order是排序规则，1是降序，0是升序
function array_sort($arr, $keys, $order = 0)
{
    if (!is_array($arr)) {
        return false;
    }
    $keysvalue = array();
    foreach ($arr as $key => $val) {
        $keysvalue[$key] = $val[$keys];
    }
    if ($order == 0) {
        asort($keysvalue);
    } else {
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $key => $vals) {
        $keysort[$key] = $key;
    }
    $new_array = array();
    foreach ($keysort as $key => $val) {
        //$new_array[$key] = $arr[$val];
        //不需要保留键值
        $new_array[] = $arr[$val];
    }
    return $new_array;
}

//拿出抢购成功的数据，保存到Redis里面去
function getSucData($data){
    $redis = new \Redis();
    $redis->connect(config('redis.host'),config('redis.port'));

    //今天结束时间
    $expireTime = mktime(23, 59, 59, date("m"), date("d"), date("Y"));

    $redis->set('dqwc',0);
    if ($data) {
        foreach ($data as $v) {
            $redis->set($v['periods'].'_wc_'.$v['buy_username'],$v['buy_username']);
            $redis->expireAt($v['periods'].'_wc_'.$v['buy_username'],$expireTime);
        }
    }
    $redis->set('dqwc',1);
    $redis->expireAt('dqwc',$expireTime);
    $redis->close();
}

handle();