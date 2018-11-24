<?php
header('Access-Control-Allow-Origin:*');
include_once(dirname(__FILE__)."/../../config/mgr/config.php");
include_once(dirname(__FILE__)."/../../config/tinyWeb/config.php");
include_once(dirname(__FILE__)."/quicksdkAsy.php");

$urlRoot="$tinyWebRoot/notifyPay";
//echo $urlRoot;

//////////////////////////////////////////////////////////////////////////////
//quick后台参数
$Callback_Key="48772214732686863041809386966101";
$Md5_Key="yrxd97dsvsupzn3sss0lwugkbe1hn6rl";
//////////////////////////////////////////////////////////////////////////////

$params=$_POST;
/*$params=json_decode('{"nt_data":"@115@118@171@159@165@81@173@152@168@166@159@162@165@110@84@102@97@102@84@88@156@158@154@165@153@158@165@151@113@87@135@133@125@100@107@84@89@164@171@148@164@151@151@159@166@159@151@114@85@164@161@90@118@110@115@167@170@158@154@155@167@153@157@144@164@156@166@165@154@152@156@113@114@160@155@166@170@146@153@154@113@114@155@171@150@164@156@169@169@115@103@108@99@158@165@144@171@156@166@166@119@109@154@155@151@161@164@152@163@111@101@101@100@114@97@155@159@145@165@164@154@161@117@108@151@157@147@159@165@156@159@145@174@154@155@113@108@108@108@104@103@101@98@109@111@101@149@160@152@158@165@155@161@148@172@153@152@115@110@148@159@152@161@160@158@157@150@162@168@151@155@165@117@97@100@103@99@103@106@104@110@96@104@103@104@106@103@97@102@103@106@104@106@112@102@110@104@148@159@148@164@161@155@159@150@160@164@153@152@168@112@116@158@145@164@155@148@164@169@148@153@167@112@149@107@154@99@150@155@100@109@149@107@151@107@103@107@147@103@151@151@151@151@156@110@100@112@111@155@104@110@100@102@102@150@109@102@158@148@159@158@144@166@165@154@152@168@113@115@160@164@153@152@168@145@166@166@110@106@102@102@103@103@97@108@101@105@97@104@104@102@103@105@97@112@101@111@100@109@104@112@99@102@103@111@101@161@170@155@149@169@149@163@164@117@108@164@150@171@144@171@160@160@151@119@99@103@100@110@96@102@106@100@97@99@85@100@105@108@109@103@106@105@111@113@100@167@145@173@148@166@154@164@156@113@110@154@158@166@168@164@167@116@99@101@97@99@113@98@151@159@167@172@158@171@116@113@168@171@145@168@170@165@111@103@115@98@165@173@146@171@168@169@113@114@152@175@165@164@150@166@149@162@153@169@145@164@169@115@113@102@149@172@169@164@146@170@150@163@147@171@146@164@166@116@111@101@160@156@164@165@150@154@155@112@116@102@161@172@159@152@160@170@148@159@148@159@150@170@170@148@153@158@111","sign":"@106@152@100@101@157@104@110@100@110@103@151@106@104@103@103@105@103@105@102@153@156@149@105@110@151@110@152@105@154@155@106@103","md5Sign":"c5bcbd369e7e48bc4681735769ca995e"}',true);*/

$sdk=new quickAsy();

//校验签名
if($sdk->getSign($params,$Md5_Key)!=$params['md5Sign'])
{
	echo 'md5Sign error';
	exit();
}

$curTime=date('Y-m-d H:i:s',time());


$xml=simplexml_load_string($sdk->decode($params['nt_data'],$Callback_Key));
$data=json_decode(json_encode($xml),true);
$params=$data['message'];
//echo json_encode($data);

 
$rtJson=json_encode($params);
 //记录回调日志（调试时打开）
$curDate=date('Ymd',time());
//file_put_contents($curDate.'.txt',$rtJson);
error_log($curTime."\r\n".$rtJson."\r\n\r\n",3,"log/$curDate.txt");











$game_order=$params['game_order'];//CP方订单
$order_no=$params['order_no'];//SDK方订单
$pay_time=$params['pay_time'];//支付完成时间
$rmb=((double)$params['amount'])*100;//订单金额（单位：分）
$status=$params['status'];//订单状态
// $pf=$params['pf'];//渠道名称
// $sign=$params['sign'];//签名*

if($status!="0")//状态不为0表示失败
{
	exit();
}

$cnn=new_db_cnn(get_db_common_info());//获取帐号信息数据库信息
$cnn->exec("lock table `order` write");

//检查订单状态
$sql="select id,userID,`type`,accountID,uid from `order` where status=0 and num='$game_order' and rmb='$rmb' limit 1";
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