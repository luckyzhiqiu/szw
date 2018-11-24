//quick h5转渠道版（最终会调起原生渠道登录，不能在浏览器中直接打开）
function AKQuickGameSDK(json)
{
	var that=this;
	this.json=json;
	this.userInfo=null;
	this.callback=null;
	this.ver="h52client";//h5转渠道版
	
	this.initQuick=function()
	{
		QuickSDK.init(this.json.productCode,this.json.productKey,this.json.debug);
		this.callback();
	}
	
	this.regLogoutCallback=function(callback)
	{
		QuickSDK.setLogoutNotification(callback);
	}
	
	this.start=function(callback)
	{
		this.callback=callback;
		this.initQuick();
	}
	
	this.login=function(callback)
	{
		QuickSDK.login(function(callbackData){
			var message;
			if(callbackData.status)
			{
				message = 'GameDemo:QuickSDK登录成功: uid=>' + callbackData.data.uid;
				that.userInfo=callbackData.data;
				callback();
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
	
	this.uploadGameRoleInfo=function(roleInfo)
	{
		var roleInfoJson = JSON.stringify(roleInfo);
		QuickSDK.uploadGameRoleInfo(roleInfoJson,function(response){
			if(response.status){
				console.log('uploadGameRoleInfo succ');
			}else{
				console.log(response.message);
			}
		});
	}
}
