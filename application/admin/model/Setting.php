<?php
/**
 * 系统参数设置
 * User: Administrator
 * Date: 2019/3/27
 * Time: 11:33
 */

namespace app\admin\model;

use think\Model;

class Setting extends Model
{

    // 表名
    protected $name = 'system_setting';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
        // 'prevtime_text',
        // 'logintime_text',
        // 'jointime_text'
    ];
    protected $bkey = 'system';

}