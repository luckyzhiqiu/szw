<?php
header('Access-Control-Allow-Origin:*');
include_once(dirname(__FILE__)."/../../config/mgr/config.php");
//后台私钥
$mgrPrivateKey='c80065485ead57f9165fdd09cae0d355';

//获取后台用户通行证
function mgrGetClientTick($userID)
{
	global $mgrPrivateKey;
	$curDate=date('Y-m-d');
	return md5($userID.$mgrPrivateKey.$curDate);
}

//校验用户通行证
function mgrCheckClientTick($userID,$tick)
{
	$rightTick=mgrGetClientTick($userID);
	if($tick!=$rightTick)//校验失败
	{
		echo '{"result":0}';
		exit();
	}
}

//免密登录签名
function mgrCheckQuickLoginSign()
{
	global $mgrPrivateKey;
	$curDate=date('Y-m-d');
	return md5($mgrPrivateKey.$curDate);
}

//创建实例表
function creatTable($cnn,$tableName,$copyTable)
{
	$sql="create table if not exists $tableName like $copyTable";
	$rs=$cnn->exec($sql);
}

//发送开服通知
function openInform($cnn,$serverID)
{
	$sql="select * from game_server where id=$serverID";
	$rs=$cnn->exec($sql);
	$row=$cnn->getRow($rs);
	if(!$row)
	{
		//游戏服ID不存在
		echo '{"result":-1}';
		exit();
	}
	$time=time();//当前时间戳
	$server_open_time=$row['openTime'];//生成时间
	$server_status=$row['status'];//状态：0=待开；1=新服；2=火爆；
	$server_name=$row['serverName'];//游戏服名称
	$service_ip=$row['gameServerIP'];//游戏服IP
	$service_port=$row['gameServerPort'];//游戏服port
	$db_ip=$row['dbIP'];//数据库IP
	$db_port=$row['dbPort'];//数据库port

	$url="http://szw.task01.jingh5.com:8080/api/Infosyncszw/SyncServerinfo?product_code=szw";// api测试地址
	$key="e9c3b7f1af0b451d386f6221b09809d2";
	$product_code="szw";//
	$platform_code="szw";//渠道名称
	$server_type=2;//第几区
	$db_name=$row['dbName'];//"game_jipin";
	$sign=md5($product_code."&".$serverID."&".$service_ip."&".$server_open_time."&".$db_ip."&".$time."&".$key);

	$url.="&service_id=".$serverID;
	$url.="&service_ip=".$service_ip;
	$url.="&server_open_time=".$server_open_time;
	$url.="&db_ip=".$db_ip;
	$url.="&time=".$time;
	$url.="&sign=".$sign;
	$url.="&platform_code=".$platform_code;
	$url.="&service_port=".$service_port;
	$url.="&server_name=".$server_name;
	$url.="&server_type=".$server_type;
	$url.="&server_status=".$server_status;
	$url.="&db_name=".$db_name;
	$url.="&db_port=".$db_port;
	$url.="&game_port=0";
	$url.="&recharge_port=0";
	$url.="&client_version=''";
	$url.="&merge_server=''";
	$url.="&merge_time=0";

	$rt=AKSubmitForm($url);
	return $rt;
}

?>