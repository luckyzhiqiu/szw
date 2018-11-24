uiMgr.addUI
(
	"serverUI",
	function()//初始化
	{
		serverUI=new ServerUI();
		$("#serverUI_createBtn").click(function(){
			$("#createServerUI").modal("open");
		});
		$("#createServerUI_createBtn").click(function(){
			serverUI.createServer();
		});
		$("#editServerUI_saveBtn").click(function(){
			serverUI.saveServer();
		});
		$("#serverUI_createCacheBtn").click(function(){
			serverUI.createCache();
		});
		$("#startServerUI_yesBtn").click(function(){
			serverUI.startGameServer();
		});
		$("#serverUI_serverListView").scroll(function(){
			var pos=$(this).height()+this.scrollTop;
			var maxPos=this.scrollHeight;
			if(pos>=(maxPos-50))
			{
				serverUI.reqPage();
			}
		});
		$("#serverUI_cdnBtn").click(function(){
			myAlert("暂未接入CDN API，请手动刷新");
		});
	},
	function()//显示
	{
		//刷新用户列表
		serverUI.resetPage();
		serverUI.reqPage();
	}
);

ServerUI=function()
{
	var that=this;
	var page=0;
	var isPageEnd=false;
	var isReqPage=false;
	this.gameServerID=0;
	//请求页
	this.resetPage=function()
	{
		page=0;
		isPageEnd=false;
	}
	this.statusStr=function(status)
	{
		switch(status)
		{
			case 0:
				return "待开";
			case 1:
				return "新服";
			case 2:
				return "火爆";
			case 3:
				return "维护";
			default:
				return "未知";
		}
	}
	//刷新游戏服列表
	this.reqPage=function()
	{
		if(isReqPage)return;
		if(isPageEnd)return;
		isReqPage=true;
		reqJson
		(
			"post",
			"web_api/server.php",
			{
				action:'list',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				page:page,
			},
			function(json)
			{
				isReqPage=false;
				if(json.result==1)
				{
					if(json.data.length>0)
					{
						if(page==0)
						{
							$("#serverUI_serverListView").html('<table></table>');
							$("#serverUI_serverListView table").append("<tr><td>ID</td><td>名称</td><td>状态</td><td>创建日期</td><td>操作</td></tr>");
							$("#serverUI_serverListView")[0].scrollTop=0;
						}
						++page;
						var list=json.data;
						for(var i=0;i<list.length;++i)
						{
							var info=list[i];
							var str='<tr>';
							str+='<td>'+info.id+'</td>';
							str+='<td>'+info.serverName+'</td>';
							str+='<td>'+that.statusStr(info.status)+'</td>';
							str+='<td>'+info.genTime+'</td>';
							str+='<td>';
							str+="<button onclick='serverUI.loadGameServer("+info.id+");'>编辑</button>";
							str+="<button onclick='serverUI.openStartGameServerUI("+info.id+");'>开服</button>";
							str+="<button onclick='serverUI.sendStartGame("+info.id+");'>发送开服通知</button>";
							str+="<button onclick='serverUI.cloneServer("+info.id+");'>克隆</button>";
							str+='</td>';
							str+='</tr>';
							$("#serverUI_serverListView table").append(str);
						}
					}
					else
					{
						if(page==0)
						{
							$("#userUI_userListView").html('无用户数据');
						}
						isPageEnd=true;
					}
					//console.log(json);
				}
			}
		);
	}
	//克隆游戏服
	this.cloneServer=function(gameServerID)
	{
		reqJson
		(
			"post",
			"web_api/server.php",
			{
				action:'clone',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				gameServerID:gameServerID,
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					myAlert("克隆成功");
					that.resetPage();
					that.reqPage();
					getServerList(function(){
						myAlert("刷新游戏服列表成功");
					});
				}
			}
		);
	}
	//增加游戏服
	this.createServer=function()
	{
		var serverName=$("#createServerUI_serverName").val();
		var serverIP=$("#createServerUI_serverIP").val();
		var serverPort=$("#createServerUI_serverPort").val();
		var dbIP=$("#createServerUI_dbIP").val();
		var dbPort=$("#createServerUI_dbPort").val();
		var dbName=$("#createServerUI_dbName").val();
		var dbUser=$("#createServerUI_dbUser").val();
		var dbPassword=$("#createServerUI_dbPassword").val();
		reqJson
		(
			"post",
			"web_api/server.php",
			{
				action:'create',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				serverName:serverName,
				serverIP:serverIP,
				serverPort:serverPort,
				dbName:dbName,
				dbIP:dbIP,
				dbPort:dbPort,
				dbUser:dbUser,
				dbPassword:dbPassword,
			},
			function(json)
			{
				if(json.result==1)//创建成功
				{
					myAlert("创建成功");
					that.resetPage();
					that.reqPage();
					$("#createServerUI").modal("close");
					getServerList(function(){
						myAlert("刷新游戏服列表成功");
					});
				}
				else//失败
				{
					myAlert("创建失败");
				}
			}
		);
	}
	//保存游戏服
	this.saveServer=function()
	{
		var serverName=$("#editServerUI_serverName").val();
		var serverIP=$("#editServerUI_serverIP").val();
		var serverPort=$("#editServerUI_serverPort").val();
		var dbIP=$("#editServerUI_dbIP").val();
		var dbPort=$("#editServerUI_dbPort").val();
		var dbName=$("#editServerUI_dbName").val();
		var dbUser=$("#editServerUI_dbUser").val();
		var dbPassword=$("#editServerUI_dbPassword").val();
		var status=$("#editServerUI_status").val();
		reqJson
		(
			"post",
			"web_api/server.php",
			{
				action:'save',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				serverID:this.gameServerID,
				serverName:serverName,
				serverIP:serverIP,
				serverPort:serverPort,
				dbIP:dbIP,
				dbPort:dbPort,
				dbName:dbName,
				dbUser:dbUser,
				dbPassword:dbPassword,
				status:status,
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					myAlert("保存成功");
					that.resetPage();
					that.reqPage();
					$("#editServerUI").modal("close");
					getServerList(function(){
						myAlert("刷新游戏服列表成功");
					});
				}
			}
		);
	}
	//刷新游戏服列表缓存
	this.createCache=function()
	{
		reqJson
		(
			"post",
			"web_api/server.php",
			{
				action:'createCache',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					myAlert("刷新成功");
				}
			}
		);
	}
	//加载游戏服数据
	this.loadGameServer=function(gameServerID)
	{
		this.gameServerID=gameServerID;
		reqJson
		(
			"post",
			"web_api/server.php",
			{
				action:'load',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				gameServerID:gameServerID,
			},
			function(json)
			{
				if(json.result==1)//创建成功
				{
					$("#editServerUI_serverName").val(json.data.serverName);
					$("#editServerUI_serverIP").val(json.data.gameServerIP);
					$("#editServerUI_serverPort").val(json.data.gameServerPort);
					$("#editServerUI_dbIP").val(json.data.dbIP);
					$("#editServerUI_dbPort").val(json.data.dbPort);
					$("#editServerUI_dbName").val(json.data.dbName);
					$("#editServerUI_dbUser").val(json.data.dbUser);
					$("#editServerUI_dbPassword").val(json.data.dbPassword);
					$("#editServerUI_status").val(json.data.status);
					$("#editServerUI").modal("open");
				}
			}
		);
	}
	//打开开服界面
	this.openStartGameServerUI=function(gameServerID)
	{
		this.gameServerID=gameServerID;
		$("#startServerUI").modal("open");
	}
	//开服
	this.startGameServer=function()
	{
		gameServerID=this.gameServerID;
		reqJson
		(
			"post",
			redirect(gameServerID,"web_api/server.php"),
			{
				action:'start',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				bt:$("#startServerUI_bt").val(),
				serverID:gameServerID,
				serverIP:getServerInfo(gameServerID).gameServerIP
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					myAlert("开服成功");
					$("#startServerUI").modal("close");
				}
			}
		);
	}
	//发送开服通知
	this.sendStartGame=function(gameServerID)
	{
		reqJson
		(
			"post",
			"web_api/server.php",
			{
				action:'sendStartServer',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				serverID:gameServerID
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					myAlert("发送成功");
				}
				else
				{
					myAlert("发送失败");
				}
			}
		);
	}
}