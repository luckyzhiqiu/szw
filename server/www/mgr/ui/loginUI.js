uiMgr.addUI
(
	"loginUI",
	function()
	{
		$("#loginUI_loginBtn").click(function(){
			var account=$("#loginUI_account").val();
			var password=$.md5($("#loginUI_password").val());
			reqJson
			(
				"post",
				"web_api/login.php",
				{
					account:account,
					password:password
				},
				function(json)
				{
					if(json.result==1)
					{
						//存储通行证到本地
						cache.set("userID",json.userID);
						cache.set("tick",json.tick);
						
						$("#loginUI2").modal("close");
						
						uiMgr.showUI("topUI");
						uiMgr.showUI("bottomUI");
						getServerList(function(){
							$("#bottomUI_userUIBtn").click();
						})
					}
					else
					{
						onLoginError();
					}
				}
			);
		});
		
	},
	function()
	{
		$("#loginUI2").modal("open");
	}
);