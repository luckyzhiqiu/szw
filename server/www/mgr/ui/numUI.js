uiMgr.addUI
(
	"numUI",
	function()
	{
		exportExcelTools=new ExportExcelTools("downloadExcelFile");
		numUI=new NumUI();
		$("#numUI_createNumBtn").click(function(){
			$("#createNumUI_numName").val("");
			tableEditor.setData(
				[
					["id","编号"],
					["name","名称"],
					["attack","攻击"],
					["defend","防御"],
				],
				[
					["1","关羽","100","100"],
					["2","张飞","103","90"],
				]
			);
			$("#createNumUI").show();
		});
		
		$("#createNumUI_closeBtn").click(function(){
			$("#createNumUI").hide();
		});
		
		$("#headDiv_closeBtn").click(function(){
			$("#headDiv").modal("close");
		});
		
		$("#createNumUI_headBtn").click(function(){
			tableEditor.updateHeadView();
			$("#headDiv").modal("open");
		});
		
		$("#createNumUI_addColBtn").click(function(){
			tableEditor.addCol();
		});
		$("#createNumUI_removeColBtn").click(function(){
			tableEditor.removeLastCol();
		});
		
		$("#createNumUI_addRowBtn").click(function(){
			tableEditor.addRow();
			//console.log(tableEditor.getData());
		});
		$("#createNumUI_removeRowBtn").click(function(){
			tableEditor.removeRow();
		});
		$("#createNumUI_showDataBtn").click(function(){
			$("#createNumUI_dataView").show();
		});
		$("#createNumUI_updateTableBtn").click(function(){
			tableEditor.updateDataFromView();
			tableEditor.updateHeadFromView();
			tableEditor.updateDataView();
			$("#headDiv").modal("close");
		});
		$("#createNumUI_saveBtn").click(function(){
			numUI.save();
		});
		$("#createNumUI_exportBtn").click(function(){
			var numName=$("#createNumUI_numName").val();
			if(numName=="")numName="这里是下载的文件名";
			$("#downloadExcelFile").attr("download",numName+".xlsx");
			numUI.exportExcel();
		});
		
		//更新表格内容位置，使其不被表头遮挡，更新表格高度，使其一直覆盖窗口底部
		function setTableView()
		{
			var h=$("#createNumUI_dataView table:nth-child(1)").height();
			$("#createNumUI_dataView table:nth-child(2)").css("margin-top",h+"px");
			var hh=$(window).height()-$("#createNumUI_dataView").offset().top;
			$("#createNumUI_dataView").height(hh);
			setTimeout(setTableView,1000);
		}
		setTableView();
		
		tableEditor=new AKTableEditor
		(
			"createNumUI_headView",
			"createNumUI_dataView",
			{
				head:[],
				data:[]
			}
		);
		
	},
	function()
	{
		numUI.list();
	}
);

NumUI=function()
{
	var that=this;
	var curNumName="";
	this.list=function()
	{
		reqJson
		(
			"post",
			"web_api/num.php",
			{
				action:'list',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
			},
			function(json)
			{
				if(json.result==1)
				{
					//console.log(json);
					$("#numUI_numListView").html('<table></table>');
					$("#numUI_numListView table").append("<tr><td>名称</td><td>操作</td></tr>");
					var data=json.data;
					for(var i=0;i<data.length;++i)
					{
						var fileName=data[i];
						var arr=fileName.split(".");
						var name=arr[0];
						var row="<tr>";
						row+="<td onclick='numUI.load(\""+name+"\");'>"+name+"</td>";
						row+="<td><button onclick='numUI.removeYesNo(\""+name+"\");'>删除</button></td>";
						row+="</tr>";
						$("#numUI_numListView table").append(row);
					}
					$("#numUI_numListView").scrollTop(0);
				}
			}
		);
	}
	this.remove=function(numName)
	{
		reqJson
		(
			"post",
			"web_api/num.php",
			{
				action:'remove',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				name:numName,
			},
			function(json)
			{
				if(json.result==1)
				{
					myAlert("删除成功");
					that.list();
				}
			}
		);
	}
	this.removeYesNo=function(numName)
	{
		curNumName=numName;
		$("#numUI_removeUI").modal({
			relatedTarget:this,
			onConfirm: function(options)
			{
				that.remove(curNumName);
			}
		});
	}
	this.load=function(numName)
	{
		reqJson
		(
			"post",
			"web_api/num.php",
			{
				action:'load',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				name:numName,
			},
			function(json)
			{
				if(json.result==1)
				{
					var jsonStr=base64_decode(json.data.base64JsonStr);
					var json=eval("("+jsonStr+")");
					$("#createNumUI_dataView").hide();
					tableEditor.setData(json.head,json.data);
					$("#createNumUI_numName").val(numName);
					$("#createNumUI").show();
				}
				else
				{
					myAlert("读取失败");
				}
			}
		);
	}
	this.save=function()
	{
		var numName=$("#createNumUI_numName").val();
		var jsonStr=JSON.stringify(tableEditor.getData());
		reqJson
		(
			"post",
			"web_api/num.php",
			{
				action:'save',
				userID:cache.get('userID'),
				tick:cache.get('tick'),
				name:numName,
				base64JsonStr:base64_encode(jsonStr),
			},
			function(json)
			{
				if(json.result==1)
				{
					myAlert("保存成功");
					$("#createNumUI").hide();
					that.list();
					
				}
				else if(json.result==-1)
				{
					myAlert("数值名称为空");
				}
			}
		);
	}
	this.exportExcel=function()
	{
		var json=tableEditor.getData();
		var head=json.head;
		var data=json.data;
		var outJson=[];
		//第一和第二行
		var row1={};
		var row2={};
		for(var i=0;i<head.length;++i)
		{
			var info=head[i];
			row1[info[1]]=info[1];
			row2[info[1]]=info[0];
		}
		outJson.push(row1);
		outJson.push(row2);
		//后面的行
		for(var i=0;i<data.length;++i)
		{
			var row={};
			var info=data[i];
			for(var j=0;j<head.length;++j)
			{
				var key=head[j][1];
				var col=info[j];
				row[key]=col;
			}
			outJson.push(row);
		}
		exportExcelTools.export(outJson);
	}
}

