<?php
include_once(dirname(__FILE__)."/../../ak/AKCnn.php");
//////////////////////////////////////
//游戏服信息列表rd键名
$gameServerListKey=$cachePreName.'_gameServerList';
//全局数据库
$db_global_info=array
(
	'ip'=>'test1.aawan.cn',
	'port'=>'3306',
	'usr'=>'root',
	'pwd'=>'c543008d622d8d63505d909760b7a184',
	'db'=>'szw_global',
);
//帐号信息数据库
$db_account_info=array
(
	'ip'=>'test1.aawan.cn',
	'port'=>'3306',
	'usr'=>'root',
	'pwd'=>'c543008d622d8d63505d909760b7a184',
	'db'=>'szw_account',
);
//游戏服数据库
$db_common_info=array
(
	'ip'=>'127.0.0.1',
	'port'=>'3306',
	'usr'=>'root',
	'pwd'=>'c543008d622d8d63505d909760b7a184',
	'db'=>'szw001',
);
//////////////////////////////////////
//根据键名计算索引值
function getIndexFromKey($key,$count)
{
	$id=abs(crc32($key))%$count;
	return $id;
}
//获取通用数据库信息
function get_db_common_info()
{
	global $db_common_info;
	return $db_common_info;
}
//获取全局数据库信息
function get_db_global_info()
{
	global $db_global_info;
	return $db_global_info;
}
//获取帐号信息数据库信息
function get_db_account_info()
{
	global $db_account_info;
	return $db_account_info;
}
//////////////////////////////////////
?>