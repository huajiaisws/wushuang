<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/8/5
 * Time: 18:10
 */
if (!defined("APP_PATH")) {
    define('APP_PATH', __DIR__ . '/application/');
    // 加载框架引导文件
    require __DIR__ . '/thinkphp/start.php';
}

function booking(){
    echo "\r\n操作开始".date('Y-m-d H:i:s')."\r\n";
    $psize = 1000;

    $data = db('booking_log')->where('status',0)->field('id,username,level,credit1')->limit(0,$psize)->select();
    $prefix = config('database.prefix');
    while($data){
        //记录需要回退的预约记录信息
        $bl_arr = null;
        //插入日志记录信息
        $log_arr = null;
        //用户修改信息
        $user_arr = null;
        //处理回退
        foreach ($data as $res) {
            $tm = time();
            //状态改为已退回
            //$bldb->where('id',$bv['id'])->where('status',0)->update(['status' => 1]);
            $bl_arr[] = 'update '.$prefix.'booking_log set status=1 where id='.$res['id'].' and status=0';
            //针对没有抢购成功的，而又预约的，退回矿机
            //setCc($bv['username'],'credit1',$bv['credit1'],'抢购失败，回退预约'.config('site.credit1_text').'：'.$bv['credit1']);
            $user_arr[] = 'update '.$prefix.'user set credit1=credit1+'.$res['credit1'].' where username="'.$res['username'].'"';
            $log_arr[] = [
                'username' => $res['username'],
                'type'  => 'credit1',
                'num' => $res['credit1'],
                'remark' => '抢购失败，回退预约'.config('site.credit1_text').'：'.$res['credit1'],
                'createtime' => $tm,
                'updatetime' => $tm
            ];

        }

        //批量处理
        if ($bl_arr) {
            db('booking_log')->batchQuery($bl_arr);
        }
        if ($log_arr) {
            db('cc_detail_log')->insertAll($log_arr);
        }
        if ($user_arr) {
            db('user')->batchQuery($user_arr);
        }

        $data = db('booking_log')->where('status',0)->field('id,username,level,credit1')->limit(0,$psize)->select();
    }
    echo '操作完成'.date('Y-m-d H:i:s');
}

booking();

