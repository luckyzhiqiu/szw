#!/bin/bash
#hub守护进程脚本
name="szw001"
app_port="5102"


app_password="0af9b4c42b77600539208f8ee2bb5777"
app_name="${name}_hub" 	#进程名
app_cmd="/usr/data/$name/akload hub $app_name 1 $app_port $app_password" 	#进程命令
app_dir="/usr/data/$name"	#进程路径
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
#/usr/data/szw001/hub.sh &