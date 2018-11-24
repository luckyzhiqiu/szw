#!/bin/bash
#关闭游戏服

#杀死守护进程
ps aux | grep gate.sh | grep -v grep | awk '{print $2}' | tr '\n' ' ' | xargs kill -9
ps aux | grep game.sh | grep -v grep | awk '{print $2}' | tr '\n' ' ' | xargs kill -9
ps aux | grep hub.sh | grep -v grep | awk '{print $2}' | tr '\n' ' ' | xargs kill -9
ps aux | grep scanner.sh | grep -v grep | awk '{print $2}' | tr '\n' ' ' | xargs kill -9
ps aux | grep tinyWeb.sh | grep -v grep | awk '{print $2}' | tr '\n' ' ' | xargs kill -9

#杀死节点
ps aux | grep jipin | grep -v grep | awk '{print $2}' | tr '\n' ' ' | xargs kill -9