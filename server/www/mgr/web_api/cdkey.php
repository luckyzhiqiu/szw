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

function genSn()
{
	$c=8;
	$sn='';
	for($i=0;$i<$c;++$i)
	{
		$sn.=rand()%100;
	}
	return $sn;
}

if($action=='add')//创建CDKEY
{
	$type=(int)$_POST['type'];//类型：0=只可使用一次；1=每角色可使用一次；
	$count=$_POST['count'];//创建数量
	$money=(int)$_POST['money'];
	$gold=(int)$_POST['gold'];
	$gold2=(int)$_POST['gold2'];
	$soldier=(int)$_POST['soldier'];
	$food=(int)$_POST['food'];
	$item=AKReplace(AKValue($_POST['item']));
	$heroID=(int)$_POST['heroID'];
	$wifeID=(int)$_POST['wifeID'];
	
	if($count<=0)//创建数量错误
	{
		$json=array(
			result=>-1
		);
		echo json_encode($json);
		exit();
	}
	
	if($type!=0&&$type!=1)//类型错误
	{
		$json=array(
			result=>-2
		);
		echo json_encode($json);
		exit();
	}
	
	
	$snArr=array();
	$cnn=new_db_cnn(get_db_account_info());
	
	for($i=0;$i<$count;++$i)
	{
		//创建sn
		$sn='';
		while(true)
		{
			$sn=genSn();
			$sql="select id from `cdkey` where sn='$sn' limit 1";
			$rs=$cnn->exec($sql);
			if(!($row=$cnn->getRow($rs)))
			{
				break;
			}
		}
		//创建CDKEY
		$sql="insert into `cdkey` set `type`=$type,sn='$sn',money=$money,gold=$gold,gold2=$gold2,soldier=$soldier,food=$food,item='$item',heroID=$heroID,wifeID=$wifeID,genTime='$genTime'";
		$cnn->exec($sql);
		array_push($snArr,$sn);
	}
	
	$json=array(
		result=>1,
		data=>$snArr
	);
	echo json_encode($json);
	exit();
}
else if($action=='del')//删除CDKEY
{
	$cdkeyID=(int)$_POST['cdkeyID'];
	
	$sql="delete from `cdkey` where id=$cdkeyID limit 1";
	$cnn=new_db_cnn(get_db_account_info());
	$cnn->exec($sql);
	
	$json=array(
		result=>1
	);
	echo json_encode($json);
	exit();
}
else if($action=='delBatch')//批量删除CDKEY
{
	$datetime=AKReplace(AKValue($_POST['datetime']));
	
	$sql="delete from `cdkey` where genTime='$datetime'";
	//echo $sql;
	$cnn=new_db_cnn(get_db_account_info());
	$cnn->exec($sql);
	
	$json=array(
		result=>1
	);
	echo json_encode($json);
	exit();
}
else if($action=='selectBatch')//批量查询CDKEY
{
	$datetime=AKReplace(AKValue($_POST['datetime']));
	
	$sql="select sn from `cdkey` where genTime='$datetime'";
	//echo $sql;
	$cnn=new_db_cnn(get_db_account_info());
	$rs=$cnn->exec($sql);
	$data=array();
	while($row=$cnn->getRow($rs))
	{
		array_push($data,$row['sn']);
	}
	//echo $sql;
	$json=array(
		result=>1,
		data=>$data
	);
	echo json_encode($json);
	exit();
}
else if($action=='list')//列表
{
	$pageSize=25;
	$page=(int)$_POST['page'];
	$offset=$page*$pageSize;
	
	$sn=AKReplace(AKValue($_POST['sn']));//序列号
	$type=(int)$_POST['type'];//类型：-1=全部；0=一次性；1=通用（每角色一次）；
	
	$cnn=new_db_cnn(get_db_account_info());
	$sql="select id,sn,`type`,useCount,genTime from `cdkey`";
	if($sn!=''&&$type!=-1)
	{
		$sql.=" where `sn`='$sn' and `type`=$type";
	}
	else if($sn!='')
	{
		$sql.=" where `sn`='$sn'";
	}
	else if($type!=-1)
	{
		$sql.=" where `type`=$type";
	}
	$sql.=" order by id desc limit $offset,$pageSize";
	$rs=$cnn->exec($sql);
	$data=array();
	while($row=$cnn->getRow($rs))
	{
		$info=array(
			id=>$row['id'],
			sn=>$row['sn'],
			type=>$row['type'],
			useCount=>$row['useCount'],
			genTime=>$row['genTime']
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
