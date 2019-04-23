<?php

/**
 * 内购基础函数封装
 */
include dirname(__FILE__) . '/../../../../_base/config.php';
include Z_ROOT . '/Database.php';
include Z_ROOT . '/Data/MySQL.php';
include dirname(__FILE__) . '/../../../../_base/entity/yuemi_main.php';
include dirname(__FILE__) . '/../../../../_base/entity/yuemi_sale.php';

if(!defined('NG_CLIENTID'))	define('NG_CLIENTID'			, '242019a522a5173d4368003e8a545c50');
if(!defined('NG_SECRET'))	define('NG_SECRET'				, '3936ba3c3a2e621584ecdf6348f14c48');
if(!defined('NG_URL_BASE'))	define('NG_URL_BASE'			, 'https://openapi.neigou.com');
if(!defined('NG_OAUTH'))	define('NG_OAUTH'				, NG_URL_BASE . '/Authorize/V1/OAuth2/Platform/token');

define('NG_URL_LIST', NG_URL_BASE . '/ChannelInterop/V2/Gallywix/Goods/queryGoodBns');
define('NG_URL_ITEM', NG_URL_BASE . '/ChannelInterop/V2/Gallywix/Goods/queryGoodInfo');
define('NG_URL_PRICE', NG_URL_BASE . '/ChannelInterop/V2/Gallywix/Goods/querySkuStockPrice');

define("SUPPLIER_ID", 2); // 外部供应商ID，2=内购，3=贡云
define("PROFIT_SMALL", 10); // 最低利润率，10表示不低于10%

function _LOCK(string $key) {
	$f = __DIR__ . DIRECTORY_SEPARATOR . '../../data/neigou-' . $key . '.lock';
	if (!file_exists($f)) {
		file_put_contents($f, time());
		return true;
	}
	$c = file_get_contents($f);
	$t = intval($c);
	if (time() - $t > 3600) {
		unlink($f);
		file_put_contents($f, time());
		return true;
	}
	return false;
}

function _UNLOCK(string $key) {
	$f = __DIR__ . DIRECTORY_SEPARATOR . '../../data/neigou-' . $key . '.lock';
	if (file_exists($f)) {
		unlink($f);
	}
}

/**
 * 输出错误信息
 * @param string $fmt
 * @param type $args
 */
function _E(string $fmt, ...$args) {
	if (empty($args))
		$msg = '[' . date('Y-m-d H:i:s') . '] ERROR,' . $fmt . "\n";
	else
		$msg = '[' . date('Y-m-d H:i:s') . '] ERROR,' . vsprintf($fmt, $args) . "\n";
	echo $msg;
	file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . '../../data/neigou.log', $msg, FILE_APPEND);
}

function _T(string $fmt, ...$args) {
	if (empty($args))
		$msg = '[' . date('Y-m-d H:i:s') . '] DEBUG,' . $fmt . "\n";
	else
		$msg = '[' . date('Y-m-d H:i:s') . '] DEBUG,' . vsprintf($fmt, $args) . "\n";
	echo $msg;
	file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . '../../data/neigou.log', $msg, FILE_APPEND);
}

function _LOG(string $fmt, ...$args) {
	if (empty($args))
		$msg = '[' . date('Y-m-d H:i:s') . '] DEBUG,' . $fmt . "\n";
	else
		$msg = '[' . date('Y-m-d H:i:s') . '] DEBUG,' . vsprintf($fmt, $args) . "\n";
	file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . '../../data/neigou.log', $msg, FILE_APPEND);
}

function _parse_csv(string $file, \Closure $each, $ctx) {
	$f = fopen($file, 'r');
	$buf = fread($f, 32768);
	$src = '';
	$lno = 0;
	while ($buf !== false && !empty($buf)) {
		$src .= $buf;
		$len = strlen($src);
		$p = 0;
		for ($i = 0; $i < $len; $i ++) {
			if ($src[$i] === "\n") {
				$line = substr($src, $p, $i - $p);
				$p = $i + 1;
				$lno ++;
				$each->call($ctx, $lno, $line, $ctx);
			}
		}
		$src = substr($src, $p);
		$buf = fread($f, 32768);
	}
	if (!empty($src)) {
		$p = 0;
		$len = strlen($src);
		for ($i = 0; $i < $len; $i ++) {
			if ($src[$i] === "\n") {
				$line = substr($src, $p, $i - $p);
				$p = $i + 1;
				$lno ++;
				$each->call($ctx, $lno, $line, $ctx);
			}
		}
	}
	fclose($f);
}

$_ACCESS_TOKEN_VALUE = null;
$_ACCESS_TOKEN_EXPIRE = 0;

/**
 * 获取 access_token
 * @return json string
 */
