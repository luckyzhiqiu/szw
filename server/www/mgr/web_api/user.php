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

function sendMail($cnn,$userID,$title,$body,$money,$gold,$gold2,$soldier,$food,$item)
{
	global $genTime;
	$tableName="mail$userID";
	creatTable($cnn,$tableName,'mail');
	$sql="insert into $tableName set title='$title',body='$body',money=$money,gold=$gold,gold2=$gold2,soldier=$soldier,food=$food,item='$item',genTime='$genTime'";
	//echo $sql;
	$cnn->exec($sql);
}


if($action=='sendMail')//发送邮件
{
	$gameUserID=(int)$_POST['gameUserID'];//$gameUserID为0时代表发给全部用户
	$userIDArrStr=AKReplace(AKValue($_POST['userIDArr']));//userID数组
	$title=AKReplace(AKValue($_POST['title']));
	$body=AKReplace(AKValue($_POST['body']));
	$money=(int)$_POST['money'];
	$gold=(int)$_POST['gold'];
	$gold2=(int)$_POST['gold2'];
	$soldier=(int)$_POST['soldier'];
	$food=(int)$_POST['food'];
	$item=AKReplace(AKValue($_POST['item']));
	$sendType=$_POST['sendType'];
	
	$cnn=new_db_cnn(get_db_common_info());
	
	if($sendType=='any')//发给指定多个用户
	{
		$userIDArr=explode(',',$userIDArr);
		$c=sizeof($userIDArr);
		for($i=0;$i<$c;++$i)
		{
			$curUserID=(int)$userIDArr[$i];
			sendMail($cnn,$curUserID,$title,$body,$money,$gold,$gold2,$soldier,$food,$item);
		}
		$json=array(
			result=>1
		);
		echo json_encode($json);
		exit();
	}
	else if($sendType=='all')//发给全服所有用户
	{
		$sql="select id from user";
		$rs=$cnn->exec($sql);
		while($row=$cnn->getRow($rs))
		{
			$curUserID=(int)$row['id'];
			sendMail($cnn,$curUserID,$title,$body,$money,$gold,$gold2,$soldier,$food,$item);
		}
		$json=array(
			result=>1
		);
		echo json_encode($json);
		exit();
	}
	else if($sendType=='one')//发给指定用户
	{
		sendMail($cnn,$gameUserID,$title,$body,$money,$gold,$gold2,$soldier,$food,$item);
		$json=array(
			result=>1
		);
		echo json_encode($json);
		exit();
	}
	
	$json=array(
		result=>-1
	);
	echo json_encode($json);
	exit();
}
else if($action=='limitChat')//禁言
{
	$switch=(int)$_POST['switch'];//禁言开关
	$time=(int)$_POST['time'];//禁言时间（分钟）
	$gameUserID=(int)$_POST['gameUserID'];
	
	$rd=new AKRedis($redisInfoArr);
	$userData=array();
	$rd->updateRow
	(
		get_db_common_info(),
		'user','id',$gameUserID,'*',
		//修改回调
		function(&$user,&$userData)
		{
			global $switch,$time;
			//mysqlCol("int","limitChat",0);//禁言开关：0=关闭；1=开启；
			//mysqlCol("bigint","limitChatEndTime",0);//禁言结束时间（秒）
			if($switch==0)$user['limitChat']=0;
			else $user['limitChat']=1;
			$user['limitChatEndTime']=time()+$time*60;
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
else if($action=='loadJson')//获取用户json数据
{
	$gameUserID=(int)$_POST['gameUserID'];
	
	$rd=new AKRedis($redisInfoArr);
	$userData=array();
	$rd->updateRow
	(
		get_db_common_info(),
		'user','id',$gameUserID,'*',
		//修改回调
		function(&$user,&$userData)
		{
			$json=array(
				result=>1,
				data=>$user['json']
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
else if($action=='saveJson')//保存用户json数据
{
	$gameUserID=(int)$_POST['gameUserID'];
	$jsonBase64Str=AKValue($_POST['jsonBase64Str']);
	
	$rd=new AKRedis($redisInfoArr);
	$userData=array();
	$rd->updateRow
	(
		get_db_common_info(),
		'user','id',$gameUserID,'*',
		//修改回调
		function(&$user,&$userData)
		{
			global $jsonBase64Str;
			$user['json']=$jsonBase64Str;
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
else if($action=='loadJsonExt')//获取用户jsonExt数据
{
	$gameUserID=(int)$_POST['gameUserID'];
	
	$rd=new AKRedis($redisInfoArr);
	$userData=array();
	$rd->updateRow
	(
		get_db_common_info(),
		'user','id',$gameUserID,'*',
		//修改回调
		function(&$user,&$userData)
		{
			$json=array(
				result=>1,
				data=>$user['jsonExt']
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
else if($action=='saveJsonExt')//保存用户jsonExt数据
{
	$gameUserID=(int)$_POST['gameUserID'];
	$jsonBase64Str=AKValue($_POST['jsonBase64Str']);
	
	$rd=new AKRedis($redisInfoArr);
	$userData=array();
	$rd->updateRow
	(
		get_db_common_info(),
		'user','id',$gameUserID,'*',
		//修改回调
		function(&$user,&$userData)
		{
			global $jsonBase64Str;
			$user['jsonExt']=$jsonBase64Str;
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
else if($action=='loadUser')//获取用户数据
{
	$gameUserID=(int)$_POST['gameUserID'];
	
	$rd=new AKRedis($redisInfoArr);
	$userData=array();
	$rd->updateRow
	(
		get_db_common_info(),
		'user','id',$gameUserID,'*',
		//修改回调
		function(&$user,&$userData)
		{
			$data=json_encode($user,JSON_UNESCAPED_UNICODE);
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
else if($action=='saveUser')//保存用户数据
{
	$gameUserID=(int)$_POST['gameUserID'];
	$jsonBase64Str=AKValue($_POST['jsonBase64Str']);
	
	$rd=new AKRedis($redisInfoArr);
	$userData=array();
	$rd->updateRow
	(
		get_db_common_info(),
		'user','id',$gameUserID,'*',
		//修改回调
		function(&$user,&$userData)
		{
			global $jsonBase64Str,$gameUserID;
			$userJson=json_decode($jsonBase64Str,true);
			//var_dump($userJson['id']);
			//var_dump("$gameUserID");
			if($userJson['id']!="$gameUserID")
			{
				$json=array(
					result=>-1
				);
				echo json_encode($json);
				return false;
			}
			foreach($userJson as $key=>$val)
			{
				$user[$key]=$val;
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
else if($action=='mailList')//邮件列表
{
	$gameUserID=(int)$_POST['gameUserID'];
	
	$data=array();
	$sql="select id,title,body,genTime from mail$gameUserID order by id desc";
	$cnn=new_db_cnn(get_db_common_info());
	$rs=$cnn->exec($sql);
	while($row=$cnn->getRow($rs))
	{
		$info=array(
			id=>$row['id'],
			title=>$row['title'],
			body=>$row['body'],
			genTime=>$row['genTime'],
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
else if($action=='list')//用户列表
{
	$pageSize=25;
	$page=(int)$_POST['page'];
	$offset=$page*$pageSize;
	
	$gameUserID=(int)$_POST['gameUserID'];
	$nickname=AKReplace(AKValue($_POST['nickname']));
	$type=(int)$_POST['type'];
	
	$data=array();
	
	
	$sql="select id,nickname,genTime,online,level,first_uid from user";
	if($type==2)//查全部
	{
		//分页返回
		if($nickname!=''&&$gameUserID!=0)
		{
			$sql.=" where nickname like '%$nickname%' and id=$gameUserID";
			if($type==1)$sql.=" and online=1";
			else if($type==0)$sql.=" and online=0";
			$sql.=" order by id desc limit $offset,$pageSize";
		}
		else if($nickname!='')
		{
			$sql.=" where nickname like '%$nickname%'";
			if($type==1)$sql.=" and online=1";
			else if($type==0)$sql.=" and online=0";
			$sql.=" order by id desc limit $offset,$pageSize";
		}
		else if($gameUserID!=0)
		{
			$sql.=" where id=$gameUserID";
			if($type==1)$sql.=" and online=1";
			else if($type==0)$sql.=" and online=0";
			$sql.=" order by id desc limit $offset,$pageSize";
		}
		else
		{
			if($type==1)$sql.=" where online=1";
			else if($type==0)$sql.=" where online=0";
			$sql.=" order by id desc limit $offset,$pageSize";
		}
	}
	else//查在线或不在线
	{
		if($page!=0)//只返回1次
		{
			$json=array(
				result=>1,
				data=>$data
			);
			exit();
		}
		if($nickname!=''&&$gameUserID!=0)
		{
			$sql.=" where nickname like '%$nickname%' and id=$gameUserID";
			if($type==1)$sql.=" and online=1";
			else if($type==0)$sql.=" and online=0";
			$sql.=" order by id desc";
		}
		else if($nickname!='')
		{
			$sql.=" where nickname like '%$nickname%'";
			if($type==1)$sql.=" and online=1";
			else if($type==0)$sql.=" and online=0";
			$sql.=" order by id desc";
		}
		else if($gameUserID!=0)
		{
			$sql.=" where id=$gameUserID";
			if($type==1)$sql.=" and online=1";
			else if($type==0)$sql.=" and online=0";
			$sql.=" order by id desc";
		}
		else
		{
			if($type==1)$sql.=" where online=1";
			else if($type==0)$sql.=" where online=0";
			$sql.=" order by id desc";
		}
	}
	//echo $sql;
	$cnn=new_db_cnn(get_db_common_info());
	$rs=$cnn->exec($sql);
	while($row=$cnn->getRow($rs))
	{
		$info=array(
			id=>$row['id'],
			nickname=>$row['nickname'],
			genTime=>$row['genTime'],
			online=>$row['online'],
			level=>$row['level'],
			uid=>$row['first_uid'],
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
