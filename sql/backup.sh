#!/bin/bash
#
# Backup script for Yuemi system 
#
# Author : eglic <eglic@ziima.cn>
# Create at 2018-03-11

export MYSQL_CMD='/opt/mysql/bin/mysql  -hrm-uf645fgt710n5mglw.mysql.rds.aliyuncs.com -uyuemi -pQ1w2e3r4t5 --default-character-set=utf8';
export DUMP_CMD='/opt/mysql/bin/mysqldump -hrm-uf645fgt710n5mglw.mysql.rds.aliyuncs.com -uyuemi -pQ1w2e3r4t5 --default-character-set=utf8';
export GREP_CMD='/usr/bin/grep';
export PHP_CMD='/opt/php/bin/php';
export DBS_PREFIX='yuemi_';
export DBS_LIST=('main' 'sale' 'log');

P=`/bin/date "+%Y/%m/%d"`;
mkdir -p /data/nfs/backup/mysql/$P;
cd /data/nfs/backup/mysql/$P;
F=`/bin/date "+%H"`;
if [ -f ${F}.tar ]; then
	rm -f ${F}.tar;
fi;
if [ -f ${F}.tar.gz ]; then
	rm -f ${F}.tar.gz;
fi;
for DB in ${DBS_LIST[@]};
do
	$DUMP_CMD $DBS_PREFIX$DB > $DB.sql 2>/dev/null;
done;
tar cf $F.tar *.sql;
gzip -9 $F.tar;
rm -f *.sql;

