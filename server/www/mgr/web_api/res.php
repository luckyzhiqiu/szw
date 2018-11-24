<?php
include_once(dirname(__FILE__)."/common.php");
$userID=AKValue($_POST['userID']);//用户ID
$tick=AKValue($_POST['tick']);//通行证
//校验通行证
mgrCheckClientTick($userID,$tick);
$action=$_POST['action'];
$genTime=date('Y-m-d H:i:s',time());


if($action=='list')//用户列表
{
	$pageSize=25;
	$page=(int)$_POST['page'];
	$offset=$page*$pageSize;
	
	$gameUserID=(int)$_POST['gameUserID'];
	$date=AKReplace(AKValue($_POST['date']));
	$type1=(int)$_POST['type1'];
	$type2=(int)$_POST['type2'];
	
	
	$cnn=new_db_cnn(get_db_common_info());
	$tableName='res_flow';
	creatTable($cnn,"$tableName$date",$tableName);
	$sql="select userID,`type`,io,count,val,genTime from $tableName$date where `type`>=$type1 and `type`<=$type2";
	if($gameUserID>0)
	{
		$sql.=" and userID=$gameUserID";
	}
	$sql.=" order by id limit $offset,$pageSize";
	$data=array();
	//echo $sql;
	$cnn=new_db_cnn(get_db_common_info());
	$rs=$cnn->exec($sql);
	while($row=$cnn->getRow($rs))
	{
		$info=array(
			userID=>$row['userID'],
			type=>$row['type'],
			io=>$row['io'],
			count=>$row['count'],
			val=>$row['val'],
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
