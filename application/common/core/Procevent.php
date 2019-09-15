<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/25
 * Time: 11:51
 */
namespace app\common\core;
/*******************
 * 奖金发放
 */

class Procevent
{
    /**
     * 触发奖金
     */
    public static function dsell_event($rs, $type){
        $periods = Jsutil::getPeriods()+1;

        //判断该奖金是否开启
        $ops = config('site.bonus_open');

        switch ($type){
            case 'tgj': #推广奖
                if ($ops && in_array($type,$ops)) {
                    //开启
                    BonusSettle::do_tuiguang($rs,$periods);
                }
                self::do_grant_bonus();
                break;
            case 'tdj': #团队奖
                if ($ops && in_array($type,$ops)) {
                    //开启
                    BonusSettle::do_tuandui($rs, $periods);
                }
                self::do_grant_bonus();
                break;
        }
    }


    /**
     * 结算奖金
     * -credit3：奖金发放的币种
     */
    public static function do_grant_bonus()
    {
        //限制为本次执行如果没有执行完的话，其他人不能调用，等到本次执行完成在执行下一个要执行的
        $redis = new \Redis();
        $redis->connect(config('redis.host'),config('redis.port'));
        if ($redis->get('grant') == 1) {
            sleep(1);
            self::do_grant_bonus();
            return ;
        }else{
            $redis->set('grant',1);
        }

        $psize = 1000;

        $bonus_list = db('bonus')
            ->field('id, uid, netincome, source')
            ->where('done=0 and state=0')->limit(0,$psize)->select();
        //表头
        $prefix = config('database.prefix');
        $user = db('user')->where('id','>',0)->column('id,username,level','id');

        while($bonus_list){

            $bonus_arr = [];
            $log_arr = [];
            $user_arr = [];
            $tm = time();

            foreach ($bonus_list as $bonus){
                if (isset($user_arr[$bonus['uid']])) {
                    $user_arr[$bonus['uid']] = [
                        'credit3' => $user_arr[$bonus['uid']]['credit3'] + $bonus['netincome']
                    ];
                }else{
                    $user_arr[$bonus['uid']] = [
                        'credit3' => $bonus['netincome']
                    ];
                }

                $log_arr[] = [
                    'username' => $user[$bonus['uid']]['username'],
                    'type' => 'credit3',
                    'num' => $bonus['netincome'],
                    'remark' => $bonus['source'],
                    'createtime' => $tm,
                    'updatetime' => $tm
                ];

                $bonus_arr[] = 'update '.$prefix.'bonus set granttime='.$tm.',done=1 where done=0 and state=0 and id='.$bonus['id'];
            }

            //批量处理
            if ($user_arr) {
                $arr = [];
                foreach ($user_arr as $k=>$v) {
                    $arr[] = 'update '.$prefix.'user set credit3 = credit3+'.$v['credit3'].' where id='.$k;
                }
                if ($arr) {
                    db('user')->batchQuery($arr);
                }
            }
            if ($bonus_arr) {
                db('bonus')->batchQuery($bonus_arr);
            }
            if ($log_arr) {
                db('cc_detail_log')->insertAll($log_arr);
            }

            $bonus_list = db('bonus')
                ->field('id, uid, netincome, source')
                ->where('done=0 and state=0')->limit(0,$psize)->select();
        }

        $redis->del('grant');
        $redis->close();
    }

}