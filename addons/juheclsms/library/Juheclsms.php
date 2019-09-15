<?php

namespace addons\juheclsms\library;

/**
 * 聚合短信发送
 * 如有问题，请加微信  andiff424  QQ:165607361
 */
class Juheclsms
{

    private $_params = [];
    protected $error = '';
    protected $config = [];

    public function __construct($options = [])
    {
        if ($config = get_addon_config('juheclsms'))
        {
            $this->config = array_merge($this->config, $config);
        }
        $this->config = array_merge($this->config, is_array($options) ? $options : []);
    }

    /**
     * 单例
     * @param array $options 参数
     * @return Clsms
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance))
        {
            self::$instance = new static($options);
        }
        return self::$instance;
    }

    /**
     * 立即发送短信
     *
     * @return boolean
     */
//    public function senddemo()
//    {
//        $this->error = '';
//        $params = $this->_params();
//        $postArr = array(
//            'account'  => $params['account'],
//            'password' => $params['password'],
//            'msg'      => $params['msg'] . ($params['smstype'] ? '退订请回T.' . $params['sign'] : $params['sign']),
//            'phone'    => $params['mobile'],
//            'report'   => $params['report']
//        );
//        $options = [
//            CURLOPT_HTTPHEADER => array(
//                'Content-Type: application/json; charset=utf-8'
//            )
//        ];
////        $result = \fast\Http::sendRequest('http://smssh1.253.com/msg/send/json', json_encode($postArr), 'POST', $options);
////        $result = \fast\Http::sendRequest('http://intapi.253.com/send/json', json_encode($postArr), 'POST', $options);
//        $result = $this->curlPost( 'http://intapi.253.com/send/json', $postArr);
//        //查询参数
//        if ($result['ret'])
//        {
//            $res = (array) json_decode($result['msg'], TRUE);
//            if (isset($res['code']) && $res['code'] == '0')
//                return TRUE;
//            $this->error = isset($res['Message']) ? $res['Message'] : 'InvalidResult';
//        }
//        else
//        {
//            $this->error = $result['msg'];
//        }
//        return FALSE;
//    }

    public function send($params) {
        //聚合接口参数 使用绝对路径调用的时候要注释，通过URL访问定时任务需要开启
        //header('content-type:text/html;charset=utf-8');
        //短信类型
        $istype = $params['event'];

        //获取插件配置
        $config = $this->config;

        //短信模板id
        $tpl_id = $config["$istype"];

        $sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL

        $smsConf = array(
            'key'   => $config['key'], //您申请的APPKEY
            'mobile'    => $params['mobile'], //接受短信的用户手机号码
            'tpl_id'    => $tpl_id, //您申请的短信模板ID，根据实际情况修改
            'tpl_value' =>"#code#=".$params['code']."&#company#=聚合数据" //您设置的模板变量，根据实际情况修改
        );

        $content = $this->juhecurl($sendUrl,$smsConf,1); //请求发送短信

        if($content){
            $result = json_decode($content,true);
            $error_code = $result['error_code'];
            if($error_code == 0){
                //状态为0，说明短信发送成功
                return TRUE;
            }else{
                //状态非0，说明失败
                $msg = $result['reason'];
                $this->error = "短信发送失败(".$error_code.")：".$msg;
            }
        }else{
            //返回内容异常，以下可根据业务逻辑自行修改
            return FALES;
        }
    }

    function juhecurl($url,$params=false,$ispost=0){
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if( $ispost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }


    private function _params()
    {
        $smstype = isset($this->_params['smstype']) ? $this->_params['smstype'] : 0;
        return array_merge([
            'smstype'  => $smstype,
            'account'  => ($smstype ? $this->config['key1'] : $this->config['key']),
            'password' => ($smstype ? $this->config['secret1'] : $this->config['secret']),
            'sign'     => $this->config['sign'],
            'report'   => true,
        ], $this->_params);
    }

    /**
     * 获取错误信息
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 短信类型
     * @param   string    $st       0验证码1会员营销短信（会员营销短信不能测试）
     * @return Clsms
     */
    public function smstype($st = 0)
    {
        $this->_params['smstype'] = $st;
        return $this;
    }

    /**
     * 接收手机
     * @param   string  $mobile     手机号码
     * @return Clsms
     */
    public function mobile($mobile = '')
    {
        $this->_params['mobile'] = $mobile;
        return $this;
    }

    /**
     * 短信内容
     * @param   string  $msg        短信内容
     * @return Clsms
     */
    public function msg($msg = '')
    {
        $this->_params['msg'] = $msg;
        return $this;
    }

    public function curlPost($url,$postFields){
        $postFields = json_encode($postFields);
        $ch = curl_init ();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8'
            )
        );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt( $ch, CURLOPT_TIMEOUT,1);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = curl_exec ( $ch );
        if (false == $ret) {
            $result = curl_error(  $ch);
        } else {
            $rsp = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
            if (200 !== $rsp) {
                $result = "请求状态 ". $rsp . " " . curl_error($ch);
            } else {
                $result = $ret;
            }
        }
        curl_close ( $ch );
        return $result;
    }

}
