<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/19
 * Time: 20:41
 */
namespace app\api\controller;

use app\common\controller\Api;

/**
 * 公告栏
 */
class Notice extends Api
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['getTitles','details'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = [];

    // 分页参数
    // 一个页面显示几条数据
    protected $pagesize = 8;
    // 页码
    protected $page = 1;
    // 总页数
    protected $totalpage = 0;

    public function __construct()
    {
        parent::__construct();
        $ps = config('paginate.list_rows');
        if($ps > 0){
            $this->pagesize = $ps;
        }
        // 获取分页参数
        $this->page = input('get.page') ? intval(input('get.page')) : $this->page;
    }

    /**
     * 会员首页 mh ，交易中心 tc
     * @throws \think\Exception
     */
    public function getTitles(){
        $type = input('get.type') ? input('get.type') : 'mh';

        // 获取所有公告栏的标题
        $index = ($this->page - 1)*$this->pagesize;
        $data = db('notice')->field('n.title,n.id')->alias('n')->join('notice_category nc','n.type = nc.id')->where('nc.type',$type)->limit($index,$this->pagesize)->select();

        // 总条数
        $total = db('notice')->field('n.title')->alias('n')->join('notice_category nc','n.type = nc.id')->where('nc.type',$type)->count();
        $this->totalpage = ceil($total/$this->pagesize);

        $datas['page'] = $this->page;
        $datas['totalpage'] = $this->totalpage;
        $datas['data'] = $data;

        if(empty($datas)){
            $this->success('没有数据');
        }else{
            $this->success('返回成功',$datas);
        }
    }

    /**
     * 公告栏详情
     */
    public function details(){
        $id = input('get.id');
        // 判断是否有传参数、参数是否正确
        if(!empty($id) && is_numeric($id)){
            // 通过id查询对应的公告信息
            $data = db('notice')->field('id,title,contents,createtime')->where('id',intval($id))->select();
            if(!empty($data)){
                // 成功查询到数据，返回对应的数据
                $data[0]['createtime'] = date('Y-m-d H:i:s');
                $this->success('返回成功',$data);;
            }else{
                // 没有找到数据，返回对应提示
                $this->success('没有数据');
            }
        }else{
            // 参数不正确，返回对应的提示
            $this->error('参数错误');
        }
    }
}