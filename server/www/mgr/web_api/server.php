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

if($action=='create')//接入游戏服
{
	$serverName=AKReplace(AKValue($_POST['serverName']));
	$serverIP=AKReplace(AKValue($_POST['serverIP']));
	$serverPort=(int)AKReplace(AKValue($_POST['serverPort']));
	$dbIP=AKReplace(AKValue($_POST['dbIP']));
	$dbPort=(int)AKReplace(AKValue($_POST['dbPort']));
	$dbName=AKReplace(AKValue($_POST['dbName']));
	$dbUser=AKReplace(AKValue($_POST['dbUser']));
	$dbPassword=AKReplace(AKValue($_POST['dbPassword']));
	
	$cnn=new_db_cnn(get_db_global_info());
	$sql="insert into game_server set genTime='$genTime'";
	$sql.=",serverName='$serverName'";
	$sql.=",gameServerIP='$serverIP'";
	$sql.=",gameServerPort=$serverPort";
	$sql.=",dbIP='$dbIP'";
	$sql.=",dbPort=$dbPort";
	$sql.=",dbName='$dbName'";
	$sql.=",dbUser='$dbUser'";
	$sql.=",dbPassword='$dbPassword'";
	$cnn->exec($sql);
	echo '{"result":1}';
	exit();
}
else if($action=='clone')//克隆游戏服
{
	$gameServerID=(int)$_POST['gameServerID'];
	
	$cnn=new_db_cnn(get_db_global_info());
	$sql="select * from game_server where id=$gameServerID";
	$rs=$cnn->exec($sql);
	$row=$cnn->getRow($rs);
	if(!$row)
	{
		//游戏服ID不存在
		echo '{"result":-1}';
		exit();
	}
	$serverName=$row['serverName'];
	$serverIP=$row['gameServerIP'];
	$serverPort=$row['gameServerPort'];
	$dbIP=$row['dbIP'];
	$dbPort=$row['dbPort'];
	$dbName=$row['dbName'];
	$dbUser=$row['dbUser'];
	$dbPassword=$row['dbPassword'];
	
	
	$sql="insert into game_server set genTime='$genTime'";
	$sql.=",serverName='$serverName'";
	$sql.=",gameServerIP='$serverIP'";
	$sql.=",gameServerPort=$serverPort";
	$sql.=",dbIP='$dbIP'";
	$sql.=",dbPort=$dbPort";
	$sql.=",dbName='$dbName'";
	$sql.=",dbUser='$dbUser'";
	$sql.=",dbPassword='$dbPassword'";
	$cnn->exec($sql);
	echo '{"result":1}';
	exit();
}
else if($action=='list')//游戏服列表
{
	$pageSize=25;
	$page=(int)$_POST['page'];
	$offset=$page*$pageSize;
	$cnn=new_db_cnn(get_db_global_info());
	$sql="select id,serverName,status,genTime from game_server order by id desc limit $offset,$pageSize";
	$rs=$cnn->exec($sql);
	$data=array();
	while($row=$cnn->getRow($rs))
	{
		$info=array(
			id=>$row['id'],
			serverName=>$row['serverName'],
			status=>(int)$row['status'],
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
else if($action=='load')//加载游戏服数据
{
	$gameServerID=(int)$_POST['gameServerID'];
	
	$cnn=new_db_cnn(get_db_global_info());
	$sql="select serverName,gameServerIP,gameServerPort,dbIP,dbPort,dbName,dbUser,dbPassword,status from game_server where id=$gameServerID limit 1";
	$rs=$cnn->exec($sql);
	$data=array();
	if($row=$cnn->getRow($rs))
	{
		$data=array(
			serverName=>$row['serverName'],
			gameServerIP=>$row['gameServerIP'],
			gameServerPort=>$row['gameServerPort'],
			dbIP=>$row['dbIP'],
			dbPort=>$row['dbPort'],
			dbName=>$row['dbName'],
			dbUser=>$row['dbUser'],
			dbPassword=>$row['dbPassword'],
			status=>$row['status']
		);
	}
	$json=array(
		result=>1,
		data=>$data
	);
	echo json_encode($json);
	exit();
}
else if($action=='save')//保存游戏服
{
	$serverID=(int)$_POST['serverID'];
	$status=(int)$_POST['status'];
	$serverName=AKReplace(AKValue($_POST['serverName']));
	$serverIP=AKReplace(AKValue($_POST['serverIP']));
	$serverPort=(int)AKReplace(AKValue($_POST['serverPort']));
	$dbIP=AKReplace(AKValue($_POST['dbIP']));
	$dbPort=(int)AKReplace(AKValue($_POST['dbPort']));
	$dbName=AKReplace(AKValue($_POST['dbName']));
	$dbUser=AKReplace(AKValue($_POST['dbUser']));
	$dbPassword=AKReplace(AKValue($_POST['dbPassword']));
	
	$cnn=new_db_cnn(get_db_global_info());
	$sql="update game_server set ";
	$sql.="serverName='$serverName'";
	$sql.=",gameServerIP='$serverIP'";
	$sql.=",gameServerPort=$serverPort";
	$sql.=",dbIP='$dbIP'";
	$sql.=",dbPort=$dbPort";
	$sql.=",dbName='$dbName'";
	$sql.=",dbUser='$dbUser'";
	$sql.=",dbPassword='$dbPassword'";
	$sql.=",status=$status";
	$sql.=" where id=$serverID limit 1;";
	//echo $sql;
	$cnn->exec($sql);
	echo '{"result":1}';
	exit();
}
else if($action=='gameServerList')//获取游戏服列表信息
{
	$data=array();
	$cnn=new_db_cnn(get_db_global_info());
	$sql="select id,serverName,gameServerIP from game_server";
	$rs=$cnn->exec($sql);
	while($row=$cnn->getRow($rs))
	{
		$serverID=(int)$row['id'];
		$serverName=$row['serverName'];
		$gameServerIP=$row['gameServerIP'];
		array_push
		(
			$data,
			array
			(
				'serverID'=>$serverID,
				'serverName'=>$serverName,
				'gameServerIP'=>$gameServerIP
			)
		);
	}
	$rt=array
	(
		result=>1,
		data=>$data
	);
	echo json_encode($rt);
	exit();
}
else if($action=='createCache')//更新游戏服列表缓存
{
	function getStatusStr($status)
	{
		switch($status)
		{
			case 0:
				return '待开';
			case 1:
				return '新服';
			case 2:
				return '火爆';
			case 3:
				return '维护';
			default:
				return '未知';
		}
	}
	//[1,"宇宙无敌","火爆","192.168.35.100",10050]
	$data=array();
	$cnn=new_db_cnn(get_db_global_info());
	$sql="select id,serverName,status,gameServerIP,gameServerPort from game_server where status>=1 and status<=3";
	$rs=$cnn->exec($sql);
	while($row=$cnn->getRow($rs))
	{
		$serverID=(int)$row['id'];
		$serverName=$row['serverName'];
		$status=(int)$row['status'];
		$gameServerIP=$row['gameServerIP'];
		$gameServerPort=$row['gameServerPort'];
		array_push
		(
			$data,
			array
			(
				$serverID,
				$serverName,
				getStatusStr($status),
				$gameServerIP,
				$gameServerPort
			)
		);
	}
	//写入文件
	file_put_contents('../../cache/serverList.json',json_encode($data,JSON_UNESCAPED_UNICODE));
	//返回
	echo '{"result":1}';
	exit();
}
else if($action=='start')//开服
{
	$bt=$_POST['bt'];//开服日期时间，格式：2017-10-31 10:20:30
	$serverID=(int)$_POST['serverID'];
	
	$cnn=new_db_cnn(get_db_global_info());
	$sql="select gameServerIP from game_server where id=$serverID";
	$rs=$cnn->exec($sql);
	$row=$cnn->getRow($rs);
	if(!$row)
	{
		//游戏服ID不存在
		echo '{"result":-1}';
		exit();
	}
	$serverIP=$row['gameServerIP'];
	
	//填写开服时间
	$sql="update game_server set openTime='$genTime' where id=$serverID";
	$cnn->exec($sql);
	
	$rd=new AKRedis($redisInfoArr);
	$userData=array
	(
		bt=>$bt,
		serverID=>$serverID,
		serverIP=>$serverIP,
	);
	$rd->updateRow
	(
		get_db_common_info(),
		'global_var','name','gameServer','*',
		//修改回调
		function(&$var,&$userData)
		{
			$varJson=json_decode($var['json'],true);
			$varJson['bt']=$userData['bt'];
			$varJson['switch']=1;
			$varJson['isResetStage']=1;
			$varJson['serverID']=$userData['serverID'];
			$varJson['serverIP']=$userData['serverIP'];
			//保存
			$var['json']=json_encode($varJson);
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
	
	/*$rd->updateRow
	(
		get_db_common_info(),
		'global_var','name','rushLoop','*',
		//修改回调
		function(&$var,&$userData)
		{
			$varJson=json_decode($var['json'],true);
			$varJson['stage']=0;
			//保存
			$var['json']=json_encode($varJson);
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
	);*/

	$json=array(
			result=>1
		);
	echo json_encode($json);
	exit();
}
else if($action=='sendStartServer')//发送开服通知
{
	$serverID=(int)$_POST['serverID'];
	$cnn=new_db_cnn(get_db_global_info());
	//发送开服通知
	$data=json_decode(openInform($cnn,$serverID),true);
	$status=$data["status"];
	if($status)
	{
		$json=array(
			result=>1
		);
	}
	else
	{
		$json=array(
			result=>-2
		);
	}
	echo json_encode($json);
	exit();
}