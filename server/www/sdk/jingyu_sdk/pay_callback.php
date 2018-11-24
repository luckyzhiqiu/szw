<?php
header('Access-Control-Allow-Origin:*');
include_once(dirname(__FILE__)."/../../config/mgr/config.php");
include_once(dirname(__FILE__)."/../../config/tinyWeb/config.php");

$urlRoot="$tinyWebRoot/notifyPay";
//echo $urlRoot;

// 本项目统一签名方法 PHP语言列示
function make_sing($params,$md5_key='qusSTGFiBaPiKaqiu2546843')
{
	unset($params['sign']);
	unset($params['extras_params']);
	// 按键名a-z升序
	ksort($params); 
	reset($params);
	$sign = '';
	foreach($params as $k => $v){
		if($v != null){
			$sign .=  $k . '=' . $v;
		}
	}
	return md5($sign.$md5_key);
}

$params=$_GET;



// //调试时打开
// $jsonStr=file_get_contents('sample.json');
// $params=json_decode($jsonStr,true);




$rtJson=json_encode($params);

//记录回调日志（调试时打开）
$curDate=date('Ymd',time());
//file_put_contents($curDate.'.txt',$rtJson);
error_log($rtJson."\r\n\r\n",3,"log/$curDate.txt");

//校验签名
$sign=make_sing($params);
if($params['sign']!=$sign)//签名错误
{
	echo 'sign error';
	exit();
}


// $uid=$params['uid'];//用户ID
// $channel=$params['channel'];//渠道ID
$game_order=$params['game_order'];//CP方订单
$order_no=$params['order_no'];//SDK方订单
$pay_time=$params['pay_time'];//支付完成时间
// $amount=$params['amount'];//订单金额
$status=$params['status'];//订单状态
// $pf=$params['pf'];//渠道名称
// $sign=$params['sign'];//签名*

$cnn=new_db_cnn(get_db_common_info());//获取帐号信息数据库信息
$cnn->exec("lock table `order` write");

//检查订单状态
$sql="select id,userID,`type`,accountID,uid from `order` where status=0 and num='$game_order' limit 1";
$rs=$cnn->exec($sql);
$row=$cnn->getRow($rs);//没有需要支付的订单
if(!$row)
{
	//echo 'no order';
	echo 'SUCCESS';
	exit();
}
$orderID=$row['id'];
$userID=$row['userID'];
$type=$row['type'];
$uid=$row['uid'];

$payTime=date('Y-m-d H:i:s',$pay_time);
$modifTime=date('Y-m-d H:i:s',time());

//修改订单状态
$sql="update `order` set status=1";
$sql.=",order_no='$order_no'";
$sql.=",rtJson='$rtJson'";
$sql.=",payTime='$payTime'";
$sql.=",modifTime='$modifTime'";
$sql.="where id=$orderID";
//echo $sql;
$cnn->exec($sql);

//修改account表支付信息
$rd=new AKRedis($redisInfoArr_account);
$row=$rd->updateRow
(
	get_db_account_info(),
	'account','uid',$uid,'*',
	//修改回调
	function(&$row,&$userData)
	{
		global $payTime;
		$firstPay=(int)$row['firstPay'];
		if($firstPay==1)//首次支付
		{
			$row['firstPayTime']=$payTime;
			$row['firstPay']=0;
		}
		$row['endPayTime']=$payTime;
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

AKSubmitForm($urlRoot."?game_order=$game_order&userID=$userID&type=$type");

echo 'SUCCESS';