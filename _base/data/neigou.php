<?php
include '../config.php';
include Z_ROOT . '/Database.php';
include Z_ROOT . '/Data/MySQL.php';
$mysql = new \Ziima\Data\MySQLConnection(MYSQL_WRITER,MYSQL_READER);
$mysql->execute("TRUNCATE TABLE `yuemi_sale`.`ext_neigou_catagory`");
$map = [];
$csv = file_get_contents('neigou.csv');
$lin = explode("\n", $csv);
foreach($lin as $l){
	if(empty($l))
		continue;
	$fld = explode(',', $l);
	if(count($fld) != 6)
		continue;
	if(!is_numeric($fld[0]))
		continue;
	$c1 = intval($fld[0]);
	$n1 = $fld[1];
	$c2 = intval($fld[2]);
	$n2 = $fld[3];
	$c3 = intval($fld[4]);
	$n3 = $fld[5];
	if(!in_array($c1, $map)){
		$mysql->execute("INSERT INTO `yuemi_sale`.`ext_neigou_catagory` (`id`,`parent_id`,`name`) VALUES (%d,0,'%s')",$c1,$n1);
		$map[] = $c1;
	}
	if(!in_array($c2, $map)){
		$mysql->execute("INSERT INTO `yuemi_sale`.`ext_neigou_catagory` (`id`,`parent_id`,`name`) VALUES (%d,%d,'%s')",$c2,$c1,$n2);
		$map[] = $c2;
	}
	if(!in_array($c3, $map)){
		$mysql->execute("INSERT INTO `yuemi_sale`.`ext_neigou_catagory` (`id`,`parent_id`,`name`) VALUES (%d,%d,'%s')",$c3,$c2,$n3);
		$map[] = $c3;
	}
}
echo "DONE\n";