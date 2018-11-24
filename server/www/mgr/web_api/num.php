<?php
include_once(dirname(__FILE__)."/common.php");
$userID=AKValue($_POST['userID']);//用户ID
$tick=AKValue($_POST['tick']);//通行证
//校验通行证
mgrCheckClientTick($userID,$tick);
$action=$_POST['action'];
//$genTime = date('Y-m-d H:i:s',time());

//$cnn=new_db_cnn(get_db_common_info());
//$rd=new AKRedis($redisInfoArr);

$fileDir='../../json_table/';

if($action=='list')
{
	$data=array();
	$dir=opendir($fileDir);
	while($fileName= readdir($dir))
	{
		if($fileName=="."||$fileName=="..")
		{
		}
		else
		{
			array_push($data,$fileName);
		}
	}
	$json=array(
		result=>1,
		data=>$data
	);
	echo json_encode($json);
}
else if($action=='load')
{
	$name=AKValue($_POST['name']);
	if($name=='')//数值名称为空
	{
		echo '{"result":-1}';
		exit();
	}
	$jsonStr=file_get_contents("$fileDir$name.json");
	$json=array(
		result=>1,
		data=>array(
			base64JsonStr=>base64_encode($jsonStr)
		)
	);
	echo json_encode($json);
}
else if($action=='save')
{
	$name=AKValue($_POST['name']);
	if($name=='')//数值名称为空
	{
		echo '{"result":-1}';
		exit();
	}
	//保存
	$jsonStr=base64_decode(AKValue($_POST['base64JsonStr']));
	file_put_contents("$fileDir$name.json",$jsonStr);
	//更新数值表版本号
	$verUrl='ver.txt';
	file_put_contents("$fileDir$verUrl",time());
	echo '{"result":1}';
	exit();
}
else if($action=='remove')
{
	$name=AKValue($_POST['name']);
	if($name=='')//数值名称为空
	{
		echo '{"result":-1}';
		exit();
	}
	unlink("$fileDir$name.json");
	echo '{"result":1}';
	exit();
}
?>