<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/18
 * Time: 10:11
 */

namespace app\admin\model;

use think\Db;
use think\Model;
use think\Config;

class Initialization extends Model
{
    public function index()
    {
        // 获取extra文件夹中的inisql.php文件中的数据
        $data = Config::get('initsql');
        if(!empty($data)){
            $prefix = config("database.prefix");
            foreach($data as $val){
                $sql = 'truncate table '.$prefix.$val.';';
                $this->query($sql);
            }
        }

    }
}