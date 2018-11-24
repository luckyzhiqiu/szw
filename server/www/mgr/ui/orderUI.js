uiMgr.addUI
(
	"orderUI",
	function()//初始化
	{
		orderUI=new OrderUI();
		$("#orderUI_queryBtn").click(function(){
			//刷新订单列表
			orderUI.resetPage();
			orderUI.reqPage();
		});
		$("#orderUI_orderListView").scroll(function(){
			var pos=$(this).height()+this.scrollTop;
			var maxPos=this.scrollHeight;
			if(pos>=(maxPos-50))
			{
				orderUI.reqPage();
			}
		});
		$("#orderUI_rmbBtn").click(function(){
			//刷新订单列表
			orderUI.rmb();
		});
	},
	function()//显示
	{
		//...
	}
);

OrderUI=function()
{
	var that=this;
	var page=0;
	var isPageEnd=false;
	var isReqPage=false;
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
			redirectCurServer("web_api/order.php"),
			{
				action:'list',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				page:page,
				gameUserID:$("#orderUI_userID").val(),
				num:$("#orderUI_num").val(),
				order_no:$("#orderUI_order_no").val(),
				status:$("#orderUI_status").val(),
				get:$("#orderUI_get").val(),
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
							$("#orderUI_orderListView").html('<table></table>');
							$("#orderUI_orderListView table").append("<tr><td>ID</td><td>userID</td><td>RMB</td><td>订单</td><td>付款</td><td>发货</td><td>创建日期</td><td>操作</td></tr>");
							$("#orderUI_orderListView")[0].scrollTop=0;
						}
						++page;
						var list=json.data;
						for(var i=0;i<list.length;++i)
						{
							var info=list[i];
							var str='<tr>';
							str+='<td>'+info.id+'</td>';
							str+='<td>'+info.userID+'</td>';
							str+='<td>'+(parseInt(info.rmb)/100)+'</td>';
							str+='<td>'
							str+=info.type+'（类型）<br />';
							str+=info.num+'（游戏）<br />';
							str+=info.order_no+'（平台）';
							str+='</td>';
							if(info.status==1)
								str+='<td>是</td>';
							else
								str+='<td> </td>';
							if(info.get==1)
								str+='<td>是</td>';
							else
								str+='<td> </td>';
							str+='<td>'+info.genTime+'</td>';
							str+='<td>';
							str+="<button onclick='orderUI.checkOrder(\""+info.num+"\");'>验证</button>";
							str+='</td>';
							str+='</tr>';
							$("#orderUI_orderListView table").append(str);
						}
					}
					else
					{
						if(page==0)
						{
							$("#orderUI_orderListView").html('无订单数据');
						}
						isPageEnd=true;
					}
					//console.log(json);
				}
			}
		);
	}
	//获取user
	this.checkOrder=function(num)
	{
		reqJson
		(
			"post",
			redirectCurServer("web_api/order.php"),
			{
				action:'checkOrder',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				num:num,
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					myAlert("验证请求已发送");
				}
			}
		);
	}
	//收入
	this.rmb=function(num)
	{
		reqJson
		(
			"post",
			redirectCurServer("web_api/order.php"),
			{
				action:'rmb',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					myAlert((json.rmb/100));
				}
			}
		);
	}
	
}