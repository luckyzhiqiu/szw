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

if($action=='loadUnion')//获取联盟数据
{
	$unionID=(int)$_POST['unionID'];
	
	$rd=new AKRedis($redisInfoArr);
	$userData=array();
	$rd->updateRow
	(
		get_db_common_info(),
		'union','id',$unionID,'*',
		//修改回调
		function(&$union,&$userData)
		{
			$data=json_encode($union,JSON_UNESCAPED_UNICODE);
			$json=array(
				result=>1,
				data=>$data
			);
			echo json_encode($json,JSON_UNESCAPED_UNICODE);
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
else if($action=='saveUnion')//保存联盟数据
{
	$unionID=(int)$_POST['unionID'];
	$jsonBase64Str=AKValue($_POST['jsonBase64Str']);
	
	$rd=new AKRedis($redisInfoArr);
	$userData=array();
	$rd->updateRow
	(
		get_db_common_info(),
		'union','id',$unionID,'*',
		//修改回调
		function(&$union,&$userData)
		{
			global $jsonBase64Str,$unionID;
			$unionJson=json_decode($jsonBase64Str,true);
			if($unionJson['id']!="$unionID")
			{
				$json=array(
					result=>-1
				);
				echo json_encode($json);
				return false;
			}
			foreach($unionJson as $key=>$val)
			{
				$union[$key]=$val;
			}
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
else if($action=='loadJson')//获取联盟json数据
{
	$unionID=(int)$_POST['unionID'];
	
	$rd=new AKRedis($redisInfoArr);
	$userData=array();
	$rd->updateRow
	(
		get_db_common_info(),
		'union','id',$unionID,'*',
		//修改回调
		function(&$union,&$userData)
		{
			$json=array(
				result=>1,
				data=>$union['json']
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
else if($action=='saveJson')//保存联盟json数据
{
	$unionID=(int)$_POST['unionID'];
	$jsonBase64Str=AKValue($_POST['jsonBase64Str']);
	
	$rd=new AKRedis($redisInfoArr);
	$userData=array();
	$rd->updateRow
	(
		get_db_common_info(),
		'union','id',$unionID,'*',
		//修改回调
		function(&$union,&$userData)
		{
			global $jsonBase64Str;
			$union['json']=$jsonBase64Str;
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
else if($action=='list')//列表
{
	$pageSize=25;
	$page=(int)$_POST['page'];
	$offset=$page*$pageSize;
	
	$unionID=(int)$_POST['unionID'];
	$name=AKReplace(AKValue($_POST['name']));
	
	$cnn=new_db_cnn(get_db_common_info());
	
	$sql="select id,name,genTime from `union`";
	if($name!=''&&$unionID!=0)
	{
		$sql.=" where `name` like '%$name%' and id=$unionID";
	}
	else if($name!='')
	{
		$sql.=" where `name` like '%$name%'";
	}
	else if($unionID!=0)
	{
		$sql.=" where id=$unionID";
	}
	$sql.=" order by id desc limit $offset,$pageSize";
	$rs=$cnn->exec($sql);
	$data=array();
	while($row=$cnn->getRow($rs))
	{
		$info=array(
			id=>$row['id'],
			name=>$row['name'],
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
