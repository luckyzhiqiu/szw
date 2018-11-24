<?php
include_once(dirname(__FILE__)."/common.php");

$account=AKReplace(AKValue($_POST['account']));
$password=AKReplace(AKValue($_POST['password']));

$cnn=new_db_cnn(get_db_global_info());//连接通用数据库

//创建admin
$sql="select id from mgr_user limit 1";
$rs=$cnn->exec($sql);
$row=$cnn->getRow($rs);
if(!$row)
{
	$curTime=date("Y-m-d H:i:s",time());
	$sql="insert into mgr_user set genTime='$curTime',account='admin',password='".md5('123')."'";
	$cnn->exec($sql);
}

//登录验证
$sql="select id from mgr_user where account='$account' and password='$password' limit 1";
$rs=$cnn->exec($sql);
if($row=$cnn->getRow($rs))
{
	$userID=(int)$row['id'];
	$tick=mgrGetClientTick($userID);
	$result=array
	(
		result=>1,
		userID=>$userID,
		tick=>$tick
	);
	echo json_encode($result);
}
else
{
	$result=array
	(
		result=>0
	);
	echo json_encode($result);
}