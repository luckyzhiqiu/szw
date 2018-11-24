<?php
//获取用户信息（包含：渠道ID）
include_once(dirname(__FILE__).'/../../ak/AKCommon.php');
$token=$_GET['token'];
$url="http://h5.sdk.quicksdk.net/webGameApi/getUserInfo?token=$token";
echo AKSubmitForm($url);