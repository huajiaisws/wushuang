<?php

namespace app\admin\controller\user;
use app\common\behavior\Walletapi;
use app\common\core\Procevent;
use app\common\model\Config;
use app\common\model\Levelup;
use think\Db;
use think\Env;
use think\Model;
use app\common\controller\Backend;
use fast\Random;
use think\Validate;
use app\common\library\Auth;
use app\common\model\User as CommonUser;
/**
 * 会员管理
 *
 * @icon fa fa-user
 */
class User extends Backend
{
    protected $relationSearch = true;
    /**
     * @var \app\admin\model\User
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('User');
    }
    /**
     *
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with('detail')
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->with('detail')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => &$v)
            {
                $v->hidden(['password', 'salt']);
                $v['total'] = $v['credit1']+$v['credit2']+$v['credit3']+$v['credit4']+$v['credit5'];

                $levelname = \db('user_level')
                                    ->field('levelname')
                                    ->where('level', $v['level'])
                                    ->find();
                $v['level'] = $levelname['levelname'];
                $v['polatoon'] = config('system.weights')[$v['weights']];

            }
            unset($v);

            $list = collection($list)->toArray();
            foreach ($list as $k=>$v){
                $list[$k]['detail']['cmoney'] = $v['detail']['csell']+$v['detail']['cfree']+$v['detail']['clock'];
            }

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
        $row = $this->model->get($ids,'detail');
        $weights = config('system.weights');
        $levels = db('user_level')->where('enabled',1)->field('level,levelname')->select();

        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                    // 是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    $uid = $params['id'];

                    try{
                        if ($params['creditid'] != $row->detail->creditid) {
                            CommonUser::creditidCheck($params['creditid']);
                        }
                    }catch (\Exception $e){
                        $this->error($e->getMessage());
                    }
                    
                    $where = [
                        'realname' => $params['realname'],
                        'credittype' => $params['credittype'],
                        'creditid' => $params['creditid'],
                        'alipayact' => $params['alipayact'],
                        'wechat_url' => $params['wechat_url'],
                        'wechatact' => $params['wechatact'],
                        'alipay_url' => $params['alipay_url'],
                        'bankact' => $params['bankact'],
                        'bankphone' => $params['bankphone'],
                        'account' =>$params['account'],
                        'bank' =>$params['bank'],
                        'bankname' => $params['bankname'],
                        'isreal' => $params['isreal'],
                    ];
                    $where_b = [
                        'level' => $params['level'],
                        'status' => $params['status'],
                        'weights' => $params['weights']
                    ];
                    if ($params['password']) {
                        $where_b['password'] = md5(md5($params['password']).$row['salt']);
                    }
                    if ($params['paypwd']) {
                        $where['paypwd'] = md5(md5($params['paypwd']).$row['salt']);
                    }
                    $result = Db::name('user_detail')->where('uid', $uid)->update($where);

                    $result_b = Db::name('user')
                        ->where('id', $uid)
                        ->update($where_b);
                    if ($result !== false && $result_b !== false) {
                        $this->success();
                    } else {
                        $this->error($row->getError());
                    }

            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
//        dump($row->toArray());
        $this->view->assign("row", $row);
        $this->view->assign("weights", $weights);
        $this->view->assign("levels", $levels);
        return $this->view->fetch();
    }

    /**
     * 批量更新
     */
    public function multi($ids = "")
    {
        $ids = $ids ? $ids : $this->request->param("ids");
        if ($ids) {
            if ($this->request->has('params')) {
                parse_str($this->request->post("params"), $values);
                if (!$this->auth->isSuperAdmin()) {
                    $values = array_intersect_key($values, array_flip(is_array($this->multiFields) ? $this->multiFields : explode(',', $this->multiFields)));
                }
                if ($values) {
                    $adminIds = $this->getDataLimitAdminIds();
                    if (is_array($adminIds)) {
                        $this->model->where($this->dataLimitField, 'in', $adminIds);
                    }
                    $count = 0;
                    $list = $this->model->where($this->model->getPk(), 'in', $ids)->select();
                    foreach ($list as $index => $item) {
                        $count += $item->allowField(true)->isUpdate(true)->save($values);
                    }
                    if ($count) {
                        $this->success();
                    } else {
                        $this->error(__('No rows were updated'));
                    }
                } else {
                    $this->error(__('You have no permission'));
                }
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    public function del($ids = "")
    {
        if ($ids) {
            $pk = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                $count = $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $list = $this->model->where($pk, 'in', $ids)->select();
            $count = 0;

            //查询该会员是否存在下级，如果有，则不能删除
            $dels = explode(',',$ids);
            $ud = db('user_detail');
            foreach ($dels as $v) {
                $info = $ud->where('tjid',$v)->field('uid')->find();
                if ($info) {
                    $this->error('会员存在下级不能删除');
                }
            }

            $num = count($dels);
            foreach ($dels as $k => $v) {
                db('user')->where('id',$v)->delete();
                $tjid = db('user_detail')->where('uid',$v)->value('tjid');
                if ($tjid) {
                    db('user_detail')->where('uid',$tjid)->setDec('tjnum',1);
                }
                db('user_detail')->where('uid', $v)->delete();
                $num--;
            }
            if ($num == 0) {
                $this->success();
            } else {
                $this->error(__('No rows were deleted'));
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    public function add(){
        $createtime = time();
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    // $result = $this->model->allowField(true)->save($params);

                    $ip = request()->ip();
                    $time = time();
                    $salt = \fast\Random::alnum();
                    $where = [
                        'username' => $params['username'],
                        'mobile'    => $params['mobile'],
                        'password' =>   md5(md5($params['password']).$salt),
                        'createtime' => $createtime,
                        'nickname'  => $params['username'],
                        'salt'      => $salt,
                        'jointime'  => $time,
                        'joinip'    => $ip,
                        'logintime' => $time,
                        'loginip'   => $ip,
                        'prevtime'  => $time,
                        'status'    => 'normal'
                    ];

                    // 检测用户名是否已存在
                    $user = db('user')
                        ->where('username',$params['username'])
                        ->select();
//                    检测手机号是否已存在
                    $mobile = db('user')
                        ->where('mobile',$params['mobile'])
                        ->select();
//                    检测推荐人是否存在
                    $tjj = db('user')
                        ->where("id={$params['tjid']} or mobile='{$params['tjid']}'")
                        ->find();
                    // 检测是否有会员
                    $user_tj = db('user')
                        ->select();
                    if($user){
                        $this->error('用户名已存在');
                    }

                    $num = count($user_tj) ;
                    if(empty($tjj) && $num !==0 ){
                        $this->error('推荐人编号不存在');die;
                    }
                    if ($params['username'] && !Validate::regex($params['username'], "^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,12}$"))
                    {
                        $this->error(__('用户名不符合要求'));
                    }
                    if($mobile){
                        $this->error('手机号已存在');
                    }
                    $repas = preg_match("/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,12}$/",$params['password'],$matches);
                    if($repas == 0){
                        $this->error('密码必须为6-12位的数字和字母');
                    }
                    $repay = preg_match("/^\d{6}$/",$params['paypwd'],$matches);
                    if($repay == 0){
                        $this->error('支付密码必须为6位数字');
                    }
                    if($params['password'] != $params['cpassword']){
                        $this->error('两次密码输入不一致');
                    }

                    if($params['paypwd'] != $params['cpaypwd']){
                        $this->error('两次支付密码输入不一致');
                    }
                    if($params['mobile']){
                        $match = preg_match("/^1[3|4|6|5|8|7|9]\d{9}$/",$params['mobile'],$matches);
                        if($match == 0){
                            $this->error('手机号不符合要求,手机号码');
                        }
                    }

                    //如果没人，第一个注册的为公司
                    if ($num == 0) {
                        $where['iscomp'] = 1;
                    }

                    $result = Db::name('user')
                        ->insert($where);
                    // 查询用户主表id
                    $info_b = db('user')
                        ->getLastInsID();
                    if($params['tjid']>0){
                        //更新推荐人信息
                        $tjinfo = Db::name('user_detail')
                            ->where('uid',$params['tjid'])
                            ->select();
                        $tjinfo = $tjinfo[0];
                        $info['tjnum'] = $tjinfo['tjnum'] + 1; //推荐人数
                        if (empty($tjinfo['tjstr'])) $update['tjstr'] = $tjinfo['uid'];
                        else$update['tjstr'] = $tjinfo['uid'] . ',' . $tjinfo['tjstr']; //推荐路径
                        $update['tjdept'] = substr_count($update['tjstr'], ',')+1; // 推荐深度
                        $tjinfo = Db::name('user_detail')
                            ->where('uid',$params['tjid'])
                            ->update($info);
                    }else{
                        $update['tjstr'] = 0; // 无路径
                        $update['tjdept'] = 0; // 无深度
                    }
                    

                    
                    $where_b = [
                        'paypwd'=> md5(md5($params['paypwd']).$salt),
                        'uid'   => $info_b,
                        'tjid'  => $params['tjid'],
                        'tjstr' => $update['tjstr'],
                        'tjdept' => $update['tjdept'],
                        'isreal' => 0
                    ];

                    //判断钱包是否启用
                    if (config('site.bcw_enable')) {
                        //初始化钱包
                        $where_b['walletaddr'] = Walletapi::createwallet($info_b);
                        //使用钱包地址生成二维码保存
                        $res = qrcode($where_b['walletaddr']);
                        if ($res['code'] == 1) {
                            $where_b['addr_img_url'] = $res['path'];
                        }
                    }

                    $result_b = Db::name('user_detail')
                        ->insert($where_b);
                    if ($result !== false && $result_b !==false) {
                        //注册成功，赠送矿机
                        //获取配置参数
                        $data = Config::getSetting();
                        $reg_jf = $data['reg_send_jf'];
                        if ($reg_jf > 0) {
                            setCc($params['username'],'credit1',$reg_jf,'注册成功，赠送 '.$reg_jf.config('site.credit1_text'));
                            db('user')->where('username',$params['username'])->update(['credit1acc'=>$reg_jf]);
                            //触发升级
                            Levelup::autolevelup($info_b);
                        }

                        $this->success();
                    } else {
                        $this->error($this->model->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $tjid = $this->request->request('tjid');
        if (!empty($tjid)){
            $this->view->assign('tjid', $tjid);
        }
        return $this->view->fetch();
    }



    public function tjnet(){

        $act = input('get.act') ? trim(input('get.act')) : 'manage';
        $mid = input('get.mid') ? intval(trim(input('get.mid'))) : 0;
        $type = input('get.t') ?  trim(input('get.act')) : '';
        $rootmid = 0;
        $pid = 0;
        $m = array();
        if($mid == 0) {
            $m = $this->getroot($mid);
            $mid = $m['id'];
            $rootmid = $m['id'];
        }
        // $res_level = pdo_fetchall('select id,levelname from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');

        if($m){
            $pid = $m['tjid'];
        }

        $this->view->assign('pid',$pid);

        return $this->view->fetch();
    }

    public function exsearch(){
        $keyword = input('get.keyword') ? trim(input('get.keyword')) : '';
        $rootmid = input('get.rootmid') ? intval(trim(input('get.rootmid'))) : 0;
        $type = input('get.type') ? trim(input('get.type')) : 'gl';

        $condition = "";
        $fieldname = 'gldept';
        $fieldstr = 'glstr';
        if($type != 'gl'){
            $fieldname = 'tjdept';
            $fieldstr = 'tjstr';
        }

        if (!(empty($keyword))) {
            $condition .= 'and (m.mobile like "%' . $keyword . '%" OR dm.realname like "%' . $keyword . '%" OR m.nickname like "%' . $keyword . '%" OR m.username like "%' . $keyword . '%")';
        }
        if($rootmid > 0){
            $condition .= " AND  m.id != $rootmid AND m.id NOT IN( SELECT uid FROM ".config('database.prefix') . "user_detail WHERE find_in_set('" . $rootmid. "',".$fieldstr."))";
        }

        $sql = "SELECT m.id,IFNULL(dm.realname, m.nickname) AS realname,m.mobile FROM ".config('database.prefix'). "user  AS m JOIN ".config('database.prefix') . "user_detail AS dm ".
            "ON m.id=dm.uid WHERE m.id > 0 ".$condition." LIMIT 8";

        $user_list = Db::query($sql);
        $user_list = collection($user_list)->toArray();
        if(empty($user_list)) $user_list[]=array('id'=>0, 'realname'=>'无匹配项');
        foreach ($user_list as &$u ) {
            if(empty($u['realname'])) $u['realname']=$u['mobile'];
        }
        exit(json_encode(array('data'=>$user_list)));
    }

    public function getroot($mid = 1){
        // 拿推荐id为0的人
        $member = $this->model->alias('u')->join('user_detail ud','u.id = ud.uid')->where('u.id',$mid)->find();
        if(empty($member)){
            $member = $this->model->alias('u')->join('user_detail ud','u.id = ud.uid')->where('ud.tjid',0)->find();

        }
        return $member;
    }

    //读取推荐图数据
    public function loadtjnet()
    {
        $mid = 0;
        if(input('get.mid')){
            $mid = intval(input('get.mid'));
        }else{
            $data = db('user_detail')->field('uid')->where('tjid',0)->find();
            $mid = $data['uid'];
        }
        $this->loadtjnet2(0, $mid, -1, 1);
    }

    //读取推荐图数据
    public function loadtjnet2($rootmid, $curid, $limitfloor, $isrootdp)
    {
        $arr = $this->_loadtjnet($rootmid, $curid, $limitfloor, $isrootdp);
        echo json_encode($arr);
    }
    private function _loadtjnet($rootmid, $curid, $limitfloor, $isrootdp)
    {

        $midentitylist = array();
        $pid = 0;
        $pos_cnt = config('site.pos_cnt'); //市场数, 0为太阳线
        $floor = config('site.tj_cs'); //显示层数
        $pindex = max(1, intval(input('get.page')));
        $psize = config('site.tj_psize'); //显示直推数量
        $pstar = ($pindex - 1) * $psize;
        $mid = $curid;
        $ret = array();
//        $sql = "SELECT tjdept FROM " .config('database.prefix') ."user_detail WHERE uid=$rootmid";
        $root_dept = db('user_detail')->field('tjdept')->where('uid',$rootmid)->find();

        if($root_dept == false) $root_dept = 0;
        $m = $this->getroot($curid);
        $retcode = 0;

        if($m){
            if($root_dept > $m['tjdept']){
                $floor = 0;
            }else{
                if($limitfloor > 0){
                    if($m['tjdept']-$root_dept > $limitfloor) {
                        $floor = 0;
                    }else {
                        $limitfloor = $limitfloor - ($m['tjdept'] - $root_dept);
                        if ($floor > $limitfloor) $floor = $limitfloor;
                    }
                }
            }

            if($floor == 0){
                exit(json_encode(array('data'=>array(), 'size'=>0, 'code'=>1, 'pos_cnt'=>$pos_cnt, 'mid'=>$mid,'pid'=>$rootmid, 'rootid'=>$rootmid, 'rootdp'=>$isrootdp)));
            }

            $retcode = 1;
            $ids = $m['id'];
            $pid = $m['tjid'];
            $sql = "select count(uid) as c from ".config('database.prefix') ."user_detail where find_in_set('" . $m['id'] . "',tjstr)";
            $totalnum = Db::query($sql);
            $totalnum = $totalnum[0]['c'];
            if($totalnum === false) $totalnum = 0;
            $m['rtime'] = $m['createtime'];
//            $m['dtypetext'] = $m['dantype'] == 1 ? '实单': '空单';
            $allpage = ceil($m['tjnum'] / $psize);
//            $icode = isset($midentitylist[$m['midentity']]['mname']) ? $midentitylist[$m['midentity']]['mname'] : '普通';
            $ret[$m['id']] = array('id'=>$m['id'], 'code'=>$m['username'], 'mobile'=>$m['mobile'], 'uname'=>$m['nickname'],
                'tjid'=>$m['tjid'],'status'=>$m['status'],'canreg'=>1,'totalnum'=>$totalnum,'tjnum'=>$m['tjnum'], 'tm'=>date("Y-m-d",$m['rtime']),
                'floor'=>0,'allpage'=>$allpage
            );

            for($i=1; $i<$floor; $i++){
                $members = $this->gettjusers($ids,$pstar,$psize);

                if(!empty($members)){
                    $id_arr = array();
                    array_map(function($value) use (&$id_arr){ $id_arr[] = $value['id'];}, $members);
//                    $ids = implode(',',$id_arr);
                    $ids = $id_arr;
                    $canreg = 1;
                    if($i == $floor-1)  $canreg = 0;
                    foreach($members as &$ms){
                        $sql = "select count(uid) as c from ".config('database.prefix') ."user_detail where find_in_set('" . $m['id'] . "',tjstr)";
                        $totalnum = Db::query($sql);
                        $totalnum = $totalnum[0]['c'];
                        if($totalnum === false) $totalnum = 0;
                        $ms['rtime'] = $m['createtime'];
//                        $m['dtypetext'] = $m['dantype'] == 1 ? '实单': '空单';
//                        $icode = isset($bonusset['midentity_']['param'][$m['midentity']]) ? $bonusset['midentity_']['param'][$m['midentity']] : '普通';
                        $ret[$ms['id']] = array('id'=>$ms['id'], 'code'=>$ms['username'], 'mobile'=>$ms['mobile'], 'uname'=>$ms['nickname'],
                            'tjid'=>$ms['tjid'],'status'=>$ms['status'],'canreg'=>$canreg,'totalnum'=>$totalnum,'tjnum'=>$ms['tjnum'], 'tm'=>date("Y-m-d",$ms['rtime']), 'floor'=>$i);
                    }
                }
            }
        }
        return array('data'=>$ret, 'size'=>count($ret), 'code'=>$retcode, 'pos_cnt'=>$pos_cnt,'mid'=>$m['id'],'pid'=>$pid, 'rootid'=>$rootmid, 'rootdp'=>$isrootdp);
    }

    public function gettjusers($tjid,$pstar,$psize){
        $members = array();
        $sql = "";
        if(is_array($tjid)){
            foreach($tjid as $id){
                $sql = "select u.id,u.username,u.nickname,u.mobile, ud.tjid,ud.tjnum,".
                    "ud.tjdept,ud.tjstr,u.createtime,u.status ".
                    "from ". config('database.prefix') . "user AS u JOIN ".config('database.prefix') . "user_detail AS ud ON u.id=ud.uid ".
                    "WHERE  ud.tjid=".$id." limit " . $pstar . "," . $psize;
                $members = array_merge($members,Db::query($sql));
            }
        }else{
            $sql = "select u.id,u.username,u.nickname,u.mobile, ud.tjid,ud.tjnum,".
                "ud.tjdept,ud.tjstr,u.createtime,u.status ".
                "from ". config('database.prefix') . "user AS u JOIN ".config('database.prefix') . "user_detail AS ud ON u.id=ud.uid ".
                "WHERE  ud.tjid=".$tjid." limit " . $pstar . "," . $psize;
            $members = Db::query($sql);
        }
        return $members;
    }

    //网络图节点查询
    public function netsearch(){
        $keyword = input('get.keyword') ? input('get.keyword') : '';
        $rootmid = input('get.rootmid') ? intval(trim(input('get.rootmid'))) : 0;
        $type = input('get.type') ? trim(input('get.type')) : 'gl';
        $this->netsearch2($rootmid, $keyword, $type);
    }

    public function netsearch2($rootmid, $keyword, $type='gl'){
        $floor = config('site.tj_cs');//网络图显示层数
        $dept = 0;
        $condition = "";
        $fieldname = 'gldept';
        if(empty($keyword)){
            exit(json_encode(array('code'=>0,'mid'=>0,'rootid'=>$rootmid)));
        }
        if($rootmid > 0){
            if($type != 'gl'){
                $fieldname = 'tjdept';
            }
            $sql = "select $fieldname FROM ".config('database.prefix')." WHERE uid=".$rootmid;
            $dept = db('user_detail')->field($fieldname)->where('uid',$rootmid)->find();
        }
        if($dept > 0){
            $dept += $floor;
            $condition .=" AND dm.$fieldname < ".$dept;
        }
        if (!(empty($keyword))) {
            $condition .= " AND (m.mobile like '%" . $keyword . "%' OR dm.realname like '%" . $keyword . "%' OR m.nickname like '%" . $keyword . "%' OR m.username like '%" . $keyword . "%' )";
        }
        $sql = "select m.id from ". config('database.prefix'). "user AS m JOIN ".config('database.prefix') . "user_detail AS dm ON m.id=dm.uid WHERE  1".$condition;
        $mid = Db::query($sql);
        $mid = $mid[0]['id'];
        $code = 1;
        if(empty($mid)){$code = 0; $mid = 0;}
        exit(json_encode(array('code'=>$code,'mid'=>$mid,'rootid'=>$rootmid)));
    }

    // 充值
    public function addcc($ids = NULL){
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");

            if ($params) {
                //是否采用模型验证
                if ($this->modelValidate) {
                    $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                    $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                    $row->validate($validate);
                }
                $id = $params['id'];
                // 增加 还是 减少
                $cctype = $params['addcc'];
                // 币种类型
                $credit = $params['credit'];
                // 数量
                $addnum = $params['addnum'];
//                    币种
                if ($addnum && !Validate::regex($addnum, "/(^[\-0-9][0-9]*(.[0-9]+)?)$/")) {
                    $this->error(__('请输入正确的数量'));
                }

                $where = [
                    'id' => $id
                ];
                $info = Db::name('user')
                    ->where($where)
                    ->select();
                if ($cctype == '1') {
                    setCc($info[0]['username'], $credit, $addnum, '用户' . $info[0]['username'] . '新增' . config('site.' . $credit . '_text') . $addnum . ',原有' . config('site.'.$credit.'_text') . $params[$credit] . ',现有' . config('site.'.$credit.'_text') . ($addnum + $params[$credit]));
                    if ($credit == 'credit1') {
                        db('user')->where('id',$params['id'])->setInc('credit1acc',abs($addnum));
                        Levelup::autolevelup($params['id']);
                    }
                }
                if ($cctype == '2') {
                    if ($params[$credit] < $addnum) {
                        $this->error(__('减去的不能大于现有的'));
                    }

                    setCc($info[0]['username'], $credit, -$addnum, '用户' . $info[0]['username'] . '减少' . config('site.' . $credit . '_text') . $addnum . ',原有' . config('site.'.$credit.'_text') . $params[$credit] . ',现有' . config('site.'.$credit.'_text') . ($params[$credit] - $addnum));
                }

                $this->success('修改成功!');

            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


}
