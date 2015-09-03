#!/bin/bash

# dependency: sbutils, wget

deviceinfoPassword=YOUR-PASSWORD

# battery
battery=$(sbdevice -l)
charging=$(if [ "`sbdevice -s`" = "Charging" ]; then echo "c"; else echo ""; fi)
if [ ! -f /tmp/info-battery.txt ] || [ "$(cat /tmp/info-battery.txt)" != "$charging$battery" ]; then
	echo "$charging$battery" > /tmp/info-battery.txt && chown mobile /tmp/info-battery.txt
	wget -qO- "http://YOUR-URL/deviceinfo.php?p=$deviceinfoPassword&s=b&v=$battery&$charging" &> /dev/null
fi
