<?php
/**
 * WeiXin支付操作封装
 */
class WeiXinPayment
{
	public $Data = null;

	/**
	 * 构造函数
	 */
	public function __construct() {

	}

	/**
	 * 创建订单
	 * @param string $ReUrl		回调Url
	 * @parem float $TotalFee	价格
	 * @param string $TradeNo	商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
	 * @param string $NonceStr	随机字符串，不长于32位，推荐随机数生成算法
	 * @param string $Title		商品或支付单简要描述
	 */
	public function OrderCreate($ReUrl, $TotalFee, $TradeNo, $NonceStr,  $Title = '阅米支付')
	{
		$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
		// 组合请求数据
		$this->Data = array();
		$this->Data['appid'] = WXAPP_APPID; // 应用Id
		$this->Data['mch_id'] = WXAPP_PARTNER_ID; // 商户号
		$this->Data['body'] = $Title; // 商品或支付单简要描述
		$this->Data['nonce_str'] = $NonceStr; // 随机字符串，不长于32位，推荐随机数生成算法
		$this->Data['notify_url'] = $ReUrl; // 接收微信支付异步通知回调url
		$this->Data['out_trade_no'] = $TradeNo; // 商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
		$this->Data['spbill_create_ip'] = $this->GetClientIP(); // APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP
		$this->Data['total_fee'] = round($TotalFee,2) * 100; // 订单总金额，只能为整数，详见支付金额
		$this->Data['trade_type'] = 'APP'; // 设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
		$this->Data['sign'] = $this->MakeSign($this->Data); // 签名
		// 向API发出请求，并返回结果
		$xml = $this->GetXml();
		$data = $this->Post($url, $xml);
		if ($data) {
			libxml_disable_entity_loader(true);
			return json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)), true); // 这么转换不能分开写，否则prepay_id节点会丢失，原因不明
		}
		else return null;
	}

	/**
	 * 创建订单 - 公众号支付(微信内)
	 * @param string $ReUrl		回调Url
	 * @parem float $TotalFee	价格
	 * @param string $TradeNo	商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
	 * @param string $NonceStr	随机字符串，不长于32位，推荐随机数生成算法
	 * @param string $Title		商品或支付单简要描述
	 */
	public function OrderCreateWeiXin($ReUrl, $TotalFee, $TradeNo, $NonceStr, $OpenId,  $Title = '阅米支付')
	{
		$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
		// 组合请求数据
		$this->Data = array();
		$this->Data['appid'] = WECHAT_APPID; // 应用Id
		$this->Data['mch_id'] = WXOA_PARTNER_ID; // 商户号
		$this->Data['body'] = $Title; // 商品或支付单简要描述
		$this->Data['nonce_str'] = $NonceStr; // 随机字符串，不长于32位，推荐随机数生成算法
		$this->Data['notify_url'] = $ReUrl; // 接收微信支付异步通知回调url
		$this->Data['out_trade_no'] = $TradeNo; // 商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
		$this->Data['spbill_create_ip'] = $this->GetClientIP(); // APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP
		$this->Data['total_fee'] = round($TotalFee,2) * 100; // 订单总金额，只能为整数，详见支付金额
		$this->Data['trade_type'] = 'JSAPI'; // 设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
		$this->Data['scene_info'] = '{"store_info":{"id":"WX01", "name":"阅米"}}';
		$this->Data['openid'] = $OpenId; // 用户OpenId
		$this->Data['sign'] = $this->MakeSign($this->Data); // 签名
		// 向API发出请求，并返回结果
		$xml = $this->GetXml();
		$data = $this->Post($url, $xml);
		if ($data) {
			libxml_disable_entity_loader(true);
			$ReData = json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)), true); // 这么转换不能分开写，否则prepay_id节点会丢失，原因不明
			$ReData['sign'] = $this->Data['sign'];
			return $ReData;
		}
		else return null;
	}

	/**
	 * 创建订单 - H5支付
	 * @param string $ReUrl		回调Url
	 * @parem float $TotalFee	价格，单位：元
	 * @param string $TradeNo	商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
	 * @param string $NonceStr	随机字符串，不长于32位，推荐随机数生成算法
	 * @param string $Title		商品或支付单简要描述
	 */
	public function OrderCreateH5($ReUrl, $TotalFee, $TradeNo, $NonceStr,  $Title = '阅米支付')
	{
		$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
		// 组合请求数据
		$this->Data = array();
		$this->Data['appid'] = WECHAT_APPID; // 应用Id
		$this->Data['mch_id'] = WXOA_PARTNER_ID; // 商户号
		$this->Data['body'] = $Title; // 商品或支付单简要描述
		$this->Data['nonce_str'] = $NonceStr; // 随机字符串，不长于32位，推荐随机数生成算法
		$this->Data['notify_url'] = $ReUrl; // 接收微信支付异步通知回调url
		$this->Data['out_trade_no'] = $TradeNo; // 商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
		$this->Data['spbill_create_ip'] = $this->GetClientIP(); // APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP
		$this->Data['total_fee'] = round($TotalFee,2) * 100; // 订单总金额，只能为整数，详见支付金额
		$this->Data['trade_type'] = 'MWEB'; // 设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
		$this->Data['scene_info'] = '{"h5_info":{"type":"android", "wap_url":"http://a6.yuemilife.com/", "wap_name":"阅米OA"}}';
		$this->Data['sign'] = $this->MakeSign($this->Data); // 签名
		// 向API发出请求，并返回结果
		$xml = $this->GetXml();
		$data = $this->Post($url, $xml);
		if ($data) {
			libxml_disable_entity_loader(true);
			return json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)), true); // 这么转换不能分开写，否则prepay_id节点会丢失，原因不明
		}
		else return null;
	}

	/**
	 * 下载对账单
	 * @param type $BillDate	对账单日期，如：20140603
	 * 文档 https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_6&index=8
	 */
	public function DownloadBill($BillDate)
	{
		$this->Data = array();
		$this->Data['appid'] = WXAPP_APPID; // 应用Id
		$this->Data['mch_id'] = WXAPP_PARTNER_ID; // 商户号
		$this->Data['nonce_str'] = time() + rand(10000000, 99999999); // 随机字符串，不长于32位。推荐随机数生成算法
		$this->Data['bill_date'] = $BillDate; // 下载对账单的日期，格式：20140603
		$this->Data['bill_type'] = 'SUCCESS'; // ALL,返回当日所有订单信息,默认值；SUCCESS,返回当日成功支付的订单
		$this->Data['sign'] = $this->MakeSign($this->Data); // 签名
		$xml = $this->GetXml();
		$url = "https://api.mch.weixin.qq.com/pay/downloadbill";
		$data = $this->Post($url, $xml);

		$DataList = null;
		$DataCount = null;
		$TempArr = explode("\n", $data);
		foreach ($TempArr AS $Key => $Val)
		{
			$arr = explode(',', $Val);
			// 处理前后特殊符号
			if (is_array($arr)) {
				foreach ($arr AS $k => $v) {
					$arr[$k] = trim($v, '`');
				}
			}
			// 当天统计数据
			if (is_array($arr) && count($arr) == 5) {
				$DataCount[] = $arr;
				continue;
			}
			// 帐单列表
			if (!is_array($arr) || count($arr) < 18) {
				continue;
			}
			$DataList[] = $arr;
		}
		$Data['count'] = $DataCount;
		$Data['list'] = $DataList;
		return $Data;
	}

	/**
	 * 查询订单信息
	 * @param type $TransactionId	微信的订单号，优先使用
	 * @param type $OutTradeNo		商户系统内部的订单号，当没提供transaction_id时需要传这个
	 * 文档 https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_2&index=4
	 */
	public function GetOrderInfo($TransactionId = '', $OutTradeNo = '')
	{
		$this->Data = array();
		$this->Data['appid'] = WXAPP_APPID; // 应用Id
		$this->Data['mch_id'] = WXAPP_PARTNER_ID; // 商户号
		$this->Data['transaction_id'] = $TransactionId; // 微信的订单号，优先使用
		$this->Data['out_trade_no'] = $OutTradeNo; // 商户系统内部的订单号，当没提供transaction_id时需要传这个
		$this->Data['nonce_str'] = time() + rand(10000000, 99999999); // 随机字符串，不长于32位。推荐随机数生成算法
		$this->Data['sign'] = $this->MakeSign($this->Data); // 签名

		$xml = $this->GetXml();
		$url = "https://api.mch.weixin.qq.com/pay/orderquery";
		$data = $this->Post($url, $xml);
		if ($data) {
			libxml_disable_entity_loader(true);
			return json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)), true); // 这么转换不能分开写，否则prepay_id节点会丢失，原因不明
		}
		else return null;
	}

	/**
	 * POST 请求
	 * @param type $url
	 * @param type $xml
	 * @return type
	 */
	public function Post($url, $xml)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 设置超时
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2); // 严格校验
		curl_setopt($ch, CURLOPT_HEADER, FALSE); // 设置header
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // 要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_POST, TRUE); // post提交方式
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml); // post提交方式
		$data = curl_exec($ch);
		@curl_close($ch);
		return empty($data) ? null : trim($data);
	}

	/**
	 * 生成微信签名
	 * @return bool
	 */
	public function MakeSign($Data)
	{
		ksort($Data); // 排序
		// 合成字符串、在后加入KEY
		$SignStr = "";
		foreach ($Data AS $k => $v) {
			if ($k != "sign" && $v != "" && !is_array($v)) {
				$SignStr .= $k . "=" . $v . "&";
			}
		}
		$SignStr = trim($SignStr, "&");
		$SignStr = $SignStr . "&key=" . WXAPP_PARTNER_API_KEY;
		// MD5加密、转换大写、返回
		return strtoupper(md5($SignStr));
	}

	/**
	 * 数组转XML
	 * @return string
	 */
	private function GetXml()
	{
		if (!is_array($this->Data) || count($this->Data) <= 0) return null;
    	$xml = "<xml>";
    	foreach ($this->Data AS $key => $val) {
    		if (is_numeric($val)) {
    			$xml.="<".$key.">".$val."</".$key.">";
    		} else {
    			$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
    		}
        }
        $xml.="</xml>";
        return $xml;
	}

	/**
	 * 获取客户端IP
	 */
	private function GetClientIP()
	{
		$IP = null;
		if (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
			$IP = $_SERVER['REMOTE_ADDR'];
		} elseif (getenv("REMOTE_ADDR")) {
			$IP = getenv("REMOTE_ADDR");
		}
		return $IP;
	}
}
