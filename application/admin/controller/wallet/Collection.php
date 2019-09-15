<?php

namespace app\admin\controller\wallet;

use app\common\controller\Backend;
use app\common\behavior\Walletapi;
/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Collection extends Backend
{
    
    /**
     * Log模型对象
     * @var \app\admin\model\collection\Log
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\collection\Log;

    }
    
    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        //节点余额
        $money=Walletapi::amount();
        
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $addr=$params['addr'];
            $res=Walletapi::collection($addr);
            
            if($res['code']==200){
                $data['addr']=$addr;
                $data['hash']=$res['data'];
                $data['createtime']=time();
                db('collection_log')->insert($data);
                $this->success(__('Collection success'));
            }else{
                $this->error(__('Collection failed, please try again'));
            }
            
        }
        $this->assign('money',$money);
        return $this->view->fetch();
    }
    

}
