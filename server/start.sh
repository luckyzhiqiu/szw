#!/bin/bash
#启动游戏服
./hub.sh 0 &
./scanner.sh &
./game.sh 0 &
./game.sh 1 &
./tinyWeb.sh 0 &
./gate.sh 0 &