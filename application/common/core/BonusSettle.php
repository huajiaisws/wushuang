<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/25
 * Time: 11:35
 */
namespace app\common\core;
/**
 * 奖金计算
 */

class BonusSettle
{

    /**
     * 推广奖
     * - 得到智能合约的时候触发
     */
    public static function do_tuiguang ($data,$periods){
        $config = \app\common\model\Config::getSetting();
        //表头
        $prefix = config('database.prefix');
        //记录用户修改信息
        $acc_arr = [];
        //记录结算信息
        $js_arr = [];
        $tm = time();

        if ($data) {
            //根据会员的等级取具体的参数
            $user = \db('user_detail')
                ->alias('ud')
                ->join('user u', 'ud.uid=u.id')
                ->where("ud.uid > 0")
                ->column('ud.tjstr, u.username, u.level','ud.uid');

            foreach ($data as $val) {


                $tjstr = $user[$val['uid']]['tjstr'];
                if (empty($tjstr)) return false;

                $bkey = 'f1';
                $tjstr = explode(',', $tjstr);

                foreach ($tjstr as $k=>$v){

                    $tgconfig = []; //根据推荐人的等级获取

                    if (!isset($config['bonus_tgj_level'.$user[$v]['level']])){   //如果会员的等级对应的奖金参数不存在，跳出当前循环
                        continue;
                    }

                    $tgstr = $config['bonus_tgj_level'.$user[$v]['level']];
                    foreach (json_decode($tgstr) as $v2){
                        $tgconfig[] = $v2;
                    }
                    if (!empty($user[$v])){
                        if ($k < count($tgconfig)){
                            $award = $val['amount'] * $tgconfig[$k] * 0.01;
                            $source = '推广奖：'.$user[$v]['username'].'获得了'.$user[$val['uid']]['username'].'的推广奖'.$award.config('site.credit3_text').'；';
                            $params = array('bkey'=>$bkey, 'bval'=>$award);

                            $js_arr[] = [
                                'uid' => $v,
                                'bkey' => $bkey,
                                'award' => $award,
                                'time' => $tm,
                                'periods' => $periods,
                                'params' => $params,
                                'source' => $source
                            ];

                            //累计推广收益
                            if (isset($acc_arr[$v])) {
                                $acc_arr[$v] = ['credit3acc' => $acc_arr[$v]['credit3acc'] + $award];
                            }else{
                                $acc_arr[$v] = ['credit3acc' => $award];
                            }

                        }
                    }
                }
            }
        }

        //批量处理
        if ($acc_arr) {
            $arr = [];
            foreach ($acc_arr as $k=>$v) {
                $arr[] = 'update '.$prefix.'user set credit3acc = credit3acc+'.$v['credit3acc'].' where id='.$k;
            }
            if ($arr) {
                db('user')->batchQuery($arr);
            }
        }
        if ($js_arr) {
            Jsutil::do_js_bonus($js_arr);
        }

    }


    /**
     * 团队奖
     * - 智能合约收益、推广奖、团队奖
     */
    public static function do_tuandui($data, $periods)
    {
        if ($data) {
            $config = \app\common\model\Config::getSetting();
            $bkey = 'f2';
            $db = \db('user_detail');
            $ldb = db('user_level');

            //表头
            $prefix = config('database.prefix');
            //记录用户修改信息
            $acc_arr = [];
            //记录结算信息
            $js_arr = [];
            $tm = time();


            $level = $ldb
                ->where("id",'>',0)
                ->column('level','levelname');

            //根据会员的等级取具体的参数
            $user = $db
                ->alias('ud')
                ->join('user u', 'ud.uid=u.id')
                ->where("u.id > 0")
                ->column('ud.tjstr, u.username, u.level','u.id');

            $tdconfig = config('site.bonus_tdj');

            foreach ($data as $val) {

                $tjstr = $user[$val['uid']]['tjstr'];
                if (empty($tjstr)) return false;

                /*$tdconfig = [];
                foreach (json_decode($config['bonus_tdj']) as $k=>$v){
                    $tdconfig[$level[$k]] = $v;
                }*/

                $tjstr = explode(',', $tjstr);

                $maxlevel = 0;
                foreach ($tjstr as $k=>$v){

                    //必须初级合伙人以上，level >= 3，由于开启极差模式，所以必须等级大于伞下成员
                    if ($user[$v]['level'] >= 3 && $user[$v]['level'] > $maxlevel){
                        if (!isset($tdconfig[$maxlevel])){  //极差
                            $rate = $tdconfig[$user[$v]['level']];
                        }else{
                            $rate = $tdconfig[$user[$v]['level']] - $tdconfig[$maxlevel];
                        }
                        $maxlevel = $user[$v]['level'];

                        $award = $val['amount'] * $rate * 0.01;
                        $source = '团队奖：'.$user[$v]['username'].'获得了'.$user[$val['uid']]['username'].'的团队奖'.$award.config('site.credit3_text').'；';
                        $params = array('bkey'=>$bkey, 'bval'=>$award);

                        $js_arr[] = [
                            'uid' => $v,
                            'bkey' => $bkey,
                            'award' => $award,
                            'time' => $tm,
                            'periods' => $periods,
                            'params' => $params,
                            'source' => $source
                        ];

                        //累计团队收益
                        //$acc_arr[] = 'update '.$prefix.'user set credit3acd = credit3acd+'.$award.' where id='.$v;
                        if (isset($acc_arr[$v])) {
                            $acc_arr[$v] = [
                                'credit3acd' => $acc_arr[$v]['credit3acd'] + $award
                            ];
                        }else{
                            $acc_arr[$v] = [
                                'credit3acd' => $award
                            ];
                        }
                    }
                }
            }

            //批量处理
            if ($acc_arr) {
                $arr = [];
                foreach ($acc_arr as $k=>$v) {
                    $arr[] = 'update '.$prefix.'user set credit3acd = credit3acd+'.$v['credit3acd'].' where id='.$k;
                }
                if ($arr) {
                    db('user')->batchQuery($arr);
                }
            }
            if ($js_arr) {
                Jsutil::do_js_bonus($js_arr);
            }
        }

    }

}