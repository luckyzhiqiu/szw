uiMgr.addUI
(
	"accountUI",
	function()//初始化
	{
		accountUI=new AccountUI();
		$("#accountUI_queryBtn").click(function(){
			//刷新帐号列表
			accountUI.resetPage();
			accountUI.reqPage();
		});
		$("#accountUI_accountListView").scroll(function(){
			var pos=$(this).height()+this.scrollTop;
			var maxPos=this.scrollHeight;
			if(pos>=(maxPos-50))
			{
				accountUI.reqPage();
			}
		});
		$("#editAccountUI_saveBtn").click(function(){
			accountUI.saveAccount();
		});
		$("#banAccountUI_saveBtn").click(function(){
			accountUI.banAccount();
		});
	},
	function()//显示
	{
		//...
	}
);

AccountUI=function()
{
	var that=this;
	var page=0;
	var isPageEnd=false;
	var isReqPage=false;
	this.uid=0;
	//请求页
	this.resetPage=function()
	{
		page=0;
		isPageEnd=false;
	}
	//刷新帐号列表
	this.reqPage=function()
	{
		if(isReqPage)return;
		if(isPageEnd)return;
		isReqPage=true;
		reqJson
		(
			"post",
			"web_api/account.php",
			{
				action:'list',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				page:page,
				uid:$("#accountUI_uid").val(),
				type:$("#accountUI_type").val(),
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
							$("#accountUI_accountListView").html('<table></table>');
							$("#accountUI_accountListView table").append("<tr><td>ID</td><td>UID</td><td>类型</td><td>封禁</td><td>serverIDMap</td><td>创建日期</td><td>操作</td></tr>");
							$("#accountUI_accountListView")[0].scrollTop=0;
						}
						++page;
						var list=json.data;
						for(var i=0;i<list.length;++i)
						{
							var info=list[i];
							var str='<tr>';
							str+='<td>'+info.id+'</td>';
							str+='<td>'+info.uid+'</td>';
							switch(info.type)
							{
								case 0://普通帐号
									str+='<td> </td>';
									break;
								case 1://测试帐号
									str+='<td>测试</td>';
									break;
								default:
									str+='<td>未知</td>';
									break;
							}
							switch(info.ban)
							{
								case 0://未封禁
									str+='<td> </td>';
									break;
								case 1://已封禁
									str+='<td>封禁</td>';
									break;
								default:
									str+='<td>未知</td>';
									break;
							}
							str+='<td>'+info.serverIDMap+'</td>';
							str+='<td>'+info.genTime+'</td>';
							str+='<td>';
							str+="<button onclick='accountUI.openAccountUI(\""+info.uid+"\");'>设置</button>";
							str+="<button onclick='accountUI.openBanAccountUI(\""+info.uid+"\");'>封禁</button>";
							str+='</td>';
							str+='</tr>';
							$("#accountUI_accountListView table").append(str);
						}
					}
					else
					{
						if(page==0)
						{
							$("#accountUI_accountListView").html('无帐号数据');
						}
						isPageEnd=true;
					}
					//console.log(json);
				}
			}
		);
	}
	//保存帐号
	this.saveAccount=function()
	{
		var uid=this.uid;
		reqJson
		(
			"post",
			"web_api/account.php",
			{
				action:'saveAccount',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				uid:uid,
				type:$("#editAccountUI_type").val(),
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					$("#editAccountUI").modal("close");
				}
			}
		);
	}
	//封禁帐号
	this.banAccount=function()
	{
		var uid=this.uid;
		reqJson
		(
			"post",
			"web_api/account.php",
			{
				action:'banAccount',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				uid:uid,
				ban:$("#banAccountUI_ban").val(),
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					$("#banAccountUI").modal("close");
				}
			}
		);
	}
	//打开帐号界面
	this.openAccountUI=function(uid)
	{
		this.uid=uid;
		$("#editAccountUI").modal("open");
	}
	//打开封禁帐号界面
	this.openBanAccountUI=function(uid)
	{
		this.uid=uid;
		$("#banAccountUI").modal("open");
	}
}