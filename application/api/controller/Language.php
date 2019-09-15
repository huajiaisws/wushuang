<?php
/**
 * 多语言设置接口.
 * User: admin
 * Date: 2019/7/19
 * Time: 17:07
 */

namespace app\api\controller;

use app\common\controller\Api;
use think\Cookie;

/**
 * 首页接口
 */
class Language extends Api
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 设置语言
     * 繁体请创建存放繁体语言包的文件夹(zh-tw)和对应的繁体语言包，键名使用英文键名
     */
    public function setLanguage()
    {
        $lang = input('get.lg') ? input('get.lg') : 'zh-cn';
        //       中文     英文     繁体
        $lgs = ['zh-cn','zh-en','zh-tw'];
        if (in_array($lang,$lgs)) {
            Cookie::set('think_var',$lang);
            $this->success(__('success'),Cookie::get('think_var'));
        }else{
            //Cookie::set('think_var','zh-tw');
            $this->error(__('Parameter error'));
        }
    }

    /**
     * 获取语言类型
     */
    public function getLanguage(){
        $this->success('返回成功',Cookie::get('think_var'));
    }


}