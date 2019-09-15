<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\core\Procevent;
use think\Cookie;

/**
 * 示例接口
 */
class Demo extends Api
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['test', 'test1'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['test2'];

    /**
     * 测试方法
     *
     * @ApiTitle    (测试名称)
     * @ApiSummary  (测试描述信息)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api/demo/test/id/{id}/name/{name})
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="id", type="integer", required=true, description="会员ID")
     * @ApiParams   (name="name", type="string", required=true, description="用户名")
     * @ApiParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
         'code':'1',
         'msg':'返回成功'
        })
     */
    public function test()
    {
        //批量生产用户
        $num = input('get.num/d');
        $nb = 0;
        if ($num) {
            $arr = [];
            $arr2 = [];

            $salt = \fast\Random::alnum();
            $mobile = 13611111111;
            $url = '/uploads/20190420/dc1aa6aa307ebeb45c0e95b2f50c63c6.jpg';
            db()->query('truncate fa_user');
            db()->query('truncate fa_user_detail');
            $id = 1;
            while($num){
                $tjid = 1;
                $tjstr = 1;
                $iscomp = 0;
                if ($id == 1) {
                    $tjid = 0;
                    $tjstr = '';
                    $iscomp = 1;
                }
                $arr[] = [
                    //'id' => $id,
                    'username' => uniqid(),
                    'mobile'    => $mobile++,
                    'password' =>   md5(md5('admin123').$salt),
                    'createtime' => time(),
                    'nickname'  => $mobile,
                    'salt'      => $salt,
                    'status'    => 'normal',
                    'level' => 2,
                    'credit1' => 10000,
                    'weights' => 1,
                    'iscomp' => $iscomp
                ];

                $arr2[] = [
                    'uid' => $id,
                    'realname' => '张三'.$id,
                    'creditid' => '123456789123456789',
                    'alipayact' => '123456',
                    'alipayname' => '123456',
                    'wechatact' => '123456',
                    'wechatname' => '123456',
                    'paypwd' => md5(md5('123456').$salt),
                    'isreal' => 1,
                    'tjid' => $tjid,
                    'tjstr' => $tjstr,
                    'alipay_url' => $url,
                    'wechat_url' => $url
                ];

                if ($nb == 1000) {
                    db('user')->insertAll($arr);
                    db('user_detail')->insertAll($arr2);
                    $arr = [];
                    $arr2 = [];
                    $nb = 0;
                }

                $id++;
                $num--;
            }

            db('user')->insertAll($arr);
            db('user_detail')->insertAll($arr2);
            echo '添加完成';
        }

        //$this->success('返回成功', $this->request->param());
    }

    /**
     * 无需登录的接口
     *
     */
    public function test1()
    {
        $this->success('返回成功', ['action' => 'test1']);
    }

    /**
     * 需要登录的接口
     *
     */
    public function test2()
    {
        $this->success('返回成功', ['action' => 'test2']);
    }

    /**
     * 需要登录且需要验证有相应组的权限
     *
     */
    public function test3()
    {
        $this->success('返回成功', ['action' => 'test3']);
    }

}
