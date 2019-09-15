<?php
/**
 * Created by PhpStorm.
 * 系统初始化管理 cjj
 * User: Administrator
 * Date: 2019/3/18
 * Time: 10:09
 */

namespace app\admin\controller\System;
use app\common\controller\Backend;

class Initialization extends Backend
{
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Initialization');
    }

    // 系统初始化
    public function index(){
        if($this->request->isAjax()){
            $this->model->index();
            // 初始化成功
            return json(['code' => 1,'msg' => __('successful initialization')]);
        }
        return $this->view->fetch();
    }
}