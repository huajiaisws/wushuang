<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 首页接口
 */
class Index extends Api
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     *
     */
    public function index()
    {
        $this->success('请求成功');
    }


//获取logo,登录背景图，首页顶部背景图，底部icon,服务中心背景图、用户头像
    public function getImg()
    {
        $result = [];
        $result['logo'] = config('site.logo');
        $result['login_logo'] = config('site.login_logo');
        $result['home_top_banner'] = config('site.home_top_banner');
        $result['home_bottom_nav'] = [];

        $type = ['name', 'icon', 'src', 'route'];
        $arr = [];
        for ($i = 0; $i < count(config('site.home_bottom_name')); $i++) {
            foreach ($type as $item) {

                if (empty(config('site.home_bottom' . "_" . $item)[$i])) {
                    $arr[$item] = "";
                } else {
                    $arr[$item] = config('site.home_bottom' ."_". $item)[$i];
                }
            }
            array_push($result['home_bottom_nav'], $arr);
        }


        $result['service_bg']=config('site.service_bg');
        $result['user_photo']=config('site.user_photo');
        $this->success("请求成功",$result);
    }

}
