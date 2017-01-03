<?php
define('API_URL', 'https://dnsapi.cn/'); //API接口url
define('LOG_PATH', dirname(__FILE__) . '/'); //log存放路径
define('CONFIG_PATH', dirname(__FILE__) . '/'); //配置文件路径

require 'Model/ddns.php';

use Model\Ddns;

!file_exists(CONFIG_PATH . 'config.php') && exit('配置文件不存在');

$config_arr = include(CONFIG_PATH . 'config.php');
$TOKEN = $config_arr['TOKEN_ID'] .','. $config_arr['TOKEN'];

$ddns = new Ddns($TOKEN, $config_arr['DOMAIN'], $config_arr['SUB']); //实例化

$recordInfo = $ddns->getAllRecordData('info', $config_arr['SUB']); //当前域名记录数

if ($recordInfo['record_total'] == 0) {
    //执行新增操作
    $ddns->createRecord(); //留空则默认为当前外网ip
} else {
    //执行修改操作
    $ddns->modifyRecord();
}




