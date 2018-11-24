uiMgr.addUI
(
	"cdkeyUI",
	function()//初始化
	{
		cdkeyUI=new CDkeyUI();
		$("#cdkeyUI_queryBtn").click(function(){
			//刷新列表
			cdkeyUI.resetPage();
			cdkeyUI.reqPage();
		});
		$("#cdkeyUI_listView").scroll(function(){
			var pos=$(this).height()+this.scrollTop;
			var maxPos=this.scrollHeight;
			if(pos>=(maxPos-50))
			{
				cdkeyUI.reqPage();
			}
		});
		$("#cdkeyUI_addBtn").click(function(){
			$("#addCDKEYUI").modal('open');
		});
		$("#addCDKEYUI_newBtn").click(function(){
			cdkeyUI.add();
		});
		$("#cdkeyUI_removeBatchBtn").click(function(){
			$("#removeBatchCDKEYUI").modal('open');
		});
		$("#removeBatchCDKEYUI_removeBtn").click(function(){
			cdkeyUI.delBatch();
		});
		$("#cdkeyUI_selectBatchBtn").click(function(){
			$("#selectBatchCDKEYUI").modal('open');
		});
		$("#selectBatchCDKEYUI_selectBtn").click(function(){
			cdkeyUI.selectBatch();
		});
	},
	function()//显示
	{
		//...
	}
);

CDkeyUI=function()
{
	var that=this;
	var page=0;
	var isPageEnd=false;
	var isReqPage=false;
	this.cdkeyID=0;
	//请求页
	this.resetPage=function()
	{
		page=0;
		isPageEnd=false;
	}
	//刷新cdkey列表
	this.reqPage=function()
	{
		if(isReqPage)return;
		if(isPageEnd)return;
		isReqPage=true;
		reqJson
		(
			"post",
			"web_api/cdkey.php",
			{
				action:'list',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				page:page,
				type:$("#cdkeyUI_type").val(),
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
							$("#cdkeyUI_listView").html('<table></table>');
							$("#cdkeyUI_listView table").append("<tr><td>ID</td><td>序列号</td><td>类型</td><td>使用次数</td><td>创建日期</td><td>操作</td></tr>");
							$("#cdkeyUI_listView")[0].scrollTop=0;
						}
						++page;
						var list=json.data;
						for(var i=0;i<list.length;++i)
						{
							var info=list[i];
							var str='<tr>';
							str+='<td>'+info.id+'</td>';
							str+='<td>'+info.sn+'</td>';
							if(info.type==0)
								str+='<td>一次性</td>';
							else if(info.type==1)
								str+='<td>通用</td>';
							str+='<td>'+info.useCount+'</td>';	
							str+='<td>'+info.genTime+'</td>';
							str+='<td>';
							/*str+="<button onclick='cdkeyUI.edit("+info.id+");'>编辑</button>";*/
							str+="<button onclick='cdkeyUI.del("+info.id+");'>删除</button>";
							str+="<button onclick='cdkeyUI.createRefCDKEY("+info.id+");'>创建引用码</button>";
							str+='</td>';
							str+='</tr>';
							$("#cdkeyUI_listView table").append(str);
						}
					}
					else
					{
						if(page==0)
						{
							$("#cdkeyUI_listView").html('无CDKEY数据');
						}
						isPageEnd=true;
					}
					//console.log(json);
				}
			}
		);
	}
	//删除cdkey
	this.del=function(cdkeyID)
	{
		this.cdkeyID=cdkeyID;
		$("#cdkeyUI_removeUI").modal({
			relatedTarget:this,
			onConfirm: function(options)
			{
				reqJson
				(
					"post",
					"web_api/cdkey.php",
					{
						action:'del',
						userID:cache.get('userID'),
						tick:cache.get('tick'),
						cdkeyID:that.cdkeyID,
					},
					function(json)
					{
						if(json.result==1)//成功
						{
							myAlert("删除成功");
							//刷新列表
							cdkeyUI.resetPage();
							cdkeyUI.reqPage();
						}
					}
				);
			}
		});
	}
	//批量删除cdkey
	this.delBatch=function()
	{
		$("#cdkeyUI_removeUI").modal({
			relatedTarget:this,
			onConfirm: function(options)
			{
				reqJson
				(
					"post",
					"web_api/cdkey.php",
					{
						action:'delBatch',
						userID:cache.get('userID'),
						tick:cache.get('tick'),
						datetime:$("#removeBatchCDKEYUI_datetime").val(),
					},
					function(json)
					{
						if(json.result==1)//成功
						{
							$("#removeBatchCDKEYUI").modal('close');
							myAlert("删除成功");
							//刷新列表
							cdkeyUI.resetPage();
							cdkeyUI.reqPage();
						}
					}
				);
			}
		});
	}
	//批量查询cdkey
	this.selectBatch=function()
	{
		reqJson
		(
			"post",
			"web_api/cdkey.php",
			{
				action:'selectBatch',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				datetime:$("#selectBatchCDKEYUI_datetime").val(),
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					var str="";
					for(var i=0;i<json.data.length;++i)
					{
						str+=json.data[i];
						str+="\r\n"
					}
					$("#selectBatchCDKEYUI_listView").html(str);
				}
			}
		);
	}
	//添加cdkey
	this.add=function()
	{
		reqJson
		(
			"post",
			"web_api/cdkey.php",
			{
				action:'add',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				type:$("#addCDKEYUI_type").val(),
				soldier:$("#addCDKEYUI_soldier").val(),
				money:$("#addCDKEYUI_money").val(),
				food:$("#addCDKEYUI_food").val(),
				gold:$("#addCDKEYUI_gold").val(),
				gold2:$("#addCDKEYUI_gold2").val(),
				item:$("#addCDKEYUI_item").val(),
				heroID:$("#addCDKEYUI_heroID").val(),
				wifeID:$("#addCDKEYUI_wifeID").val(),
				count:$("#addCDKEYUI_count").val(),
			},
			function(json)
			{
				if(json.result==1)//成功
				{
					$("#addCDKEYUI").modal('close');
					var snArr=json.data;
					var str="";
					for(var i=0;i<snArr.length;++i)
					{
						str+=snArr[i];
						str+="\r\n";
					}
					$("#showCDKEYUI_listView").html(str);
					$("#showCDKEYUI").modal('open');
					//刷新列表
					cdkeyUI.resetPage();
					cdkeyUI.reqPage();
				}
			}
		);
	}
	
}