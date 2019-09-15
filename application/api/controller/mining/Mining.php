<?php
/**
 * 抢购矿的相关操作.
 * User: Administrator
 * Date: 2019/3/30
 * Time: 9:15
 */

namespace app\api\controller\mining;

use app\common\controller\Api;
use app\common\model\Sms;
use EasyWeChat\Core\Exception;
use think\Db;

class  Mining extends Api
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = []; //['handle'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = '*';

    // 分页参数
    // 一个页面显示几条数据
    protected $pagesize = 8;
    // 页码
    protected $page = 1;
    // 总页数
    protected $totalpage = 0;
    //订单表的实例化对象
    protected $db = null;


    public function __construct()
    {
        parent::__construct();
        //获取系统设置的每页显示的数据条数
        $ps = config('paginate.list_rows');
        if ($ps > 0) {
            $this->pagesize = $ps;
        }
        // 获取分页参数
        $this->page = input('get.page') ? intval(input('get.page')) : $this->page;
        //实例化订单表的对象
        $this->db = db('ore_order');
    }

    //清除限制 测试使用
    public function clearOnly(){
        $redis = new \Redis();
        $redis->connect(config('redis.host'),config('redis.port'));
        $keys = $redis->keys('ol*');
        if ($keys) {
            foreach ($keys as $val) {
                $redis->del($val);
            }
        }
        $redis->close();
    }

    //抢矿
    public function index(){

        $level = input('get.level/d',0);

        if ($level > 0) {
            $tm = time();
            //获取期数 年月日+等级
            $periods = getPer($level);

            //抢购的数据先写入redis缓存
            $redis = new \Redis();
            $redis->connect(config('redis.host'),config('redis.port'));

            //当天结束时间
            $expireTime = mktime(23, 59, 59, date("m"), date("d"), date("Y"));

            //判断是否有该等级的信息，如果有，则不用去查询，没有则进行查询
            $lvs = json_decode($redis->hGet('block_level','lvs'.$level),true);
            if (!$lvs) {
                $lvs = db('block_ore_level')->where('level',$level)->field('stime,etime,money2,level,id')->find();
                $lstr = json_encode($lvs);
                $redis->hSet('block_level','lvs'.$level,$lstr);
                //设置等级信息当天有效
                $redis->expireAt('block_level', $expireTime);
            }


            //倒计时结束，处于抢购中的时间段
            if (date('His',$tm) >= date('His',$lvs['stime']) && date('His',$tm) <= date('His',$lvs['etime'])) {
                //可以抢购

                //测试暂时使用
                $cs = db('user')->where('id',input('get.id/s'))->field('id,username,weights')->find();
                $this->auth->id = $cs['id'];
                $this->auth->username = $cs['username'];
                $this->auth->weights = $cs['weights'];

                //一天一个矿只能点击一次立即抢购
                if ($redis->hGet('ol'.$periods,$this->auth->username)) {
                    $this->error(__('该'.config('site.ore_text').'今天已抢完，请明天再来~'));
                }else{
                    $redis->hSet('ol'.$periods,$this->auth->username,$this->auth->username);
                }

                try{
                    //禁止排单的直接记录
                    if ($this->auth->weights > 0) {
                        $goods_info = [
                            'id' => $this->auth->id,
                            'username' => $this->auth->username,
                            'weights' => $this->auth->weights,
                            'level' => $level,
                            'periods'   => date('Ymd').$level,
                            'times' => microtime(true)
                        ];
                        $redis->hSet($periods,$this->auth->username,json_encode($goods_info));
                        $redis->expireAt($periods,$expireTime);
                    }
                    $this->success('您抢购的意向已记录，5分钟后会出结果，请留意！');
                }catch(Exception $e){
                    $this->error('服务器错误，请重试！',$e->getMessage()) ;
                }

            }
            else{
                if (date('His',$tm) < date('His',$lvs['stime'])) {
                    $res['status'] = 0;
                    $this->error('还没到抢购时间',$res);
                }
                if (date('His',$tm) > date('His',$lvs['etime'])) {
                    $res['status'] = -1;
                    $this->error('错过了抢购时间',$res);
                }
            }
            $redis->close();
        }else{
            $this->error('参数错误');
        }
    }

    //查询抢购的结果
    public function getOreResult(){
        trace('info','访问('.date('Y-m-d H:i:s').'):'.$this->auth->username);
        $redis = new \Redis();
        $redis->connect(config('redis.host'),config('redis.port'));
        if (!$this->auth->username) {
            $redis->close();
            $this->error(__('请检查登录状态'),null,401);
        }

        //redis 前缀
        $rsp = config('redis.prefix');

        //判断数据是否处理完成
        //$wc = $redis->get('dqwc');
        $run = $redis->get($rsp.'_runing');

        //随机拒绝一些访问
        if (mt_rand(0,1) == 1) {
            $redis->close();
            $this->error(__('正在处理数据，请稍后再查询！'));
        }

        if ($run == 1) {
            $level = input('post.level/d',0);
            $periods = getPer($level);
            $ishas = $redis->get($periods.'_wc_'.$this->auth->username);
            if ($ishas == $this->auth->username) {
                //精确到微秒的暂停 usleep()单位是微秒，1秒 = 1000毫秒 ，1毫秒 = 1000微秒，即1微秒等于百万分之一秒
                usleep(mt_rand(1000,1000000));
                $res = db('ore_order')
                    ->where('buy_username',$this->auth->username)
                    ->where('status',0)
                    ->where('status2',0)
                    ->where('status3',0)
                    ->where('status4',0)
                    ->where('status5',0)
                    ->where('periods',$periods)
                    ->value('id');
                trace('info','查询结果('.date('Y-m-d H:i:s').'):'.$periods.'--'.$this->auth->username.'--'.$res);
                if ($res > 0) {
                    trace('info','成功返回('.date('Y-m-d H:i:s').'):'.$periods.'--'.$this->auth->username.'--'.$res);
                    $redis->close();
                    $this->success(__('抢购成功'),['url' => '/pan-gold/buy-result?type=success']);
                }else{
                    trace('info','(抢购失败1)查询结果('.date('Y-m-d H:i:s').'):'.$periods.'--'.$this->auth->username);
                    $redis->close();
                    $this->success(__('抢购失败'),['url' => '/pan-gold/buy-result?type=fail']);
                }
            }else{
                trace('info','(抢购失败2)查询结果('.date('Y-m-d H:i:s').'):'.$periods.'--'.$this->auth->username);
                $redis->close();
                $this->success(__('抢购失败'),['url' => '/pan-gold/buy-result?type=fail']);
            }
        }else{
            $redis->close();
            $this->error(__('正在处理数据，请稍后再查询！'));
        }
    }

}