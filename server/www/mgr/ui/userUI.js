uiMgr.addUI
(
	"userUI",
	function()//初始化
	{
		userUI=new UserUI();
		$("#userUI_queryBtn").click(function(){
			//刷新用户列表
			userUI.resetPage();
			userUI.reqPage();
		});
		$("#userUI_userListView").scroll(function(){
			var pos=$(this).height()+this.scrollTop;
			var maxPos=this.scrollHeight;
			if(pos>=(maxPos-50))
			{
				userUI.reqPage();
			}
		});
		$("#editJsonUI_saveJsonBtn").click(function(){
			userUI.saveJson();
		});
		$("#editJsonExtUI_saveJsonBtn").click(function(){
			userUI.saveJsonExt();
		});
		$("#editUserUI_saveBtn").click(function(){
			userUI.saveUser();
		});
		$("#sendMailUI_sendBtn").click(function(){
			userUI.sendMail();
		});
		$("#limitChatUI_yesBtn").click(function(){
			userUI.sendlimitChat();
		});
		$("#userUI_sendMailToAllBtn").click(function(){
			userUI.openSendMailUI(0);
		});
	},
	function()//显示
	{
		//...
	}
);

UserUI=function()
{
	var that=this;
	var page=0;
	var isPageEnd=false;
	var isReqPage=false;
	this.gameUserID=0;
	//创建json编辑器
	var options={};
	var editor=new JSONEditor(document.getElementById('editJsonUI_jsonEditor'),options);
	var editorExt=new JSONEditor(document.getElementById('editJsonExtUI_jsonEditor'),options);
	var editorUser=new JSONEditor(document.getElementById('editUserUI_jsonEditor'),options);
	//请求页
	this.resetPage=function()
	{
		page=0;
		isPageEnd=false;
	}
	//刷新用户列表
	this.reqPage=function()
	{
		if(isReqPage)return;
		if(isPageEnd)return;
		isReqPage=true;
		reqJson
		(
			"post",
			redirectCurServer("web_api/user.php"),
			{
				action:'list',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				page:page,
				nickname:$("#userUI_nickname").val(),
				gameUserID:$("#userUI_userID").val(),
				type:$("#userUI_type").val(),
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
							$("#userUI_userListView").html('<table></table>');
							$("#userUI_userListView table").append("<tr><td>ID</td><td>UID</td><td>昵称</td><td>等级</td><td>上线</td><td>创建日期</td><td>操作</td></tr>");
							$("#userUI_userListView")[0].scrollTop=0;
						}
						++page;
						var list=json.data;
						for(var i=0;i<list.length;++i)
						{
							var info=list[i];
							var str='<tr>';
							str+='<td>'+info.id+'</td>';
							str+='<td>'+info.uid+'</td>';
							str+='<td>'+info.nickname+'</td>';
							str+='<td>'+info.level+'</td>';
							if(info.online==1)
								str+='<td>是</td>';
							else
								str+='<td> </td>';
							str+='<td>'+info.genTime+'</td>';
							str+='<td>';
							str+="<button onclick='userUI.openLimitChatUI("+info.id+");'>禁言</button>";
							str+="<button onclick='userUI.openSendMailUI("+info.id+");'>发邮件</button>";
							str+="<button onclick='userUI.lookMailUI("+info.id+");'>查看邮件</button>";
							str+="<button onclick='userUI.loadUser("+info.id+");'>user</button>";
							str+="<button onclick='userUI.loadJson("+info.id+");'>json</button>";
							str+="<button onclick='userUI.loadJsonExt("+info.id+");'>jsonExt</button>";
							str+='</td>';
							str+='</tr>';
							$("#userUI_userListView table").append(str);
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
	//获取user
	this.loadUser=function(userID)
	{
		this.gameUserID=userID;
		reqJson
		(
			"post",
			redirectCurServer("web_api/user.php"),
			{
				action:'loadUser',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				gameUserID:userID,
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					var json=json_decode(json.data);
					editorUser.set(json);
					$("#editUserUI").modal("open");
				}
			}
		);
	}
	//获取json
	this.loadJson=function(userID)
	{
		this.gameUserID=userID;
		reqJson
		(
			"post",
			redirectCurServer("web_api/user.php"),
			{
				action:'loadJson',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				gameUserID:userID,
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					var json=json_decode(json.data);
					editor.set(json);
					$("#editJsonUI").modal("open");
				}
			}
		);
	}
	//获取jsonExt
	this.loadJsonExt=function(userID)
	{
		this.gameUserID=userID;
		reqJson
		(
			"post",
			redirectCurServer("web_api/user.php"),
			{
				action:'loadJsonExt',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				gameUserID:userID,
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					var json=json_decode(json.data);
					editorExt.set(json);
					$("#editJsonExtUI").modal("open");
				}
			}
		);
	}
	//保存json
	this.saveJson=function()
	{
		var userID=this.gameUserID;
		reqJson
		(
			"post",
			redirectCurServer("web_api/user.php"),
			{
				action:'saveJson',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				gameUserID:userID,
				jsonBase64Str:json_encode(editor.get()),
			},
			function(json)
			{
				if(json.result==1)//创建成功
				{
					$("#editJsonUI").modal("close");
				}
			}
		);
	}
	//保存jsonExt
	this.saveJsonExt=function()
	{
		var userID=this.gameUserID;
		reqJson
		(
			"post",
			redirectCurServer("web_api/user.php"),
			{
				action:'saveJsonExt',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				gameUserID:userID,
				jsonBase64Str:json_encode(editorExt.get()),
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					$("#editJsonExtUI").modal("close");
				}
			}
		);
	}
	//保存user
	this.saveUser=function()
	{
		var userID=this.gameUserID;
		reqJson
		(
			"post",
			redirectCurServer("web_api/user.php"),
			{
				action:'saveUser',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				gameUserID:userID,
				jsonBase64Str:json_encode(editorUser.get()),
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					$("#editUserUI").modal("close");
				}
				else
				{
					myAlert("不能修改id");
				}
			}
		);
	}
	//查看邮件界面
	this.lookMailUI=function(userID)
	{
		this.gameUserID=userID;
		reqJson
		(
			"post",
			redirectCurServer("web_api/user.php"),
			{
				action:'mailList',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				gameUserID:this.gameUserID,
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					var mailArr=json.data;
					var str="";
					for(var i=0;i<mailArr.length;++i)
					{
						var mail=mailArr[i];
						str+="时间："+mail.genTime+"<br />";
						str+="标题："+mail.title+"<br />";
						str+="正文："+mail.body+"<br />";
					}
					$("#lookMailUI_listView").html(str);
					$("#lookMailUI").modal("open");
				}
			}
		);
	}
	//打开发送邮件界面
	this.openSendMailUI=function(userID)
	{
		this.gameUserID=userID;
		$("#sendMailUI").modal("open");
	}
	//打开禁言界面
	this.openLimitChatUI=function(userID)
	{
		this.gameUserID=userID;
		$("#limitChatUI").modal("open");
	}
	//禁言请求
	this.sendlimitChat=function()
	{
		var userID=this.gameUserID;
		reqJson
		(
			"post",
			redirectCurServer("web_api/user.php"),
			{
				action:'limitChat',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				gameUserID:userID,
				switch:$("#limitChatUI_switch").val(),
				time:$("#limitChatUI_time").val(),
			},
			function(json)
			{
				if(json.result==1)//创建成功
				{
					myAlert("禁言成功");
					$("#limitChatUI").modal("close");
				}
			}
		);
	}
	//发送邮件
	this.sendMail=function()
	{
		var userID=this.gameUserID;
		var sendType='';
		if(userID==0)sendType="all";
		else sendType="one";
		reqJson
		(
			"post",
			redirectCurServer("web_api/user.php"),
			{
				action:'sendMail',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				gameUserID:userID,
				title:$("#sendMailUI_title").val(),
				body:$("#sendMailUI_body").val(),
				money:$("#sendMailUI_money").val(),
				food:$("#sendMailUI_food").val(),
				soldier:$("#sendMailUI_soldier").val(),
				gold:$("#sendMailUI_gold").val(),
				gold2:$("#sendMailUI_gold2").val(),
				item:$("#sendMailUI_item").val(),
				sendType:sendType,
			},
			function(json)
			{
				if(json.result==1)//创建成功
				{
					myAlert("发送邮件成功");
					$("#sendMailUI").modal("close");
				}
			}
		);
	}
}