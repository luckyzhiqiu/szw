<?php
include_once(dirname(__FILE__)."/common.php");
//免密登录
$sign=$_GET['sign'];//签名

if(mgrCheckQuickLoginSign()==$sign)
{
	$userID=1;
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