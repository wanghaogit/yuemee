<?php
/**
 * HTTP请求封装
 */
class ZiimaHTTP
{

	/**
	 * 构造函数
	 */
	public function __construct() { }
	
	/**
	 * 微信API请求
	 * @param unknown $ApiUrl 请求的URL
	 * @param unknown $Param 请求参数（数组）
	 * @param number $TimeOut 超时时间
	 * @return string|NULL
	 */
	public function WeiXinAPI($ApiUrl, $Param, $TimeOut = 10) : ?string
	{
	    $opts['http']['method'] = 'POST';
	    $opts['http']['timeout'] = $TimeOut;
	    $opts['http']['content'] = $Param;
	    $opts['http']['header'] = "Content-Type: application/json" . "\r\n";
	    $opts['http']['header'] .= "X-Requested-With:XMLHttpRequest" . "\r\n";
	    $opts['http']['header'] .= "Content-length:" . strlen($Param) . "\r\n\r\n";
	    $Context = stream_context_create($opts);

	    return file_get_contents($ApiUrl, false, $Context);
	}

	/**
	 * 尚书API请求
	 * @param unknown $ApiUrl 请求的URL
	 * @param unknown $Param 请求参数（数组）
	 * @param number $TimeOut 超时时间
	 * @return string|NULL
	 */
	public function ZiimaAPI($ApiUrl, $Param, $TimeOut = 10) : ?string
	{
	    $Param['__timestamp'] = date("YmdHis");
	    $Param = json_encode($Param);

	    $opts['http']['method'] = 'POST';
	    $opts['http']['timeout'] = $TimeOut;
	    $opts['http']['content'] = $Param;
	    $opts['http']['header'] = "Content-Type: application/json" . "\r\n";
	    $opts['http']['header'] .= "X-Requested-With:XMLHttpRequest" . "\r\n";
	    $opts['http']['header'] .= "Content-length:" . strlen($Param) . "\r\n\r\n";
	    $Context = stream_context_create($opts);
	    return file_get_contents($ApiUrl, false, $Context);
	}
	
}
