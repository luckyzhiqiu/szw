function onNetError()
{
	alert("网络通信异常");
}
function onLoginError()
{
	alert("登录超时或失败");
	window.location.reload();
}
function showLoading(isShow)
{
	if(isShow==null)isShow=true;
	if(isShow)
	{
		$("#myShowLoading").modal('open');
	}
	else
	{
		$("#myShowLoading").modal('close');
	}
}
//发起json请求
function reqJson(type,url,data,callback)
{
	showLoading(true);
	$.ajax
	({
		type:type,
		url:url,
		data:data,
		success:function(data)
		{
			showLoading(false);
			try
			{
				var json=json_decode(data);
			}
			catch(e)
			{
				console.log(data);
			}
			if(json.result==0)//登录超时
			{
				onLoginError();
			}
			else
			{
				callback(json);
			}
		},
		error:function()
		{
			showLoading(false);
			onNetError();
		}
	});
}
//提示
function myAlert(str)
{
	$("#myAlertUI_txt").html(str);
	$("#myAlertUI").modal('open');
}
//获取游戏服信息列表
function getServerList(callback)
{
	reqJson
	(
		"post",
		"web_api/server.php",
		{
			action:'gameServerList',
			userID:cache.get('userID'),
			tick:cache.get('tick'),
		},
		function(json)
		{
			if(json.result==1)//成功
			{
				//console.log(json);
				$("#serverID option").remove();
				var arr=json.data;
				for(var i=0;i<arr.length;++i)
				{
					var row=arr[i];
					$("#serverID").append("<option value='"+row.serverID+"'>"+row.serverName+"</option>");
				}
				serverList=json.data;
				callback();
			}
		}
	);
}
//获取游戏服信息
function getServerInfo(serverID)
{
	for(var i=0;i<serverList.length;++i)
	{
		var info=serverList[i];
		if(info.serverID==serverID)
		{
			return info;
		}
	}
	return null;
}
//获取当前游戏服ID
function getCurServerID()
{
	var serverID=parseInt($("#serverID").val());
	return serverID;
}
//获取当前游戏服信息
function getCurServerInfo()
{
	return getServerInfo(getCurServerID());
}
//url重定向到指定游戏服
function redirect(serverID,url)
{
	var url="http://"+getServerInfo(serverID).gameServerIP+"/mgr/"+url;
	return url;
}
//url重启向到当前游戏服
function redirectCurServer(url)
{
	return redirect(getCurServerID(),url);
}
//时间格式化，例如：new Date().format("yyyy-MM-dd hh:mm:ss");
Date.prototype.format = function(fmt) { 
     var o = { 
        "M+" : this.getMonth()+1,                 //月份 
        "d+" : this.getDate(),                    //日 
        "h+" : this.getHours(),                   //小时 
        "m+" : this.getMinutes(),                 //分 
        "s+" : this.getSeconds(),                 //秒 
        "q+" : Math.floor((this.getMonth()+3)/3), //季度 
        "S"  : this.getMilliseconds()             //毫秒 
    }; 
    if(/(y+)/.test(fmt)) {
            fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length)); 
    }
     for(var k in o) {
        if(new RegExp("("+ k +")").test(fmt)){
             fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
         }
     }
    return fmt; 
}