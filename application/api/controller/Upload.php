<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/7/9
 * Time: 16:06
 */
namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use think\Request;
use think\Validate;
use think\Db;
use think\Model;


/**
 * 上传文件
 */
class Upload extends Api
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['upFile','downFile','getVersion'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = [];

    public function __construct()
    {
        parent::__construct();
    }

    private $_speed = 1024;  // 下载速度

    //上传问题件
    public function upFile(){

        if (request()->isPost()) {
            $file = request()->file('file');
            $vs = request()->post('version');
            if (empty($vs)) {
                $this->error('版本号不能为空');
            }
            if ($file) {
                // 移动到服务器的上传目录
                $ext = $file->checkExt('wgt');
                if (!$ext) {
                    $this->error('文件格式不正确');
                }
                //限制为 50m
                $size = $file->checkSize(50*1024*1024);
                if (!$size) {
                    $this->error('文件大小超过限制');
                }
                $arr = explode('.',$file->getInfo('name'));
                $ext = $arr[count($arr) - 1];
                array_pop($arr);
                $filename = implode($arr);

                $res = $file->move('uploads'. DS .'file',$filename.'('.$vs.').'.$ext);
                if ($res) {
                    $pathname = $res->getPathname();
                    $oldname = $file->getInfo('name');
                    $newname = $res->getSaveName();
                    db('app_version')->insert(['version' => $vs,'url' => $pathname,'old_name' => $oldname,'new_name' => $newname,'createtime' => time()]);
                    $this->success('上传成功',$res->getPathname());
                }else{
                    $this->error('上传失败');
                }

            }else{
                $this->error(__('文件不能为空'));
            }
        }else{
            $this->error(__('非法请求'));
        }

    }

    //下载文件
    /*public function downFile(){
        $vs = input('get.version');
        $info = [];
        if ($vs) {
            $info = db('app_version')->where('version',$vs)->field('url,new_name')->find();
        }else{
            $info = db('app_version')->where('id','>',0)->order('id','desc')->field('url,new_name')->find();
        }
        //获取最新的版本
        //$file_url = 'uploads'. DS .'file'. DS .'test.wgt';
        $file_url = $info['url'];
        //$file_name=basename($file_url);
        $file_type=explode('.',$file_url);
        $file_type=$file_type[count($file_type)-1];

        $file_type=fopen($file_url,'r'); //打开文件
        //输入文件标签
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length: ".filesize($file_url));
        header("Content-Disposition: attachment; filename=".$info['new_name']);
        //输出文件内容
        echo fread($file_type,filesize($file_url));
        fclose($file_type);
    }*/

    //获取版本号
    public function getVersion(){
        $vs = db('app_version')->where('id','>',0)->order('id','desc')->field('version')->find();
        if ($vs) {
            $this->success('返回成功',$vs['version']);
        }else{
            $this->error('暂时还没有历史版本');
        }
    }



    /** 下载
     * @param String $file  要下载的文件路径
     * @param String $name  文件名称,为空则与下载的文件名称一样
     * @param boolean $reload 是否开启断点续传
     */
    public function downFile($file='', $name='', $reload=false){
        $vs = input('get.version');
        $info = [];
        if ($vs) {
            $info = db('app_version')->where('version',$vs)->field('url,new_name')->find();
        }else{
            $info = db('app_version')->where('id','>',0)->order('id','desc')->field('url,new_name')->find();
        }
        if (!$info) {
            return false;
        }
        //获取最新的版本
        //$file_url = 'uploads'. DS .'file'. DS .'test.wgt';
        $file = $info['url'];
        $name = $info['new_name'];
        $reload = true;

        //设置下载速度
        //$this->setSpeed(512);

        if(file_exists($file)){
            if($name==''){
                $name = basename($file);
            }

            $fp = fopen($file, 'rb');
            $file_size = filesize($file);
            $ranges = $this->getRange($file_size);

            header('cache-control:public');
            header('content-type:application/octet-stream');
            header('content-disposition:attachment; filename='.$name);

            if($reload && $ranges!=null){ // 使用续传
                header('HTTP/1.1 206 Partial Content');
                header('Accept-Ranges:bytes');

                // 剩余长度
                header(sprintf('content-length:%u',$ranges['end']-$ranges['start']));

                // range信息
                header(sprintf('content-range:bytes %s-%s/%s', $ranges['start'], $ranges['end'], $file_size));

                // fp指针跳到断点位置
                fseek($fp, sprintf('%u', $ranges['start']));
            }else{
                header('HTTP/1.1 200 OK');
                header('content-length:'.$file_size);
            }

            while(!feof($fp)){
                echo fread($fp, round($this->_speed*1024,0));
                ob_flush();
                sleep(1); // 用于测试,减慢下载速度
            }

            ($fp!=null) && fclose($fp);

        }else{
            return '';
        }
    }


    /** 获取header range信息
     * @param int  $file_size 文件大小
     * @return Array
     */
    private function getRange($file_size){
        if(isset($_SERVER['HTTP_RANGE']) && !empty($_SERVER['HTTP_RANGE'])){
            $range = $_SERVER['HTTP_RANGE'];
            $range = preg_replace('/[\s|,].*/', '', $range);
            $range = explode('-', substr($range, 6));
            if(count($range)<2){
                $range[1] = $file_size;
            }
            $range = array_combine(array('start','end'), $range);
            if(empty($range['start'])){
                $range['start'] = 0;
            }
            if(empty($range['end'])){
                $range['end'] = $file_size;
            }
            return $range;
        }
        return null;
    }

    /** 设置下载速度
     * @param int $speed
     */
    public function setSpeed($speed){
        if(is_numeric($speed) && $speed>16 && $speed<4096){
            $this->_speed = $speed;
        }
    }
}