function importExcel(obj)
{
	var wb;
	var f=obj.files[0];
	var reader=new FileReader();
	reader.onload=function(e)
	{
		var data=e.target.result;
		wb=XLSX.read(data,{type:'binary'});
		var json=XLSX.utils.sheet_to_json(wb.Sheets[wb.SheetNames[0]]);
		if(json.length>0)
		{
			var head=[];
			var data=[];
			var jsonHead=json[0];
			for(var key in jsonHead)
			{
				head.push([jsonHead[key],key]);
			}
			for(var i=1;i<json.length;++i)
			{
				var row=json[i];
				var rowData=[];
				for(var key in row)
				{
					rowData.push(row[key]);
				}
				data.push(rowData);
			}
			tableEditor.setData(head,data);
		}
	}
	reader.readAsBinaryString(f);
};

function ExportExcelTools(aID)
{
	var tmpDown; //导出的二进制对象
	function downloadExcel(json, type) {
		var keyMap = [];//获取键
		for(k in json[0]) {
			keyMap.push(k);
		}
		var tmpdata = [];//用来保存转换好的json 
		json.map((v, i) => keyMap.map((k, j) => Object.assign({}, {
			v: v[k],
			position: (j > 25 ? getCharCol(j) : String.fromCharCode(65 + j)) + (i + 1)
		}))).reduce((prev, next) => prev.concat(next)).forEach((v, i) => tmpdata[v.position] = {
			v: v.v
		});
		var outputPos = Object.keys(tmpdata); //设置区域,比如表格从A1到D10
		var tmpWB = {
			SheetNames: ['mySheet'], //保存的表标题
			Sheets: {
				'mySheet': Object.assign({},
					tmpdata, //内容
					{
						'!ref': outputPos[0] + ':' + outputPos[outputPos.length - 1] //设置填充区域
					})
			}
		};
		tmpDown = new Blob([s2ab(XLSX.write(tmpWB, 
			{bookType: (type == undefined ? 'xlsx':type),bookSST: false, type: 'binary'}//这里的数据是用来定义导出的格式类型
			))], {
			type: ""
		}); //创建二进制对象写入转换好的字节流
		var href = URL.createObjectURL(tmpDown); //创建对象超链接
		document.getElementById(aID).href = href; //绑定a标签
		document.getElementById(aID).click(); //模拟点击实现下载
		setTimeout(function() { //延时释放
			URL.revokeObjectURL(tmpDown); //用URL.revokeObjectURL()来释放这个object URL
		}, 100);
	}

	function s2ab(s) { //字符串转字符流
		var buf = new ArrayBuffer(s.length);
		var view = new Uint8Array(buf);
		for(var i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
		return buf;
	}
	// 将指定的自然数转换为26进制表示。映射关系：[0-25] -> [A-Z]。
	function getCharCol(n) {
		var temCol = '',
			s = '',
			m = 0
		while(n > 0) {
			m = n % 26 + 1
			s = String.fromCharCode(m + 64) + s
			n = (n - m) / 26
		}
		return s
	}
	this.export=function(json)
	{
		downloadExcel(json);
	}
}