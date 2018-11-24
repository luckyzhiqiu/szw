#!/bin/bash
#启动游戏服
./hub.sh &
./scanner.sh &
./game.sh &
./tinyWeb.sh &