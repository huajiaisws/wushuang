<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\library\Sms as Smslib;
use app\common\model\User;


/**
 * 手机短信接口
 */
class Sms extends Api
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 发送验证码
     *
     * @param string $mobile 手机号
     * @param string $event 事件名称
     */
    public function send($mobile,$event='register')
    {
        if ($event == 'register') {
            $mobile = $this->request->request("mobile") ? $this->request->request("mobile") : $mobile;
            $event = $this->request->request("event") ? $this->request->request("event") : $event;
        }

        if (!$mobile || !\think\Validate::regex($mobile, "^^1\d{10}$")) {
            $this->error(__('手机号不正确'));
        }
        $last = Smslib::get($mobile, $event);
        if ($last && time() - $last['createtime'] < 60) {
            $this->error(__('发送频繁'));
        }
        $ipSendTotal = \app\common\model\Sms::where(['ip' => $this->request->ip()])->whereTime('createtime', '-1 hours')->count();
        if ($ipSendTotal >= 5) {
            $this->error(__('发送频繁'));
        }
        //
        //if ($event) {
        //    $userinfo = User::getByMobile($mobile);
        //    if ($event == 'register' && $userinfo) {
        //        //已被注册
        //        $this->error(__('已被注册'));
        //    } else if (in_array($event, ['changemobile']) && $userinfo) {
        //        //被占用
        //        $this->error(__('已被占用'));
        //    } else if (in_array($event, ['changepwd', 'resetpwd']) && !$userinfo) {
        //        //未注册
        //        $this->error(__('未注册'));
        //    }
        //}
        $ret = Smslib::send($mobile, NULL, $event);
        if ($ret) {
            if ($event == 'register') {
                $this->success(__('发送成功'));
            }else{
                echo __('发送成功');
            }
        } else {
            if ($event == 'register') {
                $this->error(__('发送失败'));
            }else{
                echo __('发送失败');
            }
        }
    }

    /**
     * 发送验证码
     *
     * @param string $mobile 手机号
     * @param string $event 事件名称
     */
    //public function sendb($mobile)
    //{
    //
    //    if (!$mobile || !\think\Validate::regex($mobile, "^1[3|4|5|6|7|8|9]\d{9}$")) {
    //        $this->error(__('手机号不正确'));
    //    }
    //    $ret = Smslib::sendb($mobile, NULL, '');
    //}

    //订单变动信息
    /*public function sendb($mobile,$event='buy')
    {
        //$mobile = $this->request->request("mobile");
        //$event = $this->request->request("event");
        //$event = $event ? $event : 'buy';

        if (!$mobile || !\think\Validate::regex($mobile, "^^1\d{10}$")) {
            echo __('手机号不正确');
        }
        $ret = Smslib::sendb($mobile, NULL,$event);
        if ($ret) {
            echo __('发送成功');
        } else {
            echo __('发送失败');
        }
    }*/

    /**
     * 发送验证码
     *
     * @param string $mobile 手机号
     * @param string $event 事件名称
     */
    //public function sendc($mobile)
    //{
    //
    //    if (!$mobile || !\think\Validate::regex($mobile, "^1[3|4|5|6|7|8|9]\d{9}$")) {
    //        $this->error(__('手机号不正确'));
    //    }
    //    $ret = Smslib::sendc($mobile, NULL, '');
    //}



    /**
     * 检测验证码
     *
     * @param string $mobile 手机号
     * @param string $event 事件名称
     * @param string $captcha 验证码
     */
    public function check()
    {
        $mobile = $this->request->request("mobile");
        $event = $this->request->request("event");
        $event = $event ? $event : 'register';
        $captcha = $this->request->request("captcha");
        if (!$mobile || !\think\Validate::regex($mobile, "^1[3|4|5|6|7|8|9]\d{9}$")) {
            $this->error(__('手机号不正确'));
        }
        //if ($event) {
        //    $userinfo = User::getByMobile($mobile);
        //    if ($event == 'register' && $userinfo) {
        //        //已被注册
        //        $this->error(__('已被注册'));
        //    } else if (in_array($event, ['changemobile']) && $userinfo) {
        //        //被占用
        //        $this->error(__('已被占用'));
        //    } else if (in_array($event, ['changepwd', 'resetpwd']) && !$userinfo) {
        //        //未注册
        //        $this->error(__('未注册'));
        //    }
        //}
        $ret = Smslib::check($mobile, $captcha, $event);
        if ($ret) {
            $this->success(__('成功'));
        } else {
            $this->error(__('验证码不正确'));
        }
    }

    function juhecurl($url,$params=false,$ispost=0){
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if( $ispost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }


}
