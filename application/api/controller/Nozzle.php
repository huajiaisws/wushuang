<?php
/**
 * 测试使用的接口.
 * User: admin
 * Date: 2019/7/17
 * Time: 10:24
 */

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 示例接口
 */
class Nozzle extends Api
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = '*';
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = '*';

    //处理抢购数据
    public function handle(){
        require_once ROOT_PATH.'/handle.php';
    }
    //检测自动确认
    public function checkfinish(){
        require_once ROOT_PATH.'/checkfinish.php';
    }
    //检测到期
    public function checkore(){
        require_once ROOT_PATH.'/checkore.php';
    }
    //检测超过付款时间没付款的
    public function checkpaytime(){
        require_once ROOT_PATH.'/checkpaytime.php';
    }
    //结算收益
    public function getprofit(){
        require_once ROOT_PATH.'/getprofit.php';
    }
    //参数日志
    public function mininglogs(){
        require_once ROOT_PATH.'/mininglogs.php';
    }
    //发送短信
    public function sendsms(){
        require_once ROOT_PATH.'/sendsms.php';
    }

    //预约退还
    public function booking(){
        require_once ROOT_PATH.'/booking.php';
    }

}