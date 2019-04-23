<?php
/**
 * 生成数据库框架代码
 * 
 */
include Z_ROOT . '/Ziima.php';
include Z_ROOT . '/Database.php';
include Z_ROOT . '/Data/MySQL.php';
include Z_ROOT . '/Template.php';
include Z_ROOT . '/Modifier.php';


// 读取数据库前缀和数据库列表
$cfg = file_get_contents('install_lnx.sh');
$cfg_lines = explode("\n", $cfg);
for($i =0;$i< count($cfg_lines);$i ++){
	if(strlen($cfg_lines[$i]) < 8)
		continue;
	$m = [];
	$r = preg_match('/^export\s+DBS_PREFIX\=\'(.+)\'\;\s*$/i', $cfg_lines[$i], $m);
	if($r){
		define('DBS_PREFIX',$m[1]);
	}
	$m = [];
	$r = preg_match('/^export\s+DBS_LIST\=\((.+)\)\;\s*$/i', $cfg_lines[$i], $m);
	if($r){
		define('DBS_LIST', explode(' ', str_replace("'", '', $m[1])));
	}
}
if(! defined('DBS_PREFIX') || ! defined('DBS_LIST')){
	echo '读取数据库配置失败\n';
	return;
}
$DBN = $argv[1] ?? '';
if(empty($DBN)){
	echo "没有需要处理的数据库。\n";
	return;
}
// 建立临时目录
if(!file_exists(__DIR__ . '/.cache'))
	mkdir(__DIR__ . '/.cache',0755,true);
if(!file_exists(__DIR__ . '/.output'))
	mkdir(__DIR__ . '/.output',0755,true);
// 分析数据库
$mysql = new \Ziima\Data\MySQLConnection('mysql://root:123456@127.0.0.1:3306/mysql');
$engine = new \Ziima\Template\TemplateEngine(
		Z_ROOT . '/Lib',
		__DIR__ . '/.cache');

$db = DBS_PREFIX . $DBN;
echo "分析数据库 $db ...\n";
$schema = $mysql->schema($db);
echo "开始处理...\n";
file_put_contents(
			__DIR__ . '/.output/' . $schema->name . '.php',
			"<?php\n" .
			"/*\n" .
			" *\n" .
			" */\n" .
			"namespace " . $schema->name . ";\n"
		);
for($schema->tables->rewind();$schema->tables->valid();$schema->tables->next()){
	$table = $schema->tables->current();
	if($table === null)
		break;
	$tn = $table->name;
	if($table->flags->noEntity)
		continue;
	if($table->flags->isConfig)
		continue;
	//检查有没有 PRIMARY KEY/ AUTOINCREMENT / UNIQUE KEY
	$has_id = false;
	foreach($table->fields as $f){
		
	}
	echo "\t处理数据表 $tn ...\n";
	$code_entity = $engine->execute('Entity.tpl', [
				'Schema'	=> $schema,
				'Table'		=> $table
			]);
	//预处理SQL语句
	$table->SQLTemplate = [
		'Count'		=> 'SELECT COUNT(*) FROM `' . $schema->name . '`.`' . $table->name . '`',
		'Select'	=> '',
		'Update'	=> '',
		'Insert'	=> '',
		'Delete'	=> ''
	];

	$code_factory = $engine->execute('Factory.tpl', [
				'Schema'	=> $schema,
				'Table'		=> $table
			]);
	file_put_contents(
			__DIR__ . '/.output/' . $schema->name . '.php', 
			$code_entity,
			FILE_APPEND
	);
	file_put_contents(
			__DIR__ . '/.output/' . $schema->name . '.php',
			$code_factory,
			FILE_APPEND
	);
}
echo "\t生成存储过程调用器 ...\n";
$code_invoker = $engine->execute('Invoker.tpl', [
				'Schema'	=> $schema
			]);
file_put_contents(
		__DIR__ . '/.output/' . $schema->name . '.php',
		$code_invoker,
		FILE_APPEND
);
