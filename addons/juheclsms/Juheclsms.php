<?php

namespace addons\juheclsms;

use think\Addons;

/**
 * 聚合短信插件
 */
class Juheclsms extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 短信发送行为
     * @param   Sms     $params
     * @return  boolean
     */
    public function juhesmsSend(&$params)
    {
        $juheclsms = new library\Juheclsms();

        $result = $juheclsms->send($params);

        return $result;
    }

    /**
     * 短信发送通知
     * @param   array   $params
     * @return  boolean
     */
    public function smsNotice(&$params)
    {
        $clsms = new library\Juheclsms();
        $result = $clsms->smstype(1)->mobile($params['mobile'])
                ->msg($params['msg'])
                ->send();
        return $result;
    }

    /**
     * 检测验证是否正确
     * @param   Sms     $params
     * @return  boolean
     */
    public function smsCheck(&$params)
    {
        return TRUE;
    }

}
