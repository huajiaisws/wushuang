<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Config;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    /**
     * 查看
     */
//    public function index()
//    {
//        $seventtime = \fast\Date::unixtime('day', -7);
//        $paylist = $createlist = [];
//        for ($i = 0; $i < 7; $i++)
//        {
//            $day = date("Y-m-d", $seventtime + ($i * 86400));
//            $createlist[$day] = mt_rand(20, 200);
//            $paylist[$day] = mt_rand(1, mt_rand(1, $createlist[$day]));
//        }
//        $hooks = config('addons.hooks');
//        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
//        $addonComposerCfg = ROOT_PATH . '/vendor/karsonzhang/fastadmin-addons/composer.json';
//        Config::parse($addonComposerCfg, "json", "composer");
//        $config = Config::get("composer");
//        $addonVersion = isset($config['version']) ? $config['version'] : __('Unknown');
//        // 查詢會員數量
//        $result = db('user')->count();
//        $this->view->assign([
//            'totaluser'        => $result,
//            'totalviews'       => 219390,
//            'totalorder'       => 32143,
//            'totalorderamount' => 174800,
//            'todayuserlogin'   => 321,
//            'todayusersignup'  => 430,
//            'todayorder'       => 2324,
//            'unsettleorder'    => 132,
//            'sevendnu'         => '80%',
//            'sevendau'         => '32%',
//            'paylist'          => $paylist,
//            'createlist'       => $createlist,
//            'addonversion'       => $addonVersion,
//            'uploadmode'       => $uploadmode
//        ]);
//
//        return $this->view->fetch();
//    }


    public function index()
    {
//        $seventtime = \fast\Date::unixtime('day', -7);
//        $paylist = $createlist = [];
//        for ($i = 0; $i < 7; $i++)
//        {
//            $day = date("Y-m-d", $seventtime + ($i * 86400));
//            $createlist[$day] = mt_rand(20, 200);
//            $paylist[$day] = mt_rand(1, mt_rand(1, $createlist[$day]));
//        }
//        print_r($seventtime);
//        print_r($createlist);
//        print_r($paylist);die;
        $hooks = config('addons.hooks');
        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
        $addonComposerCfg = ROOT_PATH . '/vendor/karsonzhang/fastadmin-addons/composer.json';
        Config::parse($addonComposerCfg, "json", "composer");
        $config = Config::get("composer");
        $addonVersion = isset($config['version']) ? $config['version'] : __('Unknown');
        // 查詢會員數量
        $result = db('user')->count();

        $user = db('user')->field('id,createtime')->select();
        $daytime= strtotime(date('Ymd',time()));
        $dayuser=0;
        $w=date('w',time());
        $weektime=strtotime(date('Ymd',time()-($w-1)*86400));
        $weekuser=0;
        $d=date('d',time());
        $monthtime=strtotime(date('Ymd',time()-($d-1)*86400));
        $monthuser=0;
        $createlist = [];

        $days =date('t');

        foreach ($user as $k=>$v){
            // 会员日增加量
            if($v['createtime']<time() && $v['createtime']>$daytime){
                $dayuser++;
            }
            // 会员周增加量
            if($v['createtime']<time() && $v['createtime']>$weektime){
                $weekuser++;
            }
            // 会员周增加量
            if($v['createtime']<time() && $v['createtime']>$monthtime){
                $monthuser++;
            }
        }
        // 会员折线图数据



        for ($i = 0; $i < $days; $i++)
        {
            $day = date("Y-m-d", $monthtime + ($i * 86400));
            $dayt=strtotime($day);
            $daye = $dayt+86399;
            $createlist[$day]=0;
            foreach ($user as $k=>$v){
                if($v['createtime']>$dayt && $v['createtime']<$daye){
                    $createlist[$day]++;
                }
            }
        }



//        for ($i = 0; $i < 7; $i++)
//        {
//            $day = date("Y-m-d", $weektime + ($i * 86400));
//            $dayt=strtotime($day);
//            $daye = $dayt+86399;
//            $createlist[$day]=0;
//            foreach ($user as $k=>$v){
//                if($v['createtime']>$dayt && $v['createtime']<$daye){
//                    $createlist[$day]++;
//                }
//            }
//        }

//        print_r($createlist);die;

        $this->view->assign([
            'totaluser'        => $result,
            'totalviews'       => 219390,
            'totalorder'       => 32143,
            'totalorderamount' => 174800,
            'todayuserlogin'   => 321,
            'todayusersignup'  => $dayuser,
            'weekusersignup'  => $weekuser,
            'monthusersignup'  => $monthuser,
            'todayorder'       => 2324,
            'unsettleorder'    => 132,
            'sevendnu'         => '80%',
            'sevendau'         => '32%',
//            'paylist'          => $paylist,
            'createlist'       => $createlist,
            'addonversion'       => $addonVersion,
            'uploadmode'       => $uploadmode
        ]);

        return $this->view->fetch();
    }

}
