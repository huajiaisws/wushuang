<?php

namespace app\api\controller;

use app\common\behavior\Walletapi;
use app\common\controller\Api;
use app\common\library\Auth;
use app\common\library\Ems;
use app\common\library\Sms;
use app\common\model\Config;
use app\common\model\Levelup;
use app\common\model\User as CommonUser;
use EasyWeChat\MiniProgram\QRCode\QRCode;
use fast\Random;
use think\Log;
use think\Validate;
use think\Db;
use think\Model;

use app\common\controller\Frontend;

use think\Cookie;
use think\Hook;
use think\Session;
use think\Exception;
use think\Cache;
/**
 * 会员接口
 */
class User extends Api
{

    protected $noNeedLogin = ['login','register','getbackpass','mobilelogin','third','resetpwd','getbackpass'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();

        $auth = $this->auth;
        //监听注册登录注销的事件
        Hook::add('user_login_successed', function ($user) use ($auth) {
            $expire = input('post.keeplogin') ? 30 * 86400 : 0;
            Cookie::set('uid', $user->id, $expire);
            Cookie::set('token', $auth->getToken(), $expire);
        });
        Hook::add('user_register_successed', function ($user) use ($auth) {
            Cookie::set('uid', $user->id);
            Cookie::set('token', $auth->getToken());
        });
        Hook::add('user_delete_successed', function ($user) use ($auth) {
            Cookie::delete('uid');
            Cookie::delete('token');
        });
        Hook::add('user_logout_successed', function ($user) use ($auth) {
            Cookie::delete('uid');
            Cookie::delete('token');
        });

    }

    /**
     * 会员中心
     */
    public function index()
    {
        $this->success('', ['welcome' => $this->auth->nickname]);
    }

//    /**
//     * 会员登录
//     *
//     * @param string $account 账号
//     * @param string $password 密码
//     *
//     */
//    public function login123()
//     {
//         $account = $this->request->request('account');
//         $password = $this->request->request('password');
//         if (!$account || !$password)
//         {
//             $this->error(__('Invalid parameters'));
//         }
//         $ret = $this->auth->login($account, $password);
//         if ($ret)
//         {
//             $data = ['userinfo' => $this->auth->getUserinfo()];
////             session('username', $data['userinfo']['username']);
////             session('id', $data['userinfo']['id']);
////             session('mobile', $data['userinfo']['mobile']);
//             $this->success(__('Logged in successful'), $data);
//         }
//         else
//         {
//             $this->error($this->auth->getError());
//         }
//     }


    public function login()
    {
        $url = $this->request->request('url');
//        if ($this->auth->id)
//            $this->success(__('You\'ve logged in, do not login again'), $url);
        if ($this->request->isPost()) {
//            Session::start();

            $account = $this->request->post('account');
            $password = $this->request->post('password');
            $keeplogin = (int)$this->request->post('keeplogin');
            $token = $this->request->post('__token__');
            $identifier = $this->request->post('identifier');
            $captcha = $this->request->post('captcha');
            $rule = [
                'account'   => 'require|length:3,50',
                'password'  => 'require|length:6,30',
//                '__token__' => 'token',
//                'identifier' => 'require',
//                'captcha'   => 'require',
            ];

            $msg = [
                'account.require'  => 'Account can not be empty',
                'account.length'   => 'Account must be 3 to 50 characters',
                'password.require' => 'Password can not be empty',
                'password.length'  => 'Password must be 6 to 30 characters',
                //'identifier.length'  => 'Identifier can not be empty',
                //'captcha.length'  => 'Captcha can not be empty',
            ];
            $data = [
                'account'   => $account,
                'password'  => $password,
//                '__token__' => $token,
//                'identifier' => $identifier,
//                'captcha' => $captcha,
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                $this->error(__($validate->getError()), null, ['token' => $this->request->token()]);
                return FALSE;
            }

            //if (strtolower($captcha) != Cache::get($identifier)){  //验证码不同
            //    $this->error('图形验证码输入错误');
            //}

            $ret = $this->auth->login($account, $password);

            if ($ret)
            {
                $synchtml = '';
                ////////////////同步到Ucenter////////////////
                if (defined('UC_STATUS') && UC_STATUS) {
                    $uc = new \addons\ucenter\library\client\Client();
                    $synchtml = $uc->uc_user_synlogin($this->auth->id);
                }
//                $this->success(__('Logged in successful') . $synchtml, $url ? $url : url('user/index'));

                $info = $this->auth->getUserinfo();
                $extra = \db('user')
                    ->alias('u')
                    ->field('u.level, d.isreal, d.creditid, l.levelname,d.creditid,d.realname')
                    ->join('user_detail d', 'd.uid=u.id', 'left')
                    ->join('user_level l', 'l.level=u.level')
                    ->where("u.id={$info['id']}")
                    ->find();

                $info = array_merge($info, $extra, ['token'=>$this->auth->getToken()]);

                $data = ['userinfo' => $info];
//                session('username', $data['userinfo']['username']);
//                session('id', $data['userinfo']['id']);
//                session('mobile', $data['userinfo']['mobile']);

//                Session::clear();   //清空session
                $this->success(__('Logged in successful'), $info);
            }
            else
            {
                $this->error($this->auth->getError(), null, ['token' => $this->request->token()]);
            }
        }
        //判断来源
        $referer = $this->request->server('HTTP_REFERER');
        if (!$url && (strtolower(parse_url($referer, PHP_URL_HOST)) == strtolower($this->request->host()))
            && !preg_match("/(user\/login|user\/register)/i", $referer)) {
            $url = $referer;
        }
    }


