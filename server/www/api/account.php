<?php
header('Access-Control-Allow-Origin:*');
include_once(dirname(__FILE__)."/../config/mgr/config.php");
$cnn=new_db_cnn(get_db_account_info());//获取帐号信息数据库信息
$rd=new AKRedis($redisInfoArr_account);
$userData=array();
$genTime=date('Y-m-d H:i:s',time());

//获取帐号相关信息
$uid=AKReplace(AKValue($_POST["uid"]));//平台uid
$platformID=(int)$_POST["platformID"];//平台ID

/*if(!$uid || !$platformID)
{
	exit();
}*/

$action=$_POST['action'];
if($action=='getInfo')
{
	$ostype=AKReplace(AKValue($_POST['ostype']));//账号操作系统
	$platform=AKReplace(AKValue($_POST['platform']));//帐号所在第三方平台标识
	$firstServerID=(int)$_POST['firstServerID'];//首推游戏服ID（与首次创角服ID无关）
	
	$ip=$_SERVER["REMOTE_ADDR"];//账号注册IP
	
	$row=$rd->updateRow
	(
		get_db_account_info(),
		'account','uid',$uid,'*',
		//修改回调
		function(&$row,&$userData)
		{
			global $genTime;
			$idCard=$row['idCard'];//身份证
			$serverIDMap=$row['serverIDMap'];//创建过角色的游戏服ID数组，格式：[1,3,5]
			$lastServerID=$row['lastServerID'];//最后一次登录的游戏服ID
			$result=array
			(
				idCard=>$idCard,
				serverIDMap=>$serverIDMap,
				lastServerID=>$lastServerID,
				genTime=>$row['genTime'],
				ip=>$row['ip'],
				accountID=>$row['id'],
				type=>$row['type'],
				ban=>$row['ban'],
			);
			echo json_encode($result);
			
			//修改
			$row['lastLoginTime']=$genTime;
			
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
	if($row)//存在
	{
		exit();
	}
	else//不存在
	{
		//创建帐号
		$sql="insert into account set genTime='$genTime',lastLoginTime='$genTime',uid='".$uid."',platformID='".$platformID."',ostype='".$ostype."',platform='".$platform."',firstServerID=$firstServerID,ip='".$ip."',serverIDMap='{}'";
		$cnn->exec($sql);
		$accountID=$cnn->last_insert_id();
		echo '{"idCard":"","serverIDMap":{},"lastServerID":0,"genTime":"'.$genTime.'","ip":"'.$ip.'","accountID":'.$accountID.',"type":0,"ban":0}';
		exit();
	}
}
else if($action=='saveServerID')//记录登录信息
{
	$serverID=$_POST['serverID'];//登录的游戏服ID
	$row=$rd->updateRow
	(
		get_db_account_info(),
		'account','uid',$uid,'*',
		//修改回调
		function(&$account,&$userData)
		{
			global $serverID;
			$account["lastServerID"]=$serverID;
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
	
	if($row)//存在
	{
		$result=array
		(
			result=>1
		);
		echo json_encode($result);
		exit();
	}
	else//不存在
	{
		$sql="insert into account set genTime='$genTime',uid='".$uid."',platformID='".$platformID."',lastServerID='".$lastServerID."'";
		$cnn->exec($sql);
		$result=array
		(
			result=>1
		);
		echo json_encode($result);
		exit();
	}
}
else if($action=='setIdentityCard')//记录防沉迷信息
{
	$idCard=$_POST['idCard'];//身份证
	$name=$_POST['name'];//身份证姓名
	$row=$rd->updateRow
	(
		get_db_account_info(),
		'account','uid',$uid,'*',
		//修改回调
		function(&$account,&$userData)
		{
			global $idCard,$name;
			$account["idCard"]=$idCard;
			$account["name"]=$name;

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
	
	if($row)//存在
	{
		$result=array
		(
			result=>1
		);
		echo json_encode($result);
		exit();
	}
	else
	{
		$serverIDMap='{}';
		$sql="insert into account set genTime='$genTime',uid='".$uid."',platformID='".$platformID."',idCard='".$idCard."',name='".$name."',serverIDMap='".$serverIDMap."'";
		$cnn->exec($sql);
		$result=array
		(
			result=>1
		);
		echo json_encode($result);
		exit();
	}
}
else if($action=='batchSaveAccountInfo')//批量保存帐号信息（没有则创建，有则修改）
{
	$json=json_decode(base64_decode($_POST['json']),true);
	//file_put_contents('1.txt',base64_decode($_POST['json']));
	
	//var_dump($json);
	$count=count($json);
	for($i=0;$i<$count;$i++)
	{
		$map=$json[$i];
		$uid=$map["uid"];
		$platformID=$map["platformID"];
		$serverID=$map["serverID"];
		$nickname=$map["nickname"];
		$row=$rd->updateRow
		(
			get_db_account_info(),
			'account','uid',$uid,'*',
			//修改回调
			function(&$account,&$userData)
			{
				global $serverID,$nickname;
				$serverIDMap=json_decode($account["serverIDMap"],true);
				$serverIDMap[$serverID]=$nickname;
				$account["lastServerID"]=$serverID;
				$account["hasRole"]=1;
				//保存
				$account["serverIDMap"]=json_encode($serverIDMap,JSON_UNESCAPED_UNICODE);
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
		if(empty($row))
		{
			$serverIDMap=array($serverID=>$nickname);
			$val=json_encode($serverIDMap);
			$val=str_replace("\"","\\\"",$val);
			
			$sql="insert into account set ";
			$sql.="genTime='".$genTime."'";
			$sql.=",uid='".$uid."'";
			$sql.=",platformID='".$platformID."'";
			$sql.=",lastServerID='".$serverID."'";
			$sql.=",serverIDMap=\"".$val."\"";
			//file_put_contents('sql.txt',$sql);
			$cnn->exec($sql);
		}
	}
	
	echo '{"result":1}';
}
