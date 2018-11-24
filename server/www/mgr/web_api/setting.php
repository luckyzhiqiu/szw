<?php
include_once(dirname(__FILE__)."/common.php");
$userID=AKValue($_POST['userID']);//用户ID
$tick=AKValue($_POST['tick']);//通行证
//校验通行证
mgrCheckClientTick($userID,$tick);
$action=$_POST['action'];
$genTime=date('Y-m-d H:i:s',time());

if($action=='modifPassword')//修改密码
{
	$password=AKReplace(AKValue($_POST['password']));
	
	$sql="update mgr_user set `password`='$password' where id=$userID";
	$cnn=new_db_cnn(get_db_global_info());
	$cnn->exec($sql);
	
	$json=array(
		result=>1
	);
	echo json_encode($json);
	exit();
}