<?php
define('API_URL', 'https://dnsapi.cn/'); //API接口url
define('ROOT', dirname(__FILE__) . '/'); //根目录
define('LOG_PATH', dirname(__FILE__) . '/'); //log存放路径
define('CONFIG_PATH', dirname(__FILE__) . '/'); //配置文件路径
define('CACHE_IPS_FILE', dirname(__FILE__) . '/cacheIPs.txt'); //ip缓存文件

!file_exists(CONFIG_PATH . 'config.php') && exit('配置文件不存在');

$config_arr = include(CONFIG_PATH . 'config.php');
$TOKEN      = $config_arr['TOKEN_ID'] .','. $config_arr['TOKEN'];
$ServerChan_Url = $config_arr['SERVERCHAN_URL'].$config_arr['SERVERCHAN_KEY'].'.send';

require 'functions.php';
require 'ddns.php';

$ddns = new Ddns($TOKEN, $config_arr['DOMAIN'], $config_arr['SUB']); //实例化

if (!file_exists(CACHE_IPS_FILE)) {
    $isUpdate = false;
    $ddns->cacheIPs(8);
}

$isUpdate = isset($isUpdate) ? false : $ddns->checkIP(); //判断ip是否发生变化

if ($isUpdate == false) {
    $recordInfo = $ddns->getAllRecordData('info', $config_arr['SUB']); //当前域名记录数
    if ($recordInfo['record_total'] == 0) {
        //执行新增操作
        $ddns->createRecord(); //留空则默认为当前外网ip
    } else {
        //执行修改操作
        $ddns->modifyRecord();
        $title = 'ip地址发生变化啦～';
        $content = '你的ip地址已经发生变化啦，当前ip地址是：' . $ddns->ip;
        talkToServerChan($ServerChan_Url, $title, $content);
    }
} else {
    echo '当前ip与记录ip一致，无需修改';
}

exit();





