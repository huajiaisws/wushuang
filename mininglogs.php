<?php
/**
 * 每期抢购的信息日志记录，做定时任务，每天凌晨前执行 23:55:00.
 * User: admin
 * Date: 2019/6/4
 * Time: 11:51
 */

if (!defined("APP_PATH")) {
    define('APP_PATH', __DIR__ . '/application/');
    // 加载框架引导文件
    require __DIR__ . '/thinkphp/start.php';
}

function miningLogs(){
    echo '开始执行的时间：'.date('Y-m-d H:i:s')."\r\n";
    //基础路径
    $basepath = RUNTIME_PATH . 'mininglogs/';

    $redis = new \Redis();
    $redis->connect(config('redis.host'),config('redis.port'));

    //抢购成功的信息
    $suc_info = serialize($redis->hGetAll('success_data'));
    //抢购信息
    $log_content = serialize($redis->hGetAll('backups'));

    //判断改目录是否存在，如果不存在则创建
    !is_dir($basepath) && mkdir($basepath, 0755, true);
    //日志文件名
    $log_filename = $basepath . date('Ym-d') . ".log";
    //资源句柄
    $myfile = fopen($log_filename, "a+");
    //写入的内容
    $txt = "抢购的信息：\r\n".$log_content."\r\n抢购成功的信息：\r\n".$suc_info."\r\n";
    //写入内容
    fwrite($myfile, $txt);

    //写入日志后，清除缓存
    $redis->del('backups');
    $redis->del('success_data');
    //关闭redis
    $redis->close();

    //关闭资源句柄
    fclose($myfile);//关闭该操作
    echo '执行结束的时间：'.date('Y-m-d H:i:s')."\r\n";
}

miningLogs();