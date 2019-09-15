<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/14 0014
 * Time: 14:23
 */

namespace app\common\behavior;
use fast\Http;

class Walletapi
{
    public function _initialize(){
        //检测是否启用钱包
        if(!config('site.bcw_enable')){
            return __('钱包尚未启用');
        }
    }
    
    /**
     * 初始化创建会员钱包
     * @param $id 会员id
     */
    public static function createwallet($id){
        $config=config('wallet');
        
        if(empty($config['port'])){
            $url=$config['server'].$config['init'];
        }else{
            $url=$config['server'].':'.$config['port'].$config['init'];
        }
        $data['appId']=$config['appId'];
        $data['appSecret']=$config['appSecret'];
        $data['walletType']=$config['walletType'];
        $data['openId']=$id;
        $res=json_decode(Http::post($url,$data),true);
        if($res['code']==200){
            $addr=$res['data'][$config['coinName']];
            $user = db("user_detail")->field("walletaddr")->find();
            return $addr;
        }
      
    }
    
    /**
     * 获得钱包订单信息
     * @param $hash 交易hash值
     * @param $walletType 钱包类型
     * @param $coinName 币种名字
     */
    public static function gettx($hash,$walletType,$coinName){
        $config=config('wallet');
        
        if(empty($config['port'])){
            $url=$config['server'].$config['getTx'];
        }else{
            $url=$config['server'].':'.$config['port'].$config['getTx'];
        }
        $data['hash']=$hash;
        $data['walletType']=$walletType;
        $data['coinName']=$coinName;
        $data['appId']=$config['appId'];
        $data['appSecret']=$config['appSecret'];
        $res=json_decode(Http::post($url,$data),true);
        return $res;
    }
    
    /**
     * 获取节点余额
     * @param $hash 交易hash值
     */
    public static function amount(){
        $config=config('wallet');
        if(empty($config['port'])){
            $url=$config['server'].$config['amount'];
        }else{
            $url=$config['server'].':'.$config['port'].$config['amount'];
        }
        $data['appId']=$config['appId'];
        $data['appSecret']=$config['appSecret'];
        $data['walletType']=$config['walletType'];
        $res=json_decode(Http::post($url,$data),true);
      
        return $res['data'];
    }
    
    /**
     * 资金归集
     * @param $hash 交易hash值
     */
    public static function collection($addr){
        $config=config('wallet');
        if(empty($config['port'])){
            $url=$config['server'].$config['collection'];
        }else{
            $url=$config['server'].':'.$config['port'].$config['collection'];
        }
        $data['appId']=$config['appId'];
        $data['appSecret']=$config['appSecret'];
        $data['walletType']=$config['walletType'];
        $data['addr']=$addr;
        $res=json_decode(Http::post($url,$data),true);
        return $res;
        
    }
    
    //Exmo交易所行情接口
    public static function Exmo($coin,$type='USD'){
        $burl=config('Trade.Exmo');
        $url=$burl.$coin.'_'.$type;//https://api.exmo.me/v1/order_book/?pair=DASH_USD
        $res=json_decode(Http::get($url),true);
        $data=sprintf('%.2f',$res[$coin.'_'.$type]['bid_top']);
        return $data;
    }
    public static function Exmoall($coin,$type='USD'){
        $url=config('Trade.Exmoall');
        $res=json_decode(Http::get($url),true);
        foreach ($coin as $v) {
            $data[$v]=sprintf('%.2f',$res[$v.'_'.$type]['buy_price']);
        }
        return $data;
    }

    

   


}