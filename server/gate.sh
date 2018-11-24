#!/bin/bash
#hub守护进程脚本
app_name="jipin_gateway" 	#进程名
app_port="10049"
app_password="0af9b4c42b77600539208f8ee2bb5777"
list="127.0.0.1:10050 127.0.0.1:10051"
app_cmd="/usr/data/akserver/bin/akload load $app_name 1 $app_port $list" 	#进程命令
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
#/usr/data/jipin/server/gate.sh &