<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/12
 * Time: 12:05
 */
namespace app\api\controller;

use app\admin\library\Auth;
use app\common\controller\Api;

class Wallet extends Api
{
    protected $noNeedRight = '*';

    private $stateArr = [
        '-1' => '拒绝',
        '0' => '待审批',
        '1' => '通过',
    ];
    
    public function getRate(){
        $info = db('user')->where('id',$this->auth->id)->field('credit2,credit4')->find();
        $crefit2 = array();
        foreach (config('site.credit2_rate') as $k=>$v){
            $crefit2['key'] = $k;
            $crefit2['value'] = $v;
            $crefit2['credit2'] = $info['credit2'];
            $crefit2['credit4'] = $info['credit4'];
        }

        $crefit4 = array();
        foreach (config('site.credit4_rate') as $k=>$v){
            $crefit4['key'] = $k;
            $crefit4['value'] = $v;
            $crefit4['credit2'] = $info['credit2'];
            $crefit4['credit4'] = $info['credit4'];
        }

        $this->success('', [config('site.credit2_text')=>$crefit2, config('site.credit4_text')=>$crefit4]);
    }

    /**
     * 提取记录（区块Mine，Doge币）
     * page type
     * @throws \think\Exception
     */
    public function lists(){
        $param = $this->request->request();
        $param['page'] = !empty($param['page']) ? $param['page'] : 1;
        $pageSize = config('page_rows');
        if (!isset($param['type']) || !in_array($param['type'], ['credit2', 'credit4'])){
            $this->error('type参数错误');
        }

        $count = db('wallet')->where("uid={$this->auth->id} and type='{$param['type']}'")->count();
        $result = db('wallet')
                    ->field('number, credit5, address, createtime, state')
                    ->where("uid={$this->auth->id}")
                    ->where("type='{$param['type']}'")
                    ->limit(($param['page']-1)*$pageSize, $pageSize)
                    ->select();
        foreach ($result as &$v){
            $v['state'] = $this->stateArr[$v['state']];
        }
        unset($v);

        $this->success('', ['data'=>$result, 'page'=>$param['page'], 'totalpage'=>ceil($count/$pageSize)]);

    }

    /**
     * 提取申请（区块Mine，Doge币）
     * address number type
     */
    public function apply(){
        //credit2 区块mine
        //credit4 狗狗币
        $arr = ['credit2'=>config('site.credit2_text'), 'credit4'=>config('site.credit4_text')];

        $address = $this->request->request('address');
        $number = floatval($this->request->request('number'));
        $type = $this->request->request('type');

        if ($number <= 0) {
            $this->error(__('申请数量不能小于0'));
        }

        if (empty($address) || empty($number)){
            $this->error('地址和数量参数不能为空');
        }

        if (!in_array($type, ['credit2', 'credit4'])){
            $this->error('type参数错误');
        }

        if ($number > $this->auth->$type){
            $this->error($arr[$type].'不足');
        }

        if (!is_array(config('site.'.$type.'_rate')) || count(config('site.'.$type.'_rate')) != 1){
            $this->error($type.'后台提取配置错误');
        }

        foreach (config('site.'.$type.'_rate') as $k=>$v){
            $rate = $v/$k;
        }

        if ($rate <= 0){
            $this->error($type.'后台提取配置错误');
        }

        $data = [
            'uid' => $this->auth->id,
            'address' => $address,
            'number' => $number,
            'credit5' => $number * $rate,
            'type' => $type,
            'createtime' => time()
        ];

        db()->startTrans();
        try{
            db('wallet')->insert($data);
            setCc($this->auth->username, $type, -$number, '提现申请，单号为'.db('wallet')->getLastSql());

//            db('user')->where("id={$this->auth->id}")->setDec($type, $number);

            db()->commit();
        }catch (\Exception $e){
            db()->rollback();
            $this->error($e->getMessage());
        }

        $this->success('申请成功');
    }

}