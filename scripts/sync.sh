#!/bin/sh

TMP_DIR=/var/www/html/nocheck/scripts
ROOT_DIR=/var/www/html/nocheck

cd $ROOT_DIR

if [ -f "$TMP_DIR/$1.lock" ]; then
	echo "######`date`##### 上次未完成--" >> $TMP_DIR/$1.log
else
	touch $TMP_DIR/$1.lock
	echo "######`date`##### 上次已完成--" >> $TMP_DIR/$1.log
	php $ROOT_DIR/sync.php $1
	echo "--$?--" >> $TMP_DIR/$1.log
	rm $TMP_DIR/$1.lock
fi

