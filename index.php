<?php
define('API_URL', 'https://dnsapi.cn/');
require 'Model/ddns.php';

use Model\Ddns;

/******************************************************************
 *    配置部分
 ******************************************************************/
$config_arr = include 'config.php';
$TOKEN = $config_arr['TOKEN_ID'] .','. $config_arr['TOKEN'];

/*******************************************************************
 *    运行
 *******************************************************************/
$ddns = new Ddns($TOKEN, $config_arr['DOMAIN'], $config_arr['SUB']);

//$ip         = $ddns->getMyIP(); // 当前外网ip
$recordInfo = $ddns->getAllRecordData('info', $config_arr['SUB']); //当前域名记录数
$recordId   = $ddns->getSubDomainRecordId($config_arr['SUB']); //获取子域名的记录id

if ($recordInfo['record_total'] == 0) {
    //执行新增操作
    $ddns->createRecord();
} else {
    //执行修改操作
    $ddns->modifyRecord();
}




