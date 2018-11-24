uiMgr.addUI
(
	"resUI",
	function()//初始化
	{
		resUI=new ResUI();
		$("#resUI_queryBtn").click(function(){
			//刷新流水列表
			resUI.resetPage();
			resUI.reqPage();
		});
		$("#resUI_resListView").scroll(function(){
			var pos=$(this).height()+this.scrollTop;
			var maxPos=this.scrollHeight;
			if(pos>=(maxPos-50))
			{
				resUI.reqPage();
			}
		});
	},
	function()//显示
	{
		var now=new Date().format("yyyy-MM-dd");
		$("#resUI_date").val(now);
		//...
	}
);

ResUI=function()
{
	var that=this;
	var page=0;
	var isPageEnd=false;
	var isReqPage=false;
	this.gameUserID=0;
	//请求页
	this.resetPage=function()
	{
		page=0;
		isPageEnd=false;
	}
	//刷新流水列表
	this.reqPage=function()
	{
		if(isReqPage)return;
		if(isPageEnd)return;
		isReqPage=true;
		reqJson
		(
			"post",
			redirectCurServer("web_api/res.php"),
			{
				action:'list',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				page:page,
				gameUserID:$("#resUI_userID").val(),
				type1:$("#resUI_type1").val(),
				type2:$("#resUI_type2").val(),
				date:strReplace($("#resUI_date").val(),"-",""),
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
							$("#resUI_resListView").html('<table></table>');
							$("#resUI_resListView table").append("<tr><td>角色ID</td><td>类型</td><td>进出</td><td>数量</td><td>余额</td><td>创建日期</td></tr>");
							$("#resUI_resListView")[0].scrollTop=0;
						}
						++page;
						var list=json.data;
						for(var i=0;i<list.length;++i)
						{
							var info=list[i];
							var str="";
							if(info.io==0)
								str='<tr>';
							else
								str='<tr style=\"background:#ff0000\">';
							str+='<td>'+info.userID+'</td>';
							str+='<td>'+info.type+'</td>';
							str+='<td>'+info.io+'</td>';
							str+='<td>'+info.count+'</td>';
							str+='<td>'+info.val+'</td>';
							str+='<td>'+info.genTime+'</td>';
							str+='</tr>';
							$("#resUI_resListView table").append(str);
						}
					}
					else
					{
						if(page==0)
						{
							$("#resUI_resListView").html('无流水数据');
						}
						isPageEnd=true;
					}
					//console.log(json);
				}
			}
		);
	}
	
}