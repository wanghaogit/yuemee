#!/bin/bash
export MYSQL_CMD='C:/WAMP/MySQL/bin/mysql.exe -h127.0.0.1 -uroot -p123456 --default-character-set=utf8';
export GREP_CMD='C:/WAMP/gnu/bin/grep.exe';
export PHP_CMD='C:/WAMP/php/php.exe';
export DBS_PREFIX='yuemi_';
export DBS_LIST=('main' 'sale' 'log' );

F_DISPLAY_MENU(){
	clear;
	echo -e '\033[33m阅米数据库系统\033[0m';
	echo -e '━━━━━━━━━━━━━━━━━━━━━━━━━';
	echo -e '【\033[41;37m1\033[0m】一键重建整个数据系统';
	echo -e '【\033[41;37m2\033[0m】导入线上数据库';
	echo -e '【\033[41;37m3\033[0m】更新所有存储过程';
	echo -e '【\033[41;37m4\033[0m】运行升级脚本';
	K=5;
	for DB in ${DBS_LIST[@]};
	do
		echo -e '【\033[41;37m'$K'\033[0m】生成 '${DBS_PREFIX}${DBS_LIST[((K-5))]}' 代码';
		K=$((K+1));
	done;
	echo -e '【0】放弃操作';
};
F_RELOAD_ALL(){
	for DB in ${DBS_LIST[@]};
	do
		echo -n "	重建数据库 ${DBS_PREFIX}$DB...";
		echo -n "清空...";
		$MYSQL_CMD -e "DROP DATABASE IF EXISTS ${DBS_PREFIX}$DB" >/dev/null 2>&1;
		$MYSQL_CMD -e "CREATE DATABASE ${DBS_PREFIX}$DB DEFAULT CHARACTER SET UTF8"  > /dev/null 2>&1
		if [ -d base/$DB ]; then
			echo -n "重建.";
			for P in `ls base/$DB/*.sql 2>/dev/null`;do
				echo -n ".";
				$MYSQL_CMD ${DBS_PREFIX}$DB < $P > /dev/null 2>&1;
			done;
		fi;
		if [ -d proc/$DB ]; then
			echo '存储过程...';
			for P in `ls proc/$DB/*.sql 2>/dev/null`;do
				echo "		$P ...";
				$MYSQL_CMD ${DBS_PREFIX}$DB < $P > /dev/null 2>&1;
			done;
			echo "		OK";
		else
			echo 'OK';
		fi;
	done;
};
F_RELOAD_ONLINE(){
	for DB in ${DBS_LIST[@]};
	do
		echo -n "	重建数据库 ${DBS_PREFIX}$DB...";
		echo -n "清空...";
		$MYSQL_CMD -e "DROP DATABASE IF EXISTS ${DBS_PREFIX}$DB" >/dev/null 2>&1;
		$MYSQL_CMD -e "CREATE DATABASE ${DBS_PREFIX}$DB DEFAULT CHARACTER SET UTF8"  > /dev/null 2>&1
		if [ -f full/$DB.sql ]; then
			echo -n "重建.";
			$MYSQL_CMD ${DBS_PREFIX}$DB < full/$DB.sql > /dev/null 2>&1;
		fi;
		if [ -d proc/$DB ]; then
			echo '存储过程...';
			for P in `ls proc/$DB/*.sql 2>/dev/null`;do
				echo "		$P ...";
				$MYSQL_CMD ${DBS_PREFIX}$DB < $P > /dev/null 2>&1;
			done;
			echo "		OK";
		else
			echo 'OK';
		fi;
	done;
};
F_IMPORT_PROC(){
	for DB in ${DBS_LIST[@]};
	do
		echo "	刷新存储过程 ${DBS_PREFIX}$DB...";
		if [ -d proc/$DB ]; then
			for P in `ls proc/$DB/*.sql 2>/dev/null`;do
				echo "		$P ...";
				$MYSQL_CMD ${DBS_PREFIX}$DB < $P > /dev/null 2>&1;
			done;
			echo "		OK";
		fi;
	done;
};

F_UPGRADE_FIX(){
	for DB in ${DBS_LIST[@]};
	do
		echo "检查数据库 ${DBS_PREFIX}$DB 更新...";
		HAS_SET=`$MYSQL_CMD ${DBS_PREFIX}$DB -N -L -e "SHOW TABLES LIKE 'setting'" 2>/dev/null | $GREP_CMD '\|'`;
		if [ "$HAS_SET" == "" ]; then
			echo "	没有 setting 表,认定版本为 00000000";
			$MYSQL_CMD ${DBS_PREFIX}$DB < update/setting.sql  >/dev/null 2>/dev/null;
		else
			echo -n "	检查版本号...";
			CDB_VER=`$MYSQL_CMD ${DBS_PREFIX}$DB -N -L -e "SELECT value FROM setting WHERE name='db_version'" 2>/dev/null | $GREP_CMD '\|' | $GREP_CMD -o '[0-9]\{8\}'`;
			if [ "$CDB_VER" == "" ] ;then
				CDB_VER='00000000';
			fi;
		fi;
		echo $CDB_VER;
		for F in `ls update/$DB/*.sql 2>/dev/null`;do
			FIX_VER=`echo $F | $GREP_CMD -o '[0-9]\{8\}'`;
			IS_FORCE=`grep -o '@FORCE-UPDATE' $F`;
			if [[ -z $IS_FORCE ]]; then
				if [[ "$CDB_VER" -ge  "$FIX_VER" ]]; then
					echo "		+ $FIX_VER ... SKIP";
					continue;
				fi;
			fi;
			echo -n "		+ $FIX_VER ... ";
			$MYSQL_CMD ${DBS_PREFIX}$DB < $F >/dev/null 2>/dev/null;
			if (( $? == 0 )); then
				echo 'OK';
				$MYSQL_CMD ${DBS_PREFIX}$DB  -e "UPDATE setting SET value='$FIX_VER' WHERE name='db_version'" >/dev/null 2>&1;
			else
				echo 'ERROR';
				break;
			fi;
		done;
	done;
};

F_BUILD_PHP() {
	if [ -d .cache ]; then
		rm -rf .cache
	fi;
	$PHP_CMD install_all.php $1;
};

F_DISPLAY_MENU;
read -s -n1 KEY;
case $KEY in 
	0)
		exit;
		;;
	1)
		F_RELOAD_ALL;
		;;
	2)
		F_RELOAD_ONLINE;
		;;
	3)
		F_IMPORT_PROC;
		;;
	4)
		F_UPGRADE_FIX;
		;;
	5 | 6 | 7 | 8 | 9)
		F_BUILD_PHP ${DBS_LIST[((KEY-5))]};
		;;
esac;


read -s -n1 -p "按任意键继续 ... "
