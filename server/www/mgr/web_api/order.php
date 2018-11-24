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

if($action=='checkOrder')//验证订单
{
	$num=AKReplace(AKValue($_POST['gameUserID']));//游戏订单
	
	$json=array(
		result=>1,
		data=>$data
	);
	echo json_encode($json);
	exit();
}
else if($action=='rmb')//收入
{
	$sql="select sum(rmb) from `order` where status=1";
	
	$rmb=0;
	$cnn=new_db_cnn(get_db_common_info());
	$rs=$cnn->exec($sql);
	if($row=$cnn->getRow($rs))
	{
		$rmb=(int)$row[0];
	}
	$json=array(
		result=>1,
		rmb=>$rmb
	);
	echo json_encode($json);
	exit();
}
else if($action=='list')//列表
{
	$pageSize=25;
	$page=(int)$_POST['page'];
	$offset=$page*$pageSize;
	
	$gameUserID=(int)$_POST['gameUserID'];//userID
	$status=(int)$_POST['status'];//付款状态
	$get=(int)$_POST['get'];//发货状态
	$num=AKReplace(AKValue($_POST['num']));//游戏订单
	$order_no=AKReplace(AKValue($_POST['order_no']));//平台订单
	
	$data=array();
	
	$whereSign=false;
	$sql="select id,userID,`status`,`get`,`num`,order_no,genTime,rmb,`type` from `order`";
	if($gameUserID!=0)
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
		$sql.="userID=$gameUserID";
	}
	if($status!=-1)
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
		$sql.="`status`=$status";
	}
	if($get!=-1)
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
		$sql.="`get`=$get";
	}
	if($num!='')
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
		$sql.="`num`='$num'";
	}
	if($order_no!='')
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
		$sql.="`order_no`='$order_no'";
	}
	$sql.=" order by id desc limit $offset,$pageSize";
	//echo $sql;
	$cnn=new_db_cnn(get_db_common_info());
	$rs=$cnn->exec($sql);
	while($row=$cnn->getRow($rs))
	{
		//userID,`status`,`get`,`num`,order_no,genTime
		$info=array(
			id=>$row['id'],
			userID=>$row['userID'],
			status=>$row['status'],
			get=>$row['get'],
			num=>$row['num'],
			order_no=>$row['order_no'],
			genTime=>$row['genTime'],
			rmb=>$row['rmb'],
			type=>$row['type'],
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
