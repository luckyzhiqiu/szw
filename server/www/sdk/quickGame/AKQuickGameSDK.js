//quick h5网页加壳版（可以在浏览器中直接打开）
function AKQuickGameSDK(json)
{
	var that=this;
	this.json=json;
	this.userInfo=null;
	this.callback=null;
	this.ver="h5";//h5网页加壳版
	
	this.initQuick=function(info)
	{
		this.userInfo=info.data;
		var channelCode=info.data.channelId;
		QuickSDK.init(this.json.productCode,this.json.productKey,this.json.debug,channelCode);
		this.callback();
	}
	
	function getUserInfo(callback)
	{
		var url="../sdk/quickGame/getUserInfo.php?token="+get_html_url_var("token");
		//console.log(url);
		$.ajax
		({
			type:"get",
			url:url,
			success:function(data)
			{
				that.initQuick(json_decode(data));
			},
			error:function()
			{
				console.log("getUserInfo error");
			}
		});
	}
	
	this.start=function(callback)
	{
		this.callback=callback;
		getUserInfo();
	}
	
	this.login=function()
	{
		QuickSDK.login(function(callbackData){
			var message;
			if(callbackData.status)
			{
				message = 'GameDemo:QuickSDK登录成功: uid=>' + callbackData.data.uid;
			}
			else
			{
				message = 'GameDemo:QuickSDK登录失败:' + callbackData.message;
			}
			console.log(message);
		});
	}
	
	this.pay=function(orderInfo)
	{
		orderInfo["productCode"]=this.json.productCode;
		
		var orderInfoJson = JSON.stringify(orderInfo);
        QuickSDK.pay(orderInfoJson,function(payStatusObject){
            console.log('支付结果通知' + JSON.stringify(payStatusObject));
        });
	}
}
