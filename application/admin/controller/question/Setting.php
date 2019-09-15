<?php
/**
 * 公告栏.
 * User: Administrator
 * Date: 2019/3/19
 * Time: 14:36
 */

namespace app\admin\controller\question;
use app\common\controller\Backend;
use app\common\model\Category as CategoryModel;

class Setting extends Backend
{
    // 定义快速搜索的字段
    protected $searchFields = 'id,title';
    protected $model = null;
    protected static $category = null;

    public function _initialize()
    {
		parent::_initialize();
		$this->model = model('Question');
		$category = \db('category')->where(['type'=>'question','status'=>'normal'])->field('id,name')->select();
		$this->view->assign("typeList", $category);
    }


    // 查看
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
            
            foreach($list as &$val){
				$category = \db('category')->where(['type'=>'question','status'=>'normal','id'=>$val['category_id']])->field('name')->find();
                $val['type_text'] = $category['name'];
            }
            unset($val);

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }


    public function detail($ids = null){
        $row = $this->model->get($ids);
        if(!empty($row)){
            $row = $row->toArray();
			$category = \db('category')->where(['type'=>'question','status'=>'normal','id'=>$row['category_id']])->field('name')->find();
			$row['type_text'] = $category['name'];
        }
        $this->view->assign('row', $row);
        return $this->view->fetch();
    }
}