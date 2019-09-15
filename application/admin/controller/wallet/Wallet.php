<?php
/**
 * 各个币种变动的明细记录
 * User: Administrator
 * Date: 2019/3/19
 * Time: 10:00
 */

namespace app\admin\controller\wallet;
use app\common\controller\Backend;

class Wallet extends Backend {

    // 定义快速搜索的字段
    protected $searchFields = 'id,type';

    protected $model = null;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Wallet');
    }

    private $stateArr = [
        '-1' => '拒绝',
        '0' => '待审批',
        '1' => '通过',
    ];

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

            foreach ($list as &$v){
                $user = db('user')->where("id={$v['uid']}")->find();
                $v['name'] = $user['username'];
                $v['typename'] = $v['type'] == 'credit2' ? config('site.credit2_text') : config('site.credit4_text');
                $v['statestr'] = $this->stateArr[$v['state']];
            }
            unset($v);

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }

        //name typename createtime
        $user = db('user')->where("id={$row['uid']}")->find();
        $row['typename'] = $row['type'] == 'credit2' ? config('site.credit2_text') : config('site.credit4_text');
        $row['name'] = $user['username'];
        $row['createtime'] = date('Y-m-d H:i:s', $row['createtime']);
        $row['modifytime'] = date('Y-m-d H:i:s', $row['modifytime']);

        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                //对状态进行修改 state  -1拒绝，0未审批，1通过
                if ($row['state'] != 0){
                    $this->error('该记录已经审批过');
                }

                db()->startTrans();
                try{
                    db('wallet')->where("id={$params['id']}")->update(['state'=>$params['state'], 'modifytime'=>time()]);

                    if ($params['state'] == -1){  //拒绝，提现的币返回到原用户
                        setCc($user['username'], $row['type'], $row['number'], '单号'.$row['id'].'提现申请被拒绝');
                    }
                    db()->commit();
                }catch (\Exception $e){
                    db()->rollback();
                    $this->error($e->getMessage());
                }
                $this->success('审批成功');

            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $this->view->assign("row", $row);

        return $this->view->fetch();
    }

}
