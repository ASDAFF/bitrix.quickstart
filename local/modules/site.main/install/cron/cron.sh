#!/bin/sh
#
# @category
# @link		http://.ru
# @revision	$Revision: 2062 $
# @date		$Date: 2014-10-23 14:18:32 +0400 (Чт, 23 окт 2014) $
# 
# Usage example:
# /absolute/path/to/this/file/in/birtix/auxiliary/cron.sh -m|h|d [-f|s]
# -m - minutely
# -h - hourly
# -d - daily
# -f - log to file
# -s - log to screen (default)
#
# Restriction: this file must be run with absolute path

umask 0000

PHP_PATH=/usr/bin/php

CURRENT_DIR=${0%"/cron.sh"}

LOG_FILE=$CURRENT_DIR"/cron"$1".log"
if [ "$2" = "-f" ]; then
	LOG_MODE=1
else
	LOG_MODE=0
fi

#Logger
log(){
	DATELOG=`date "+%Y-%m-%d %H:%M:%S"`
	if [ $LOG_MODE = 1 ]; then
		echo "$$: === "$DATELOG" ===" >> $LOG_FILE
		echo "$$: $1" >> $LOG_FILE
	else
		echo "$$: === "$DATELOG" ==="
		echo "$$: $1"
	fi
}

#Task runner
runTask(){
	log "Starting '$1' like '$PHP_PATH $CURRENT_DIR/$2'"
	if [ $LOG_MODE = 1 ]; then
		$PHP_PATH $CURRENT_DIR/$2 >> $LOG_FILE
	else
		$PHP_PATH $CURRENT_DIR/$2
	fi
}

#Detect previous runned instance
SELF_COUNT=2
RUN_COUNT=`ps axw -opid,command|grep "$0 $1"|grep -v grep|wc -l`
if [ $RUN_COUNT = 0 ]; then
	log "No self processes found"
	exit 0
fi
if [ $RUN_COUNT != $SELF_COUNT ]; then
	log "Other process runned ($RUN_COUNT)"
	log "`ps axw -opid,command|grep "$0 $1"|grep -v grep`"
	exit 0
fi


log "BEGIN $1"

#Minutely
if [ "$1" = "-m" ]; then
	log 'No minutely tasks'
fi

#Hourly
if [ "$1" = "-h" ]; then
	log 'No hourly tasks'
	#runTask 'Subscribe sender' 'subscribe.php'
fi

#Daily
if [ "$1" = "-d" ]; then
	log 'No daily tasks'
fi

log "END $1"
