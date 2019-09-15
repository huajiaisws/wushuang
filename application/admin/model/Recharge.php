<?php


namespace app\admin\model;

use think\Model;

class Recharge extends Model
{

    // 表名
    protected $name = 'user_recharge_log';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [];

	public function userDetail()
	{
		return $this->belongsTo('UserDetail', 'uid', 'uid', [], 'LEFT')->setEagerlyType(0);
	}
}