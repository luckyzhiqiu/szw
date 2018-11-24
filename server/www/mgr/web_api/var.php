<?php
include_once(dirname(__FILE__)."/common.php");
$userID=AKValue($_POST['userID']);//用户ID
$tick=AKValue($_POST['tick']);//通行证
//校验通行证
mgrCheckClientTick($userID,$tick);
$action=$_POST['action'];
$genTime=date('Y-m-d H:i:s',time());

//$cnn=new_db_cnn(get_db_common_info());
//$rd=new AKRedis($redisInfoArr);

if($action=='loadVar')//获取变量json数据
{
	$varName=$_POST['varName'];
	
	$rd=new AKRedis($redisInfoArr);
	$userData=array();
	$rd->updateRow
	(
		get_db_common_info(),
		'global_var','name',$varName,'*',
		//修改回调
		function(&$var,&$userData)
		{
			$json=array(
				result=>1,
				data=>$var['json']
			);
			echo json_encode($json);
			return false;
		},
		//修改完成回调
		function(&$row,&$userData)
		{
		},
		//解锁后回调
		function(&$row,&$userData)
		{
		},
		$userData
	);
	exit();
}
else if($action=='saveVar')//保存用户json数据
{
	$varName=$_POST['varName'];
	$jsonBase64Str=AKValue($_POST['jsonBase64Str']);
	
	$rd=new AKRedis($redisInfoArr);
	$userData=array();
	$rd->updateRow
	(
		get_db_common_info(),
		'global_var','name',$varName,'*',
		//修改回调
		function(&$var,&$userData)
		{
			global $jsonBase64Str;
			$var['json']=$jsonBase64Str;
			$json=array(
				result=>1
			);
			echo json_encode($json);
			return true;
		},
		//修改完成回调
		function(&$row,&$userData)
		{
		},
		//解锁后回调
		function(&$row,&$userData)
		{
		},
		$userData
	);
	exit();
}
else if($action=='list')//变量列表
{
	$pageSize=25;
	$page=(int)$_POST['page'];
	$offset=$page*$pageSize;
	
	$gameUserID=(int)$_POST['gameUserID'];
	
	$cnn=new_db_cnn(get_db_common_info());
	
	$sql="select name from global_var";
	$sql.=" limit $offset,$pageSize";
	$rs=$cnn->exec($sql);
	$data=array();
	while($row=$cnn->getRow($rs))
	{
		$info=array(
			name=>$row['name']
		);
		array_push($data,$info);
	}
	//echo $sql;
	$json=array(
		result=>1,
		data=>$data
	);
	echo json_encode($json);
	exit();
}
