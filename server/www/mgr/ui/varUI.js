uiMgr.addUI
(
	"varUI",
	function()//初始化
	{
		varUI=new VarUI();
		$("#editVarUI_saveVarBtn").click(function(){
			varUI.saveVar();
		});
		$("#varUI_varListView").scroll(function(){
			var pos=$(this).height()+this.scrollTop;
			var maxPos=this.scrollHeight;
			if(pos>=(maxPos-50))
			{
				varUI.reqPage();
			}
		});
	},
	function()//显示
	{
		varUI.resetPage();
		varUI.reqPage();
	}
);

VarUI=function()
{
	var that=this;
	var page=0;
	var isPageEnd=false;
	var isReqPage=false;
	this.varName=0;
	//创建json编辑器
	var options={};
	var editorVar=new JSONEditor(document.getElementById('editVarUI_jsonEditor'),options);
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
			redirectCurServer("web_api/var.php"),
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
							$("#varUI_varListView").html('<table></table>');
							$("#varUI_varListView table").append("<tr><td>变量名</td><td>操作</td></tr>");
							$("#varUI_varListView")[0].scrollTop=0;
						}
						++page;
						var list=json.data;
						for(var i=0;i<list.length;++i)
						{
							var info=list[i];
							var str='<tr>';
							str+='<td>'+info.name+'</td>';
							str+='<td>';
							str+="<button onclick='varUI.loadVar(\""+info.name+"\");'>编辑</button>";
							str+='</td>';
							str+='</tr>';
							$("#varUI_varListView table").append(str);
						}
					}
					else
					{
						if(page==0)
						{
							$("#varUI_varListView").html('无变量数据');
						}
						isPageEnd=true;
					}
					//console.log(json);
				}
			}
		);
	}
	//获取变量
	this.loadVar=function(varName)
	{
		this.varName=varName;
		reqJson
		(
			"post",
			redirectCurServer("web_api/var.php"),
			{
				action:'loadVar',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				varName:varName,
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					var json=json_decode(json.data);
					editorVar.set(json);
					$("#editVarUI").modal("open");
				}
			}
		);
	}
	//保存变量
	this.saveVar=function()
	{
		var varName=this.varName;
		reqJson
		(
			"post",
			redirectCurServer("web_api/var.php"),
			{
				action:'saveVar',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				varName:varName,
				jsonBase64Str:json_encode(editorVar.get()),
			},
			function(json)
			{
				if(json.result==1)//创建成功
				{
					$("#editVarUI").modal("close");
				}
			}
		);
	}
}