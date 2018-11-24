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

if($action=='add')//添加公告
{
	$title=AKReplace(AKValue($_POST['title']));//标题
	$body=AKReplace(AKValue($_POST['body']));//正文
	$cnn=new_db_cnn(get_db_common_info());
	
	$sql="select count(id) from public_msg";
	$rs=$cnn->exec($sql);
	$row=$cnn->getRow($rs);
	$count=(int)$row[0];
	if($count>=10)//公告数不能超过10条
	{
		$json=array(
			result=>-1
		);
		echo json_encode($json);
		exit();
	}
	
	$sql="insert into public_msg set title='$title',body='$body',genTime='$genTime'";
	$cnn->exec($sql);
	$id=$cnn->last_insert_id();
	$json=array(
		result=>1,
		id=>$id
	);
	echo json_encode($json);
	exit();
}
else if($action=='get')//获取公告
{
	$publicMsgID=(int)$_POST['publicMsgID'];//公告ID
	$cnn=new_db_cnn(get_db_common_info());
	
	$sql="select title,body from public_msg where id=$publicMsgID limit 1";
	//echo $sql;
	$rs=$cnn->exec($sql);
	if($row=$cnn->getRow($rs))
	{
		$json=array(
			result=>1,
			title=>$row['title'],
			body=>$row['body']
		);
		echo json_encode($json);
		exit();
	}
	else//公告不存在
	{
		$json=array(
			result=>-1
		);
		echo json_encode($json);
		exit();
	}
}
else if($action=='save')//保存公告
{
	$publicMsgID=(int)$_POST['publicMsgID'];//公告ID
	$title=AKReplace(AKValue($_POST['title']));//标题
	$body=AKReplace(AKValue($_POST['body']));//正文
	
	$cnn=new_db_cnn(get_db_common_info());
	$sql="update public_msg set title='$title',body='$body' where id=$publicMsgID";
	$cnn->exec($sql);
	$json=array(
		result=>1
	);
	echo json_encode($json);
	exit();
}
else if($action=='del')//删除公告
{
	$publicMsgID=(int)$_POST['publicMsgID'];//公告ID
	$cnn=new_db_cnn(get_db_common_info());
	
	$sql="delete from public_msg where id=$publicMsgID limit 1";
	//echo $sql;
	$cnn->exec($sql);
	$json=array(
		result=>1
	);
	echo json_encode($json);
	exit();
}
else if($action=='getGlobal')//获取全局公告
{
	$publicMsgJson=json_decode((file_get_contents('../../cache/publicMsg.json')),true);
	$json=array(
		result=>1,
		body=>$publicMsgJson['body']
	);
	echo json_encode($json,JSON_UNESCAPED_UNICODE);
	exit();
}
else if($action=='saveGlobal')//保存全局公告
{
	$body=$_POST['body'];//正文
	$publicMsgJson=array(
		body=>$body
	);
	file_put_contents('../../cache/publicMsg.json',json_encode($publicMsgJson,JSON_UNESCAPED_UNICODE));
	$json=array(
		result=>1
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
	$sql="select id,title,genTime from public_msg order by id desc limit $offset,$pageSize";
	//echo $sql;
	$cnn=new_db_cnn(get_db_common_info());
	$rs=$cnn->exec($sql);
	while($row=$cnn->getRow($rs))
	{
		//userID,`status`,`get`,`num`,order_no,genTime
		$info=array(
			id=>$row['id'],
			title=>$row['title'],
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