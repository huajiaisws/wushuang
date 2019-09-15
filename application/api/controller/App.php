<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/18
 * Time: 14:24
 */
namespace app\api\controller;

use app\common\controller\Api;

class App extends Api
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    public function info(){
        $path = ROOT_PATH.'public'.config('site.app_package');

        $data = array(
            'app_name' => config('site.app_name'),
            'icon'  => config('site.app_icon'),
            'app_version'  => config('site.app_version'),
            'app_package'  => config('site.app_package'),
            'package_size' => (empty($path) ? 0 : round(filesize(iconv('UTF-8', 'GB2312', $path))/(1024*1024), 2)).'M',
            'app_ios_qrcode' => config('site.app_ios_qrcode')
        );

        $this->success('', $data);
    }

    //系统个别字段的名称
    public function getText(){

        $data = [
            //矿机
            'credit1_text' => __('credit1_text'),
            //区块Mine
            'credit2_text' => __('credit2_text'),
            //矿链
            'credit3_text' => __('credit3_text'),
            //Goge
            'credit4_text' => __('credit4_text'),
            //法币
            'credit5_text' => __('credit5_text'),
            //矿
            'ore_text' => __('ore_text'),

            //抢矿中
            'mine_grab_text' => __('mine_grab_text'),
            //采矿中
            'mining_text' => __('mining_text'),
            //可预约
            'booking_text' => __('booking_text'),
            //已预约
            'booked_text' => __('booked_text'),
            //等待抢矿
            'wait_mine_text'=>__('wait_mine_text'),
            //立即抢矿
            'now_mine_text'=>__('now_mine_text'),
            //抢矿记录
            'mine_grab_record'=>__('mine_grab_record'),
            //抢矿中
            'mine_snatching'=>__('mine_snatching')

        ];
        $this->success('',$data);
    }
}