    /**
     * 手机验证码登录
     *
     * @param string $mobile 手机号
     * @param string $captcha 验证码
     */
    public function mobilelogin()
    {
        $mobile = $this->request->request('mobile');
        $captcha = $this->request->request('captcha');
        if (!$mobile || !$captcha)
        {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$"))
        {
            $this->error(__('Mobile is incorrect'));
        }
        if (!Sms::check($mobile, $captcha, 'mobilelogin'))
        {
            $this->error(__('Captcha is incorrect'));
        }
        $user = \app\common\model\User::getByMobile($mobile);
        if ($user)
        {
            //如果已经有账号则直接登录
            $ret = $this->auth->direct($user->id);
        }
        else
        {
            $ret = $this->auth->register($mobile, Random::alnum(), '', $mobile, []);
        }
        if ($ret)
        {
            Sms::flush($mobile, 'mobilelogin');
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Logged in successful'), $data);
        }
        else
        {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 注册会员
     *
     * @param string $username 用户名
     * @param string $mobile 手机号
     * @param number $captcha 验证码
     * 登陆密码、支付密码、
     */
//    public function register()
//    {
//
////        Session::start();
//        $username = $this->request->request('username');
//        $mobile =  $this->request->request('mobile');
//        $password =  $this->request->request('password');
//        $paypwd =  $this->request->request('paypwd');
//        $captcha = (int)$this->request->request('captcha');
//        $imgcaptcha = $this->request->request('imgcaptcha');
//        $identifier = $this->request->request('identifier');
//
//        if (strtolower($imgcaptcha) != Cache::get($identifier)){
//            $this->error('图形验证码输入错误');
//        }
//
//        $event = 'register';
//        // 获取推荐人id
//        $tjid =  $this->request->request('tjjr');
//        if (!$username)
//        {
//            $this->error(__('Invalid parameters'));
//        }
//        if ($mobile && !Validate::regex($mobile, "^1[3|4|5|6|7|8|9]\d{9}$"))
//        {
//            $this->error(__('Mobile is incorrect'));
//        }
//        $repas = preg_match("/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,15}$/",$password,$matches);
//        if($repas == 0){
//            $this->error('密码必须为6-15位的数字和字母');
//        }
//        if (!Validate::regex($paypwd , "^\d{6}$"))
//        {
//            $this->error(__('必须为6位数字密码'));
//        }
////        $check = true;
//        $check = Sms::check($mobile, $captcha,$event,$password,$paypwd);
//        if($check){
//            $ret = $this->auth->register($username,$mobile, $tjid, $password, $paypwd);
//
//            //注册成功，赠送矿机
//            //获取配置参数
//            $sys = Config::getSetting();
//            $reg_jf = $sys['reg_send_jf'];
//            if ($reg_jf > 0) {
//                setCc($this->auth->username,'credit1',$reg_jf,'注册成功，赠送 '.$reg_jf.' 矿机');
//                db('user')->where('username',$this->auth->username)->update(['credit1acc'=>$reg_jf]);
//                Levelup::autolevelup($this->auth->id);
//            }
//
//        }else{
//            $this->error(__('验证码输入错误,请重新输入'));
//        }
//
//        if ($ret)
//        {
////            Session::clear();   //清空session
//            $this->success(__('Sign up successful'), null);
//        }
//        else
//        {
//            $this->error($this->auth->getError());
//        }
//    }

    public function register()
    {
//        Session::start();
        $username = $this->request->request('username');
        $mobile =  $this->request->request('mobile');
        $password =  $this->request->request('password');
        $paypwd =  $this->request->request('paypwd');
        $captcha = (int)$this->request->request('captcha');
        $imgcaptcha = $this->request->request('imgcaptcha');
        $identifier = $this->request->request('identifier');

        if (strtolower($imgcaptcha) != Cache::get($identifier)){
            $this->error('图形验证码输入错误');
        }

        $event = 'register';
        // 获取推荐人id
        $tjid =  $this->request->request('tjjr');
        if (!$username)
        {
            $this->error(__('Invalid parameters'));
        }
        if ($mobile && !Validate::regex($mobile, "^1\d{10}$"))
        {
            $this->error(__('Mobile is incorrect'));
        }
        $repas = preg_match("/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,15}$/",$password,$matches);
        if($repas == 0){
            $this->error('密码必须为6-15位的数字和字母');
        }
        if (!Validate::regex($paypwd , "^\d{6}$"))
        {
            $this->error(__('必须为6位数字密码'));
        }
//        $check = true;
        $check = Sms::check($mobile, $captcha,$event,$password,$paypwd);

        if($check){
            $ret = $this->auth->register($username,$mobile, $tjid, $password, $paypwd);
        }else{
            $this->error(__('验证码输入错误,请重新输入'));
        }

        if ($ret)
        {
            $data = ['userinfo' => $this->auth->getUserinfo()];

            //注册成功，赠送矿机
            //获取配置参数
            $sys = Config::getSetting();
            $reg_jf = $sys['reg_send_jf'];
            if ($reg_jf > 0) {
                setCc($data['userinfo']['username'],'credit1',$reg_jf,'注册成功，赠送 '.$reg_jf.' '.config('site.credit1_text'));
                db('user')->where('username',$data['userinfo']['username'])->update(['credit1acc'=>$reg_jf]);
                //触发升级
                Levelup::autolevelup($data['userinfo']['id']);
            }

//            Session::clear();   //清空session
            $this->success(__('Sign up successful'), null);
        }
        else
        {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        //注销本站
        $this->auth->logout();
        $synchtml = '';
        ////////////////同步到Ucenter////////////////
        if (defined('UC_STATUS') && UC_STATUS) {
            $uc = new \addons\ucenter\library\client\Client();
            $synchtml = $uc->uc_user_synlogout();
        }
        $this->success(__('Logout successful') . $synchtml, url('user/index'));
    }


    /**
     * 注销登录
     */
    /*public function logout()
    {
        if( $this->auth->logout()){
            session(null);
        }
        $this->success(__('Logout successful'));

    }*/

    /**
     * 修改会员个人信息
     *
     * @param string $avatar 头像地址
     * @param string $username 用户名
     * @param string $nickname 昵称
     * @param string $bio 个人简介
     */
    public function profile()
    {
        $user = $this->auth->getUser();
        $username = $this->request->request('username');
        $nickname = $this->request->request('nickname');
        $bio = $this->request->request('bio');
        $avatar = $this->request->request('avatar');
        $exists = \app\common\model\User::where('username', $username)->where('id', '<>', $this->auth->id)->find();
        if ($exists)
        {
            $this->error(__('Username already exists'));
        }
        $user->username = $username;
        $user->nickname = $nickname;
        $user->bio = $bio;
        $user->avatar = $avatar;
        $user->save();
        $this->success();
    }

    /**
     * 修改邮箱
     *
     * @param string $email 邮箱
     * @param string $captcha 验证码
     */
    public function changeemail()
    {
        $user = $this->auth->getUser();
        $email = $this->request->post('email');
        $captcha = $this->request->request('captcha');
        if (!$email || !$captcha)
        {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::is($email, "email"))
        {
            $this->error(__('Email is incorrect'));
        }
        if (\app\common\model\User::where('email', $email)->where('id', '<>', $user->id)->find())
        {
            $this->error(__('Email already exists'));
        }
        $result = Ems::check($email, $captcha, 'changeemail');
        if (!$result)
        {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->email = 1;
        $user->verification = $verification;
        $user->email = $email;
        $user->save();

        Ems::flush($email, 'changeemail');
        $this->success();
    }

    /**
     * 修改手机号
     *
     * @param string $email 手机号
     * @param string $captcha 验证码
     */
    public function changemobile()
    {
        $user = $this->auth->getUser();
        $mobile = $this->request->request('mobile');
        $captcha = $this->request->request('captcha');
        if (!$mobile || !$captcha)
        {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$"))
        {
            $this->error(__('Mobile is incorrect'));
        }
        if (\app\common\model\User::where('mobile', $mobile)->where('id', '<>', $user->id)->find())
        {
            $this->error(__('Mobile already exists'));
        }
//        $result = true;
        $result = Sms::check($mobile, $captcha, 'changemobile');
        if (!$result)
        {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->mobile = 1;
        $user->verification = $verification;
        $user->mobile = $mobile;
        $user->save();

        Sms::flush($mobile, 'changemobile');
        $this->success();
    }

    /**
     * 第三方登录
     *
     * @param string $platform 平台名称
     * @param string $code Code码
     */
    public function third()
    {
        $url = url('user/index');
        $platform = $this->request->request("platform");
        $code = $this->request->request("code");
        $config = get_addon_config('third');
        if (!$config || !isset($config[$platform]))
        {
            $this->error(__('Invalid parameters'));
        }
        $app = new \addons\third\library\Application($config);
        //通过code换access_token和绑定会员
        $result = $app->{$platform}->getUserInfo(['code' => $code]);
        if ($result)
        {
            $loginret = \addons\third\library\Service::connect($platform, $result);
            if ($loginret)
            {
                $data = [
                    'userinfo'  => $this->auth->getUserinfo(),
                    'thirdinfo' => $result
                ];
                $this->success(__('Logged in successful'), $data);
            }
        }
        $this->error(__('Operation failed'), $url);
    }

    /**
     * 重置密码
     *
     * @param string $mobile 手机号
     * @param string $newpassword 新密码
     * @param string $captcha 验证码
     */
    public function resetpwd()
    {
        $type = $this->request->request("type");
        $mobile = $this->request->request("mobile");
        $email = $this->request->request("email");
        $newpassword = $this->request->request("newpassword");
        $captcha = $this->request->request("captcha");
        if (!$newpassword || !$captcha)
        {
            $this->error(__('Invalid parameters'));
        }
        if ($type == 'mobile')
        {
            if (!Validate::regex($mobile, "^1\d{10}$"))
            {
                $this->error(__('Mobile is incorrect'));
            }
            $user = \app\common\model\User::getByMobile($mobile);
            if (!$user)
            {
                $this->error(__('User not found'));
            }
            $ret = Sms::check($mobile, $captcha, 'resetpwd');
            if (!$ret)
            {
                $this->error(__('Captcha is incorrect'));
            }
            Sms::flush($mobile, 'resetpwd');
        }
        else
        {
            if (!Validate::is($email, "email"))
            {
                $this->error(__('Email is incorrect'));
            }
            $user = \app\common\model\User::getByEmail($email);
            if (!$user)
            {
                $this->error(__('User not found'));
            }
//            $ret = Ems::check($email, $captcha, 'resetpwd');
//            if (!$ret)
//            {
//                $this->error(__('Captcha is incorrect'));
//            }
//            Ems::flush($email, 'resetpwd');
        }
        //模拟一次登录
        $this->auth->direct($user->id);
        $ret = $this->auth->changepwd($newpassword, '', true);
        if ($ret)
        {
            $this->success(__('Reset password successful'));
        }
        else
        {
            $this->error($this->auth->getError());
        }
    }


    /**
     * 实名认证
     * @param string
     * @param string
     * @param string
     */
    public function changeisreal()
    {
        $id = $this->request->request('id');  // 用户id
        $update['creditid'] = $this->request->request('creditid'); // 身份证
        $update['realname'] = $this->request->request('realname'); // 真实姓名
        $paypwd = $this->request->request('paypwd');
        $check = $this->auth->valiPay($id);
        $paypwd = Auth::getEncryptPassword($paypwd,$check[0]['salt']);
        try{
            CommonUser::creditidCheck($update['creditid']);
        }catch (\Exception $e){
            $this->error($e->getMessage());
        }

        if ($paypwd != $check[0]['paypwd']){
            $this->error(__('交易密码输入错误,请重新输入'));
        }else{
            $update['isreal'] = 1 ; //已实名
            $ret = $this->auth->setDetail($id,$update);
        }
        if($ret){
            $this->success(__('实名验证成功'), $ret);
        }else{
            $this->error(__('实名验证失败'));
        }
    }

    /**
     * 个人信息
     * @param string
     */
    public function getinfo()
    {
        $info = $this->auth->getReal($this->auth->id);
        if($info){
            $this->success(__('查询成功'), $info);
        }else{
            $this->error(__('查询失败'));
        }
    }
    /**
     * 用户币种查询
     * @param string
     */
    public function selecc()
    {
        $info = $this->auth->getCc($this->auth->id);
        if($info){
            $this->success(__('查询成功'), $info);
        }else{
            $this->error(__('查询失败'));
        }
    }

    /**
     * 用户计算机查询
     * @param string
     */
    public function getmach()
    {
        $info = $this->auth->getMachi($this->auth->id);
        if($info){
            $this->success(__('查询成功'), $info);
        }else{
            $this->error(__('查询失败'));
        }
    }

    /**
     * 获取地区
     * @param string
     */
    public function getcity(){
        $province = \db('user_province')
            ->select();
        // 城市
        foreach($province as $key=>$value){
            $city = \db('user_city')
                ->where('parent_code',$value['code'])
                ->select();
            foreach ($city as $k => $v){
                $province[$key]['children'][] = $v['text'];
            }
        }
        if($province){
            $this->success(__('查询成功'), $province);
        }else{
            $this->error(__('查询失败'));
        }
    }
    /**
     * 获取银行
     * @param string
     */
    public function getbank(){
        $bank = \db('user_bank')
            ->select();
        if($bank){
            $this->success(__('查询成功'), $bank);
        }else{
            $this->error(__('查询失败'));
        }
    }
    /**
     * 获取商场背景图片
     * @param string
     */
    public function getimg(){
        $img = \db('config')
            ->field('value')
            ->where('name','backimg')
            ->select();
        if($img){
            $this->success(__('查询成功'), $img);
        }else{
            $this->error(__('查询失败'));
        }
    }
    /**
     * 获取在线客服信息
     * @param string
     */
    public function getcustom(){
        $custom = \db('config')
            ->field('value')
            ->where('name','custom')
            ->select();
        if($custom){
            $this->success(__('查询成功'), $custom);
        }else{
            $this->error(__('查询失败'));
        }
    }
    /**
     * 获取区块浏览器地址
     * @param string
     */
    public function getbrowser(){
        $custom = \db('config')
            ->field('value')
            ->where('name','browser')
            ->select();
        if($custom){
            $this->success(__('查询成功'), $custom);
        }else{
            $this->error(__('查询失败'));
        }
    }
    /**
     * 找回密码
     * @param string
     */
    public function getbackpass(){
        //$mobile = $this->request->request('mobile');
        $mobile = $this->auth->mobile;
        $newpassword  = $this->request->request('newpassword');
        $captcha = $this->request->request('captcha');
        $event = 'register';
        $repas = preg_match("/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,15}$/",$newpassword,$matches);
        if($repas == 0){
            $this->error('密码必须为6-15位的数字和字母');
        }
        // 验证码
        $check = Sms::check($mobile, $captcha,$event);
        if($check){
            $salt = $this->auth->salt;
            $newpassword = Auth::getEncryptPassword($newpassword, $salt);
            $ret = $this->auth->getpwd($newpassword,$mobile);
        }else{
            $this->error(__('验证码输入错误,请重新输入'));
        }
        if($ret){
            $this->success(__('密码重置成功'), $ret);
        }else{
            $this->error(__('密码重置失败'));
        }
    }
    /**
     * 修改密码
     * @param string
     */
    public function uppass(){
        //$salt = $this->request->request('salt');
        //$mobile = $this->request->request('mobile');
        $salt = $this->auth->salt;
        $mobile = $this->auth->mobile;
        $newpassword  = $this->request->request('newpassword');
        $newpassword_b  = $this->request->request('newpassword_b');
        $captcha = $this->request->request('captcha');
        $event = 'register';
        $repas = preg_match("/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,15}$/",$newpassword,$matches);
        if($repas == 0){
            $this->error('密码必须为6-15位的数字和字母');
        }
        if ($newpassword !== $newpassword_b){
            $this->error(__('两次密码输入不一致'));
        }else{
            $newpassword = Auth::getEncryptPassword($newpassword, $salt);
        }
        // 验证码
        //$check = true;
        $check = Sms::check($mobile, $captcha,$event);
        $ret = null;
        if($check){
            $ret = $this->auth->getpwd($newpassword,$mobile);
        }else{
            $this->error(__('验证码输入错误,请重新输入'));
        }
        if($ret){
            $this->success(__('密码修改成功'), $ret);
        }else{
            $this->error(__('密码修改失败'));
        }
    }

    /**
     * 修改交易密码
     * @param string
     */
    public function uppaypsw(){
        //$id = $this->request->request('id');
        $id = $this->auth->id;
        //$salt = $this->request->request('salt');
        $salt = $this->auth->salt;
        //$mobile = $this->request->request('mobile');
        $mobile = $this->auth->mobile;
        $paypwd = $this->request->request('paypwd');
        $paypwd_b = $this->request->request('paypwd_b');
        $captcha = $this->request->request('captcha');
        $event = 'register';
        if (!Validate::regex($paypwd , "^\d{6}$"))
        {
            $this->error(__('必须为6位数字密码'));
        }
        if($paypwd != $paypwd_b)
        {
            $this->error(__('两次交易密码输入不一致'));
        }
        // 验证码
//        $check = true;
        $check = Sms::check($mobile, $captcha,$event);
        if($check){
            $res = $this->auth->uppaypwd($id, Auth::getEncryptPassword($paypwd, $salt));
        }else{
            $this->error(__('验证码输入错误,请重新输入'));
        }
        if($res){
            $this->success(__('交易密码修改成功'), $res);
        }else{
            $this->error(__('交易密码修改失败'));
        }
    }


    /**
     * 支付信息
     * @param string
     */
    public function payinfo(){
        $id = $this->auth->id;
        //$id = $this->request->request('id');
        $info = $this->auth->getpayinfo($id); // 获取支付信息
        if($info){
            $this->success(__('支付信息查询成功'), $info);
        }else{
            $this->error(__('支付信息查询失败'));
        }
    }
    /**
     * 支付宝修改
     * @param string
     */
    public function uppay(){
        $id = $this->auth->id;
        if (!$id) {
            $this->error(__('请登录'));
        }
        $alipayname = $this->request->request('alipayname');
        $alipayact = $this->request->request('alipayact');
        $alipayurl = $this->request->request('alipayurl');
        $paypwd = $this->request->request('paypwd');

        $check = $this->auth->valiPay($id);

        $paypwd = Auth::getEncryptPassword($paypwd,$check[0]['salt']);
        if ($paypwd != $check[0]['paypwd']){
            $this->error(__('交易密码输入错误,请重新输入'));
        }else{
            $res = $this->auth->uppayinfo($id,$alipayname,$alipayact,$alipayurl);
        }
        if($res == 1 ||$res == 0 ){
            $this->success(__('绑定成功'), $res);
        }else{
            $this->error(__('绑定失败'));
        }
    }


    /**
     * 微信修改
     * @param string
     */
    public function upwechar(){
        $id = $this->auth->id;
        if (!$id) {
            $this->error(__('请登录'));
        }
        $wechaname = $this->request->request('wechaname');
        $wechatact = $this->request->request('wechatact');
        $wechaturl = $this->request->request('wechaturl');

        $paypwd = $this->request->request('paypwd');
        $check = $this->auth->valiPay($id);
        $paypwd = Auth::getEncryptPassword($paypwd,$check[0]['salt']);
        if ($paypwd != $check[0]['paypwd']){
            $this->error(__('交易密码输入错误,请重新输入'));
        }else{
            $res = $this->auth->upwecha($id,$wechaname,$wechatact,$wechaturl);
        }
        if($res == 1 ||$res == 0 ){
            $this->success(__('绑定成功'), $res);
        }else{
            $this->error(__('绑定失败'));
        }
    }
    /**
     * 银行卡修改
     * @param string
     */
    public function upbank(){

        $id = $this->auth->id;
        //$id = $this->request->request('id');
        $bank = $this->request->request('bank');
        $bankname = $this->request->request('bankname');
        $bankuname = $this->request->request('bankuname');
        $bankact = $this->request->request('bankact');
        $paypwd = $this->request->request('paypwd');
        $check = $this->auth->valiPay($id,$paypwd);
        $paypwd = Auth::getEncryptPassword($paypwd,$check[0]['salt']);
        if ($paypwd != $check[0]['paypwd']){
            $this->error(__('交易密码输入错误,请重新输入'));
        }else{
            $res = $this->auth->upbank($id, $bank, $bankname, $bankuname, $bankact);
        }

        if($res == 1 ||$res == 0 ){
            $this->success(__('绑定成功'), $res);
        }else{
            $this->error(__('绑定失败'));
        }

    }





    /**
     * 上传图片
     */
    public function upload()
    {
        $img = $this->request->post('image');
        //图片路径地址
        $basedir = ROOT_PATH.'public'.DS.'uploads';
        $fullpath = $basedir;
        if(!is_dir($fullpath)){
            mkdir($fullpath,0777,true);
        }
        $types = empty($types)? array('jpg', 'gif', 'png', 'jpeg'):$types;
        $img = str_replace(array('_','-'), array('/','+'), $img);
        $b64img = substr($img, 0,100);
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $b64img, $matches))
        {
            $type = $matches[2];
            if(!in_array($type, $types)){
                $this->error('图片格式不正确，只支持 jpg、gif、png、jpeg哦！');
            }
            $img = str_replace($matches[1], '', $img);
            $img = base64_decode($img);
            $photo = '/'.md5(date('YmdHis').rand(1000, 9999)).'.'.$type;
            file_put_contents($fullpath.$photo, $img);
            $this->success('','/uploads'.$photo);
        }
        $this->error('请选择要上传的图片');
    }

    /**
     * 矿的详情
     */
    public function fa_block(){
        $block = \db('block_ore')
            ->alias('a')
            ->field('a.*,b.*')
            ->join('block_ore_level b','a.level = b.level','LEFT')
            ->where('a.level = b.level')
            ->select();
        if($block){
            $this->success(__('查询成功'), $block);
        }else{
            $this->error(__('查询失败'));
        }
    }

    /**
     * 查询用户币种数额
     */
    public function creditnum(){
        $id = $this->auth->id;
        $credi = \db('user')
            // credit1 矿机,credit2 区块mine,credit3 矿链,credit4 DOGE币,credit5 法币(收益)
            ->field('credit1,credit2,credit3,credit4,credit5')
            ->where('id',$id)
            ->select();
        if($credi){
            $this->success(__('查询成功'), $credi);
        }else{
            $this->error(__('查询失败'));
        }
    }

    /**
     * 我的团队
     */
    public function team()
    {
        $uid = $this->auth->id;
        $user = \db('user_detail')->where("uid={$uid}")->find();
        if (!$user){
            $this->error('用户不存在');
        }

        //直推、间推人
        $data = [];
        $tjusers = \db()->query("select u.mobile,u.id, u.username, ud.tjdept,ud.tjstr, u.createtime from fa_user_detail ud left join fa_user u on u.id = ud.uid where find_in_set(\"{$uid}\", ud.tjstr) order by u.createtime");

        $data['count'] = count($tjusers);

        $tjusers_b = [];
        $tjusers_b_b = [];
        foreach ($tjusers as $k=>$v){
            $res = explode(',', $v['tjstr']);
            if($res[0] == $uid){
                $tjusers_b[$k]['username'] = $v['username'];
                $tjusers_b[$k]['mobile'] = $v['mobile'];
            }else{
                $tjusers_b_b[$k]['username'] = $v['username'];
                $tjusers_b_b[$k]['mobile'] = $v['mobile'];
            }

        }
        $data['zt'] = $tjusers_b;
        $data['jt'] = $tjusers_b_b;
        $data['zt_num'] = db('user_detail')->where('tjid',$uid)->count();
        $data['jt_num'] = db('user_detail')->where('find_in_set('.$uid.',tjstr)')->where('tjid','>',$uid)->count();

        $this->success('', $data);
    }

    /**
     * 获取币种
     */
    public function getUserCoin()
    {
        $uid = $this->auth->id;
        $user = \db('user')->where(['id'=>$uid])->field('username,credit1,credit2,credit3,credit4,credit5')->find();
        //$doge = db('ore_order')->where('buy_username',$user['username'])->sum('credit4');
        if($user){
            $data = array();
            $data['credit1'] = ['money'=>$user['credit1'],'text'=>config('site.credit1_text')];
            $data['credit2'] = ['money'=>$user['credit2'],'text'=>config('site.credit2_text')];
            $data['credit3'] = ['money'=>$user['credit3'],'text'=>config('site.credit3_text')]; //矿链
            $data['credit4'] = ['money'=>($user['credit4']),'text'=>config('site.credit4_text')];
            $data['credit5'] = ['money'=>$user['credit5'],'text'=>config('site.credit5_text')];//法币

            //总资产还包括收益中和转让中、待转让
            $od = db()->query('select sum(pcp) as n from fa_ore_order where buy_username = "'.$this->auth->username.'" and status <= 2 and status4 <= 3');
            $total = $user['credit1']+$user['credit2']+$user['credit3']+$user['credit4']+$user['credit5'] + $od[0]['n'];
            $total = round($total,2);
            $data['total'] =   ['money'=>$total,'text'=>'总资产'];
            $this->success('获取成功', ['data'=>$data]);
        }else{
            $this->error("获取失败");
        }
    }

    /**
     * 获取 矿机  推广收益
     */

    public function getCoin()
    {
        //$id = $this->request->request('id');
        $id = $this->auth->id;
        $type = $this->request->request('type');
        $user =  \db('user')->where(['id'=>$id])->find();
        $log = \db('cc_detail_log')->where(['type'=>$type,'username'=>$user['username']])->where('num','<>',0)->order('id desc')->select();
//		if(!$log) $this->error("无记录", ['data'=>$log,'money'=>$user[$type]]);
        foreach($log as &$v){
            $v['time'] = date('Y-m-d H:i:s',$v['createtime']);
            unset($v['createtime']);
            unset($v['updatetime']);
        }
        unset($v);
        if($log){
            $this->success('有记录', ['data'=>$log,'money'=>$user[$type],'tjsy'=>$user['credit3acc'],'tdsj'=>$user['credit3acd'],'kj_recharge'=>intval(config('site.kj_recharge'))]);
        }else{
            $this->error("无记录", ['data'=>$log,'money'=>$user[$type],'tjsy'=>$user['credit3acc'],'tdsj'=>$user['credit3acd'],'kj_recharge'=>intval(config('site.kj_recharge'))]);
        }
    }

    /**
     * 获取 区块Mine  DOGE  我的收益
     */

    public function getCoinOrder(){

        //$id = $this->request->request('id');
        $id = $this->auth->id;
        $type = $this->request->request('type');
        $user =  \db('user')->where(['id'=>$id])->find();
        $log = \db('ore_order')
            ->where(['buy_username'=>$user['username']])
            ->where($type,'gt',0)
            ->order('id desc')
            ->field("orecode,days,per,total_money,level,credit2,credit4,credit5,due_time")
            ->select();

        //$ore_db =  \db('block_ore');
        $leve_db = \db('block_ore_level');
        foreach($log as &$v){
            $level = $leve_db->where(['level'=>$v['level']])->find();
            //$ore = $ore_db->where(['orecode'=>$v['orecode']])->find();
            $v['levelname'] = $level['levelname'];
            $v['money'] = $v[$type];
            $v['time'] = date('Y-m-d H:i:s',$v['due_time']);
            unset($v['credit2']);
            unset($v['credit4']);
            unset($v['credit5']);
            unset($v['due_time']);
        }
        unset($v);
        $param = Config::getSetting();
        $config['credit2_from'] = $param['credit2_from'];
        $config['credit2_to'] = $param['credit2_to'];
        $config['credit4_from'] = $param['credit4_from'];
        $config['credit4_to'] = $param['credit4_to'];
        $config['credit5'] =  $user['credit5'];
        if($log){
            $this->success('有记录', ['data'=>$log,'config'=>$config]);
        }else{
            $this->error("无记录", ['data'=>$log,'config'=>$config]);
        }
    }
    /**
     * 转让我的矿机
     */
    public function transfer(){
        //$id = $this->request->request('id');
        $id = $this->auth->id;
        $mobile = $this->request->request('mobile');
        $money = floatval($this->request->request('money'));
        $paypwd = $this->request->request('paypwd');
        $from =  \db('user')->where(['id'=>$id])->find();
        $to =  \db('user')->where(['mobile'=>$mobile])->find();

        //只允许上级转给下级
        //判断是否开启限制
        if (config('site.user_transfer_up')) {
            $to2 = db('user_detail')->where('uid',$to['id'])->field('tjstr')->find();
            if (isset($to2['tjstr'])) {
                $arr = explode(',',$to2['tjstr']);
                if ($arr && !in_array($id,$arr)) {
                    $this->error('只允许上级转给下级');
                }
            }
        }

        if (empty($paypwd)) {
            $this->error(__('交易密码不能为空'));
        }
        $pwd = Auth::getEncryptPassword($paypwd,$this->auth->salt);
        $payword = db('user_detail')->where('uid',$this->auth->id)->field('paypwd')->find();
        if ($pwd != $payword['paypwd']) {
            $this->error('密码错误');
        }

        if ($money <= 0) {
            $this->error(__('转出数量必须大于0'));
        }

        $min_price = config('site.min_transfer');

        if($from['credit1']<$money){
            $this->error("您的".config('site.credit1_text')."不足");
        }

        //如果矿机总数小于30不给转出
        if ($from['credit1'] < $min_price) {
            $this->error(config('site.credit1_text').'数小于'.$min_price.'，不能进行转出');
        }

        //转出之后的剩余余额不能低于设置的值
        if (($from['credit1']-$money) < $min_price) {
            $this->error('您最多只能转出'.($from['credit1'] - $min_price).config('site.credit1_text'));
        }

        if(!$to){
            $this->error("转出用户不存在");
        }
        if($to['id']==$from['id']){
            $this->error("不能转给自己");
        }
        Db::startTrans();
        try{
            \db('user')->where('id', $from['id'])->setDec('credit1', $money);
            \db('user')->where('id', $to['id'])->setInc('credit1', $money);
            \db('user')->where('id', $to['id'])->setInc('credit1acc', $money);
            \db('cc_detail_log')->insert(['username' => $from['username'],'type' => 'credit1','num' => (0-$money),'remark' =>"向 ".$mobile.' 转出'.config('site.credit1_text'),'createtime' => time(),'updatetime' =>time()]);
            \db('cc_detail_log')->insert(['username' => $to['username'],'type' => 'credit1','num' => abs($money),'remark' => "转入".config('site.credit1_text'),'createtime' => time(),'updatetime' =>time()]);

            Db::commit();
            //触发升级
            Levelup::autolevelup($to['id']);

            $this->success('转让成功');
        } catch (Exception $e){
            Db::rollback();
            $this->error($e->getMessage());
        }
    }

    /**
     * 矿机充值页面获取参数
     */
    public function getRechargeInfo(){
        //判断是否开启充值
        if (!intval(config('site.kj_recharge'))) {
            $this->error(__('充值功能未开启'));
        }
        //$id = $this->request->request('id');
        $id = $this->auth->id;
        $user =  \db('user')->where(['id'=>$id])->find();
        $sys = Config::getSetting();
        $data['sykj'] = $user['credit1'];

        $data['hkfs'] = [
            1 => '微信',
            2 => '支付宝'
        ];
        if (config('site.bcw_enable')) {
            $key = array_search(config('site.bcw_coiname'),config('recharge'));
            if ($key) {
                $data['hkfs'][$key] = config('site.bcw_coiname');
            }
            //区块链钱包地址图片二维码
            $data['addr_img_url'] = db('user_detail')->where('uid',$id)->value('addr_img_url');
        }

        $data['xzbz'] = config('site.credit1_text');
        $data['fee'] = $sys['kj_fee'];
        $data['wx_qrcode'] = $sys['kj_qrcode'];
        $data['zfb_qrcode'] = $sys['kj_zfb_qrcode'];
        $data['bzcode'] = $sys['kj_bzcode'];
        $this->success('获取成功',['data'=>$data]);
    }

    /**
     * 矿机充值页面提交
     */
    public function addRechargeLog(){
        //判断是否开启充值
        if (!intval(config('site.kj_recharge'))) {
            $this->error(__('充值功能未开启'));
        }
        //$data['uid'] = $this->request->request('id');
        $data['uid'] = $this->auth->id;
        $data['hkmoney'] = floatval($this->request->request('hkmoney'));
        $data['hkimg'] =  $this->request->request('hkimg');
        $data['paytype'] = $this->request->request('paytype');
        $data['cointype'] = 'credit1';
        $data['updatetime'] = $data['createtime'] = time();

        if ($data['hkmoney'] <= 0) {
            $this->error(__('充值数量不能小于0'));
        }
        if (empty($data['hkimg'])) {
            $this->error(__('支付凭证不能为空！'));
        }

        $rs = \db("user_recharge_log")->insert($data);
        if($rs) $this->success('提交成功，等待审核');
        else   $this->error("提交失败");
    }
    /**
     * 钩子测试升级
     */
    public function hook_test(){
        $uid = 1;
        $result = Hook::exec('app\\common\\behavior\\Levelup','run',$uid);
    }

    public function getkjfee(){
        $where = [
            'name'=>'kj_fee'
        ];
        $res = \db('config')
            ->field('value')
            ->where($where)
            ->select();
        $this->success('获取成功',['data'=>$res]);
    }
    
    /**
     * 区块链钱包会员充值
     */
    public function walletrecharge()
    {
        //拦截ip
        check_api_ip();
        $hash = $this->request->request('hash'); // 交易记录hash
        $coinName = $this->request->request('coinName'); // 币种信息
        $walletType = $this->request->request('walletType'); //钱包类型
        if($hash==''||$coinName==''||$walletType==''){
            $this->error(__('Invalid parameters'));
        }
        $res=Walletapi::gettx($hash,$walletType,$coinName);
        if($res['code']==200){
            $rdata=$res['data'];
        }else{
            $this->error($res['msg']);
        }
        $recharge=db('recharge_log')->where('hash',$rdata['hash'])->find();
        if(!empty($recharge))$this->success('该订单已充值','',200);
    
        $data['hash']=$rdata['hash'];//区块链充值交易订单号
        $data['amount']=$rdata['amount'];//充值金额
        $data['fromaddr']=$rdata['from'];//充值来源地址
        $data['toaddr']=$rdata['to'];//充值目的地址（会员对应绑定的钱包地址）
        $data['coinName']=$rdata['coinName'];//充值币种
        $data['createtime']=time();
        
        
        $userid=db('user_detail')->where('walletaddr',$rdata['to'])->value('uid');
        //地址无关联会员
        if(empty($userid)){
            db('recharge_log')->insert($data);
            $this->success(__('Charge Address Unbound Members'),'',200);
        }
        $data['mid']=$userid;
        db('recharge_log')->insert($data);
        
        //这里可以开始添加充值成功后的系统逻辑

        $username = db('user')->where('id',$data['mid'])->value('username');
        setCc($username,'credit1',$data['amount'],'通过'.$data['coinName'].'钱包成功充值'.$data['amount'].config('site.credit1_text'));
        $rl['uid'] = $data['mid'];
        //充值金额
        $rl['hkmoney'] = floatval($data['amount']);
        //如果类型为0 表示未知币种类型
        $rl['paytype'] = array_search($data['coinName'],config('recharge')) ? array_search($data['coinName'],config('recharge')) : 0;
        //充值到的那个字段
        $rl['cointype'] = 'credit1';
        $rl['updatetime'] = $data['createtime'] = time();
        $rl['status'] = 1;
        db("user_recharge_log")->insert($rl);

        
        $this->success('充值成功','',200);
        
    }

}
