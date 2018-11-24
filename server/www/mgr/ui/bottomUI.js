uiMgr.addUI
(
	"bottomUI",
	function()
	{
		var lastClickBtnID=null;
		var uiMap=
		{
			bottomUI_accountUIBtn:"accountUI",
			bottomUI_userUIBtn:"userUI",
			bottomUI_orderUIBtn:"orderUI",
			bottomUI_resUIBtn:"resUI",
			bottomUI_unionUIBtn:"unionUI",
			bottomUI_varUIBtn:"varUI",
			bottomUI_numUIBtn:"numUI",
			bottomUI_serverUIBtn:"serverUI",
			bottomUI_publicMsgUIBtn:"publicMsgUI",
			bottomUI_cdkeyUIBtn:"cdkeyUI",
			bottomUI_settingUIBtn:"settingUI",
		};
		$(".bottom_btn").click(function(){
			var btnID=$(this).attr("id");
			if(lastClickBtnID)
			{
				$("#"+lastClickBtnID).css("background","");
			}
			$(this).css("background","#cccccc");
			lastClickBtnID=btnID;
			if(uiMap[btnID])uiMgr.showUI(uiMap[btnID]);
		});
	},
	null,null,null,false
);