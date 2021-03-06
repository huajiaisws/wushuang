<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/3
 * Time: 18:38
 */
namespace app\api\controller;

use app\common\controller\Api;
use app\common\library\ValidateCode;
use think\Cookie;
use think\Session;
use think\Cache;

class Captcha extends Api
{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = '*';

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = '*';

    protected function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
    }

    /**
     * 写入缓存
     * @access public
     * @param  string   $name   缓存标识
     * @param  mixed    $value  存储数据
     * @param  int|null $expire 有效时间 0为永久
     * @return boolean
     */
    public function get(){
        Session::start();
        $identifier = $this->request->request('identifier');
        header('I: '.$identifier);
        header('C: '.Session::get($identifier));
        $code = new ValidateCode();
        try{
            $img = $code->doimg();
            Cache::set($identifier, $code->getCode(), 300);
        }catch (\Exception $e){
            $this->error($e->getMessage());
        }
    }

    public function test()
    {

        $identifier = $this->request->request('identifier');
        //Cache::set($identifier, 'think', 600);
        echo Cache::get($identifier);
    }
}
