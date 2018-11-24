#!/bin/bash
#游戏逻辑节点守护进程脚本
name="szw001"


app_name="$name" 	#进程名
app_cmd="/usr/data/$app_name/akserver game/init.aks $app_name 1 1 0" 	#进程命令
app_dir="/usr/data/$app_name"	#进程路径
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
#/usr/data/szw001/game.sh &