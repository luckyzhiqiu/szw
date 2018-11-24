#!/bin/bash
#守护进程脚本
app_name="jipin_scanner" 	#进程名
app_cmd="/usr/data/akserver/bin/akserver scanner/main.aks $app_name 1 1" 	#进程命令
app_dir=/usr/data/jipin/server	#进程路径

while true;do
	info=`ps aux | grep "$app_cmd" | grep -v grep`
	if [ "$info" = '' ] ;then
		cd $app_dir
		$app_cmd
	fi
	sleep 5
done

#开机启动
#vim /etc/rc.local
#末尾添加：
#/usr/data/jipin/server/scanner.sh &