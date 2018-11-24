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


if($action=='banAccount')//封禁
{
	$uid=AKReplace(AKValue($_POST['uid']));
	$ban=(int)$_POST['ban'];//封禁：0=否；1=否；
	if($ban!=0&&$ban!=1)//非法类型
	{
		$json=array(
			result=>-1
		);
		echo json_encode($json);
		exit();
	}

	$rd=new AKRedis($redisInfoArr_account);	
	$row=$rd->updateRow
	(
		get_db_account_info(),
		'account','uid',$uid,'*',
		//修改回调
		function(&$row,&$userData)
		{
			global $ban;
			
			//修改
			$row['ban']=$ban;
			//var_dump($row);
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
	
	$json=array(
		result=>1
	);
	echo json_encode($json);
	exit();
}
else if($action=='saveAccount')//保存帐号信息
{
	$uid=AKReplace(AKValue($_POST['uid']));
	$type=(int)$_POST['type'];//帐号类型：0=普通用户；1=测试用户（可进维护中的游戏服）；
	if($type!=0&&$type!=1)//非法类型
	{
		$json=array(
			result=>-1
		);
		echo json_encode($json);
		exit();
	}

	$rd=new AKRedis($redisInfoArr_account);	
	$row=$rd->updateRow
	(
		get_db_account_info(),
		'account','uid',$uid,'*',
		//修改回调
		function(&$row,&$userData)
		{
			global $type;
			
			//修改
			$row['type']=$type;
			//var_dump($row);
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
	
	$json=array(
		result=>1
	);
	echo json_encode($json);
	exit();
}
else if($action=='list')//用户列表
{
	$pageSize=26;
	$page=(int)$_POST['page'];
	$offset=$page*$pageSize;
	
	$uid=AKReplace(AKValue($_POST['uid']));
	$type=(int)$_POST['type'];
	
	$data=array();
	
	$whereSign=false;
	$sql="select id,uid,type,ban,genTime,serverIDMap from account";
	if($uid!='')
	{
		if(!$whereSign)
		{
			$sql.=" where ";
			$whereSign=true;
		}
		else
		{
			$sql.=" and ";
		}
		$sql.="uid='$uid'";
	}
	if($type==0||$type==1)
	{
		if(!$whereSign)
		{
			$sql.=" where ";
			$whereSign=true;
		}
		else
		{
			$sql.=" and ";
		}
		$sql.="`type`=$type";
	}
	$sql.=" order by id desc limit $offset,$pageSize";
	//echo $sql;
	$cnn=new_db_cnn(get_db_account_info());
	$rs=$cnn->exec($sql);
	while($row=$cnn->getRow($rs))
	{
		$info=array(
			id=>$row['id'],
			uid=>$row['uid'],
			genTime=>$row['genTime'],
			type=>(int)$row['type'],
			ban=>(int)$row['ban'],
			serverIDMap=>$row['serverIDMap'],
		);
		array_push($data,$info);
	}
	$json=array(
		result=>1,
		data=>$data
	);
	echo json_encode($json);
	exit();
}
