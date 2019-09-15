<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/12
 * Time: 11:08
 */
namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Config;
use think\Db;

class Question extends Api
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    //获取分类
	public function getCategoryList(){
		$page = $this->request->request('page');
		$page = !empty($page) ? $page : 1;
		$pageSize = config('page_rows');

		$count = \db('category')->where(['type'=>'question','status'=>'normal'])->count();
		$data = \db('category')
			->where(['type'=>'question','status'=>'normal'])
			->field('id,name')
			->order('weigh desc')
			->limit(($page-1)*$pageSize, $pageSize)
			->select();

		$this->success('1', ['data'=>$data, 'page'=>$page, 'totalpage'=>ceil($count/$pageSize)]);
	}
    //获取后台问题列表页
	public function getAllList()
	{
		$page = $this->request->request('page');
		$page = !empty($page) ? $page : 1;
		$pageSize = config('page_rows');

		$category_id = $this->request->request('category_id');

		$count = \db('question')->where(['category_id'=>$category_id])->count();

		$data = \db('question')
			->where(['category_id'=>$category_id])
			->field('id, title')
			->limit(($page-1)*$pageSize, $pageSize)
			->select();

		$this->success('1', ['data'=>$data, 'page'=>$page, 'totalpage'=>ceil($count/$pageSize)]);
		
	}
	// 获取某一个问题的内容
	public function getDetail()
	{
	    $id =  $this->request->request('id');
		$data = \db('question')
			->field('id, title,contents')
			->where(['id'=>$id])
		   ->find();
		if($data)
			$this->success('获取成功', ['data'=>$data]);
		else
			$this->error('获取失败');
	}

	//上传反馈问题
	public function addFeedback(){
		$data['uid'] = $this->request->request('uid');
		$data['connect'] = $this->request->request('connect');
		$data['question'] = $this->request->request('question');
		$data['img'] = $this->request->request('img');
		$rs = \db('feedback')->insert($data);
		if($rs)
			$this->success('提交成功');
		else
			$this->error('提交失败');
	}
   	//获取客服中心文字
	public function getCenter()
	{
		$data = [];
		$param = Config::getSetting();
		$this->success('', $param['customer_center']);
	}

    //获取首页顶部的自定义图片
    public function getTopImg(){
        $this->success(__('返回成功'),['banner_img' => config('site.home_top_img')]);
    }
}