function get_access_token() {
	global $_ACCESS_TOKEN_VALUE;
	global $_ACCESS_TOKEN_EXPIRE;
	if (empty($_ACCESS_TOKEN_VALUE) || $_ACCESS_TOKEN_EXPIRE < time()) {
		$txt = file_get_contents(NG_OAUTH, false, stream_context_create([
			'http' => [
				'method' => 'POST',
				'header' => 'Content-type:application/x-www-form-urlencoded',
				'content' => http_build_query([
					'data' => json_encode([
						'client_id' => NG_CLIENTID,
						'client_secret' => NG_SECRET,
						'grant_type' => 'client_credentials',
					])
				])
		]]));
		if ($txt === null || $txt === false || empty($txt)) {
			_LOG("获取内购 access_token 失败#1");
			return null;
		}
		$arr = json_decode($txt, true);
		if (isset($arr['ErrorId']) && $arr['ErrorId'] > 10000) {
			_LOG('获取内购 access_token 失败，[' . $arr['ErrorId'] . ']：' . $arr['ErrorMsg']);
			return null;
		}
		if (!isset($arr['Data']['access_token'])) {
			_LOG("获取内购 access_token 失败#2");
			return null;
		}
		$_ACCESS_TOKEN_VALUE = $arr['Data']['access_token'];
		$_ACCESS_TOKEN_EXPIRE = time() + intval($arr['Data']['expires_in']) - 5;
	}
	return $_ACCESS_TOKEN_VALUE;
}

/**
 * 获取商品BN列表
 * @param int $page_num		第几页
 * @param int $page_size	每页多少条
 * @param int $hours		过去几个小时内有更新
 * @return type
 */
function get_goods_bn_list(int $page_num = 1, int $page_size = 100, int $hours = 1) {
	// 向 API 请求数据
	$txt = file_get_contents(NG_URL_LIST, false, stream_context_create([
		'http' => [
			'method' => 'POST',
			'header' => "Content-type:application/x-www-form-urlencoded\r\n" .
			"AUTHORIZATION: Bearer " . get_access_token(),
			'content' => http_build_query([
				'data' => json_encode([
					'page' => $page_num,
					'page_size' => $page_size,
					'min_last_modify' => Z_NOW - $hours * 3600,
					'max_last_modify' => Z_NOW
				])
			])
		]
	]));
	if ($txt === null || $txt === false || empty($txt)) {
		_E("获取商品列表失败 #1");
		return null;
	}
	$arr = json_decode($txt, true);
	if (isset($arr['ErrorId']) && $arr['ErrorId'] > 10000) {
		_E('获取商品列表失败，[' . $arr['ErrorId'] . ']：' . $arr['ErrorMsg']);
		return null;
	}
	if (!isset($arr['Data']['goodBns'])) {
		_E("获取商品列表失败 #2");
		return null;
	}
	return $arr;
}

/**
 * 获取价格信息
 * @param type $Bns 多个bn用逗号(,)隔开
 * @return type
 */
function get_goods_price($Bns) {
	$ctx = stream_context_create([
		'http' => [
			'method' => 'POST',
			'header' => "Content-type:application/x-www-form-urlencoded\r\n" .
			"AUTHORIZATION: Bearer " . get_access_token(),
			'content' => http_build_query([
				'data' => json_encode([
					'sku_bns' => $Bns
	])])]]);
	$txt = file_get_contents(NG_URL_BASE . "/ChannelInterop/V2/Gallywix/Goods/querySkuStockPrice", false, $ctx);
	if (empty($txt)) {
		_E("抓取商品价格信息失败 #1");
		return null;
	}
	$arr = json_decode($txt, true);
	if (isset($arr['ErrorId']) && $arr['ErrorId'] > 10000) {
		_E('抓取商品价格信息失败，[' . $arr['ErrorId'] . ']：' . $arr['ErrorMsg']);
		return null;
	}
	if (!isset($arr['Data']['list'])) {
		_E("抓取商品价格信息失败 #2");
		return null;
	}
	return $arr;
}

/**
 * 获取商品信息
 * @param type $bn
 * @return type
 */
function get_goods_info($bn) {
	$txt = file_get_contents(NG_URL_ITEM, false, stream_context_create([
		'http' => [
			'method' => 'POST',
			'header' => "Content-type:application/x-www-form-urlencoded\r\n" .
			"AUTHORIZATION: Bearer " . get_access_token(),
			'content' => http_build_query([
				'data' => json_encode([
					'good_bn' => $bn
	])])]]));
	if ($txt === null || $txt === false || empty($txt)) {
		_E('获取商品' . $bn . '详情失败 #1');
		return null;
	}
	$arr = json_decode($txt, true);
	if (isset($arr['ErrorId']) && $arr['ErrorId'] > 10000) {
		_E('获取商品' . $bn . '详情失败，[' . $arr['ErrorId'] . ']：' . $arr['ErrorMsg']);
		return null;
	}
	if (!isset($arr['Data']['info'])) {
		var_dump($arr);
		_E('获取商品' . $bn . '详情失败 #2');
		return null;
	}
	return $arr;
}
