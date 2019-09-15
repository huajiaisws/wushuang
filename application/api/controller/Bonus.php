<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/2
 * Time: 14:40
 */
namespace app\api\controller;

use app\common\controller\Api;
use app\common\core\Procevent;

class Bonus extends Api
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = '*';
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = '*';


    /**
     * 推广奖
     * - 得到智能合约的时候触发
     */
    public function actTgj()
    {
        $param = $this->request->request();

        try{
            Procevent::dsell_event($param, 'tgj');
        }catch (\Exception $e){
            $this->error($e->getMessage());
        }
        $this->success('推广奖已触发');
    }


    /**
     * 团队奖
     * - 智能合约收益、推广奖、团队奖
     */
    public function actTdj()
    {
        $param = $this->request->request();

        try{
            Procevent::dsell_event($param, 'tdj');
        }catch (\Exception $e){
            $this->error($e->getMessage());
        }
        $this->success('团队奖已触发');
    }

}