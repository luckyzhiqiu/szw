uiMgr.addUI
(
	"unionUI",
	function()//初始化
	{
		unionUI=new UnionUI();
		$("#unionUI_queryBtn").click(function(){
			//刷新列表
			unionUI.resetPage();
			unionUI.reqPage();
		});
		$("#unionUI_unionListView").scroll(function(){
			var pos=$(this).height()+this.scrollTop;
			var maxPos=this.scrollHeight;
			if(pos>=(maxPos-50))
			{
				unionUI.reqPage();
			}
		});
		$("#editUnionUI_saveBtn").click(function(){
			unionUI.saveUnion();
		});
		$("#editUnionJsonUI_saveBtn").click(function(){
			unionUI.saveUnionJson();
		});
	},
	function()//显示
	{
		//...
	}
);

UnionUI=function()
{
	var that=this;
	var page=0;
	var isPageEnd=false;
	var isReqPage=false;
	this.unionID=0;
	//创建json编辑器
	var options={};
	var editorUnion=new JSONEditor(document.getElementById('editUnionUI_jsonEditor'),options);
	var editorUnionJson=new JSONEditor(document.getElementById('editUnionJsonUI_jsonEditor'),options);
	//请求页
	this.resetPage=function()
	{
		page=0;
		isPageEnd=false;
	}
	//刷新列表
	this.reqPage=function()
	{
		if(isReqPage)return;
		if(isPageEnd)return;
		isReqPage=true;
		reqJson
		(
			"post",
			redirectCurServer("web_api/union.php"),
			{
				action:'list',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				page:page,
				name:$("#unionUI_name").val(),
				unionID:$("#unionUI_unionID").val(),
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
							$("#unionUI_unionListView").html('<table></table>');
							$("#unionUI_unionListView table").append("<tr><td>ID</td><td>名称</td><td>创建日期</td><td>操作</td></tr>");
							$("#unionUI_unionListView")[0].scrollTop=0;
						}
						++page;
						var list=json.data;
						for(var i=0;i<list.length;++i)
						{
							var info=list[i];
							var str='<tr>';
							str+='<td>'+info.id+'</td>';
							str+='<td>'+info.name+'</td>';
							str+='<td>'+info.genTime+'</td>';
							str+='<td>';
							str+="<button onclick='unionUI.loadUnion("+info.id+");'>编辑</button>";
							str+="<button onclick='unionUI.loadUnionJson("+info.id+");'>json</button>";
							str+='</td>';
							str+='</tr>';
							$("#unionUI_unionListView table").append(str);
						}
					}
					else
					{
						if(page==0)
						{
							$("#unionUI_unionListView").html('无联盟数据');
						}
						isPageEnd=true;
					}
					//console.log(json);
				}
			}
		);
	}
	//获取联盟
	this.loadUnion=function(unionID)
	{
		this.unionID=unionID;
		reqJson
		(
			"post",
			redirectCurServer("web_api/union.php"),
			{
				action:'loadUnion',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				unionID:unionID,
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					var json=json_decode(json.data);
					editorUnion.set(json);
					$("#editUnionUI").modal("open");
				}
			}
		);
	}
	//保存联盟
	this.saveUnion=function()
	{
		var unionID=this.unionID;
		reqJson
		(
			"post",
			redirectCurServer("web_api/union.php"),
			{
				action:'saveUnion',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				unionID:unionID,
				jsonBase64Str:json_encode(editorUnion.get()),
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					$("#editUnionUI").modal("close");
				}
				else
				{
					myAlert("不能修改id");
				}
			}
		);
	}
	//获取联盟json
	this.loadUnionJson=function(unionID)
	{
		this.unionID=unionID;
		reqJson
		(
			"post",
			redirectCurServer("web_api/union.php"),
			{
				action:'loadJson',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				unionID:unionID,
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					var json=json_decode(json.data);
					editorUnionJson.set(json);
					$("#editUnionJsonUI").modal("open");
				}
			}
		);
	}
	//保存联盟json
	this.saveUnionJson=function()
	{
		var unionID=this.unionID;
		reqJson
		(
			"post",
			redirectCurServer("web_api/union.php"),
			{
				action:'saveJson',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				unionID:unionID,
				jsonBase64Str:json_encode(editorUnionJson.get()),
			},
			function(json)
			{
				if(json.result==1)//创建成功
				{
					$("#editUnionJsonUI").modal("close");
				}
			}
		);
	}
}