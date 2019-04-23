<?php

include_once 'lib/ApiHandler.php';

/**
 * 消息客服接口
 */
class message_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 发送   用户在前，对方（平台客服）在后
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		content     string          内容
	 * @request		direction   string          方向（用户为civilian  系统方回复为yumee）
	 */
	public function send(\Ziima\MVC\REST\Request $request) {
		$this->Redis->select(9);
//		if ($request->body->direction == 'civilian') {
		$key = "system_{$this->User->id}_0";
//		} 
//		else {
//			$key = "system_{$request->body->otherid}_{$this->User->id}";
//		}
		$arr = $this->Redis->lgetrange($key, 0, -1);
		if (empty($arr)) {
			$num = 1;
		} else {
			$num = count($arr) + 1;
		}
		$value = "{$request->body->direction}," . time() . ",{$request->body->content}," . $num;

		$this->Redis->rpush($key, $value);

		$k = strpos($request->body->content, "退款");
		if ($k) {
			$num2 = $num + 1;
			$value2 = "yumee," . time() . ",您好，如果您需要取消未发货订单且该订单没有超出商家承诺时间，你可以打开【阅米】-【个人中心】-【待发货】，与商家协商退款，如果商家同意，您可以直接申请退款/退货。如果订单已经超过商家承诺的发货时间，您可以打开【阅米】- 【个人中心】-【退款/退货】，申请退款。," . $num2;
			$this->Redis->rpush($key, $value2);
		}

		$zl = strpos($request->body->content, "质量");
		if ($zl) {
			$num2 = $num + 1;
			$value3 = "yumee," . time() . ",亲，如果您收到的商品有问题，请您保留好相关凭证，及时联系阅米官方在线客服。为了让客服能够尽快的解决您的问题。简单明了地描述问题：【订单单号】+【问题描述】+【涉及到的图片】。," . $num2;
			$this->Redis->rpush($key, $value3);
		}

		$sf = strpos($request->body->content, "少发");
		if ($sf) {
			$num2 = $num + 1;
			$value4 = "yumee," . time() . ",亲，如果您收到的商品发现少了，为了保障您的利益，建议您保留好相关凭证，及时联系阅米官方在线客服。为了让客服能够尽快的解决您的问题。简单明了地描述问题：【订单单号】+【问题描述】+【涉及到的图片】," . $num2;
			$this->Redis->rpush($key, $value4);
		}

		$myfh = strpos($request->body->content, "没有发货");
		if ($myfh) {
			$num2 = $num + 1;
			$value5 = "yumee," . time() . ",您好，如果您需要取消未发货订单且该订单没有超出商家承诺时间，你可以打开【阅米】-【个人中心】-【待发货】，与商家协商退款，如果商家同意，您可以直接申请退款/退货。如果订单已经超过商家承诺的发货时间，您可以打开【阅米】- 【个人中心】-【退款/退货】，申请退款。," . $num2;
			$this->Redis->rpush($key, $value5);
		}

		$yjfh = strpos($request->body->content, "已经发货");
		if ($yjfh) {
			$num2 = $num + 1;
			$value6 = "yumee," . time() . ",您好，如果商品已经发货，请联系阅米在线客服，咨询具体的退款申请流程。为了让客服能够尽快的解决您的问题。简单明了地描述问题：【订单单号】+【问题描述】。," . $num2;
			$this->Redis->rpush($key, $value6);
		}

		$dzsh = strpos($request->body->content, "到账时间");
		if ($dzsh) {
			$num2 = $num + 1;
			$value7 = "yumee," . time() . ",您好，退款成功后，款项会原路返回您的支付账户。到账时间依据您的支付方式，微信零钱会在24小时返还；银行卡会在2-6天返还；支付宝会在24小时返还。," . $num2;
			$this->Redis->rpush($key, $value7);
		}

		$cdyf = strpos($request->body->content, "承担运费");
		if ($cdyf) {
			$num2 = $num + 1;
			$value8 = "yumee," . time() . ",您好，平台运费界定的原则为：谁的责任谁承担。如果您收到的商品有质量问题，建议您保留好相关凭证，咨询平台在线客服。为了让客服能够尽快的解决您的问题。简单明了地描述问题：【订单单号】+【问题描述】+【涉及到的图片】。," . $num2;
			$this->Redis->rpush($key, $value8);
		}

		$fch = strpos($request->body->content, "发错货");

		if ($fch) {
			$num2 = $num + 1;
			$value9 = "yumee," . time() . ",您好，平台运费界定的原则为：谁的责任谁承担。如果您收到的商品有发错货的问题，建议您保留好相关凭证，咨询平台在线客服。为了让客服能够尽快的解决您的问题。简单明了地描述问题：【订单单号】+【问题描述】+【涉及到的图片】。," . $num2;
			$this->Redis->rpush($key, $value9);
		}

		$dfk = strpos($request->body->content, "待付款");
		if ($dfk) {
			$num2 = $num + 1;
			$value10 = "yumee," . time() . ",您好，付款后订单显示待付款状态，一般是由于网络问题导致，建议您先退出阅米APP后，重新登陆查看是否正常。如果依旧为待付款状态，建议您查看支付收支明细是否有支出，如果已经支出，说明支付成功，如果未支付，建议联系人工客服处理。," . $num2;
			$this->Redis->rpush($key, $value10);
		}

		$rg = strpos($request->body->content, "人工");

		if ($rg) {
			$num2 = $num + 1;
			$value11 = "yumee," . time() . ",阅米官方客服：您好，小米很高兴为您服务。请简单描述您的问题，小米会尽快为您解答。," . $num2;
			$this->Redis->rpush($key, $value11);
		}

		return [
			'__code' => 'OK',
			'__message' => '发送成功',
			'Id' => $num
		];
	}

	/**
	 * 用户读取平台
	 * @param \Ziima\MVC\REST\Request $request
	 * $request		page		int		页码
	 * 
	 */

	public function take(\Ziima\MVC\REST\Request $request) {
		$this->Redis->select(9);
		$page = $request->body->page;
		$key = "system_{$this->User->id}_0";   //	 LRANGE mylist -3 2      LRANGE KEY_NAME START END
		$end = $page * 10 + 10;
		$start = $end - 10;

//		$arr = $this->Redis->LRANGE($key, $start, $end);
		$arr = $this->Redis->lgetrange($key, 0, -1);

		if (empty($arr)) {
			return [
				'__code' => 'OK',
				'__message' => '',
				'Message' => ''
			];
		} else {
			foreach ($arr as $v) {
				$res[] = explode(",", $v);
//				var_dump($res);die;
			}
			foreach ($res as $li) {
				$k = array("type", "time", "content", "id");
				$list[] = array_combine($k, $li);
			}
//			var_dump($res);die;
			return [
				'__code' => 'OK',
				'__message' => '',
				'Message' => $list
			];
		}
	}

	/**
	 * 用户端商品资讯列表 （key的前缀：spzx）
	 * @param \Ziima\MVC\REST\Request $request
	 * 
	 */

	public function messagelist(\Ziima\MVC\REST\Request $request) {
		$this->Redis->select(9);
		$uid = $this->User->id;
		$ksy = "spzx_" . $uid . "_*";

		//查找所有的key
		$keys = $this->Redis->keys($ksy);
//		$arr = explode("_", $keys);
		if (empty($keys)) {
			return [
				'__code' => 'OK',
				'__message' => '',
				'Message' => ''
			];
		}
		foreach ($keys as $key) {
			$arr = $this->Redis->lgetrange($key, 0, -1);
			$num = count($arr) - 1;
			$res[] = $this->Redis->LRANGE($key, $num, $num);
		}
		foreach ($res as $v) {
			foreach ($v as $key) {
				$ress[] = explode(",", $key);
			}
		}
	
		foreach ($ress as $li) {
			$k = array("type", "time", "content", "id", "sku_id", "sku_name","direction");
			$list[] = array_combine($k, $li);
		}
		return [
			'__code' => 'OK',
			'__message' => '',
			'Message' => $list
		];
	}

	/**
	 * 商品资讯
	 * @param \Ziima\MVC\REST\Request $request
	 * $request		sku_id		int			商品ID
	 * $request		content		string		内容
	 */

	public function sku_task(\Ziima\MVC\REST\Request $request) {
		$this->Redis->select(9);
		$sku_id = $request->body->sku_id;

		$key = "spzx_{$this->User->id}_{$sku_id}";
		$name = $this->MySQL->scalar("SELECT title FROM `yuemi_sale`.`sku` FROM id = " . $sku_id);

		$arr = $this->Redis->lgetrange($key, 0, -1);
		if (empty($arr)) {
			$num = 1;
		} else {
			$num = count($arr) + 1;
		}
		$value = "spzx," . time() . ",{$request->body->content}," . $num . "," . $sku_id . "," . $name . ",civilian";
		$b = $this->Redis->rpush($key, $value);
		return [
			'__code' => 'OK',
			'__message' => '发送成功',
			'Id' => $num
		];
	}

	/**
	 * 用户读取商品资讯客服信息
	 * @param \Ziima\MVC\REST\Request $request
	 * $request		page		int		页码
	 * $request		sku_id		int		skuid
	 * 
	 */

	public function spzx_post(\Ziima\MVC\REST\Request $request) {
		$this->Redis->select(9);
		$page = $request->body->page;
		$key = "spzx_{$this->User->id}_{$request->body->sku_id}";
		$end = $page * 10 + 10;
		$start = $end - 10;
		$arr = $this->Redis->LRANGE($key, $start, $end);
		if (empty($arr)) {
			return [
				'__code' => 'OK',
				'__message' => '',
				'Message' => ''
			];
		}
		foreach ($arr as $v) {
			$res[] = explode(",", $v);
		}
		foreach ($res as $li) {
			$k = array("type", "time", "content", "id", "sku_id", "sku_name", "direction");
			$list[] = array_combine($k, $li);
		}
		return [
			'__code' => 'OK',
			'__message' => '',
			'Message' => $list
		];
	}

	/**
	 * 技术售后发送   用户在前，对方（平台客服）在后
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		content     string          内容
	 * @request		direction   string          方向（用户为civilian  系统方回复为yumee）
	 */
	public function jssh_send(\Ziima\MVC\REST\Request $request) {
		$this->Redis->select(9);
//		if ($request->body->direction == 'civilian') {
		$key = "sh_{$this->User->id}_0";
//		} 
//		else {
//			$key = "system_{$request->body->otherid}_{$this->User->id}";
//		}
		$arr = $this->Redis->lgetrange($key, 0, -1);
		if (empty($arr)) {
			$num = 0;
		} else {
			$num = count($arr) + 1;
		}

		$value = "{$request->body->direction}," . time() . ",{$request->body->content}," . $num . ",civilian";

		$this->Redis->rpush($key, $value);

		return [
			'__code' => 'OK',
			'__message' => '发送成功',
			'Id' => $num
		];
	}

	/*
	 * 用户读取技术售后
	 * @param \Ziima\MVC\REST\Request $request
	 * $request		page		int		页码
	 * 
	 */

	public function jssh_take(\Ziima\MVC\REST\Request $request) {
		$this->Redis->select(9);
		$page = $request->body->page;
		$key = "sh_{$this->User->id}_0";   //	 LRANGE mylist -3 2      LRANGE KEY_NAME START END
		$end = $page * 10 + 10;
		$start = $end - 10;

//		$arr = $this->Redis->LRANGE($key, $start, $end);
		$arr = $this->Redis->lgetrange($key, 0, -1);
		if (empty($arr)) {
			return [
				'__code' => 'OK',
				'__message' => '',
				'Message' => ''
			];
		} else {
			foreach ($arr as $v) {
				$res[] = explode(",", $v);
			}

			foreach ($res as $li) {
				$k = array("type", "time", "content", "id", "direction");
				$list[] = array_combine($k, $li);
			}
			return [
				'__code' => 'OK',
				'__message' => '',
				'Message' => $list
			];
		}
	}

	/**
	 * 删除与系统客服对话
	 * @param \Ziima\MVC\REST\Request $request
	 * 
	 */

	public function del(\Ziima\MVC\REST\Request $request) {
		$this->Redis->select(9);
		$user = $this->User->id;
		$key = "system_{$user}_0";
		$this->Redis->del($key);
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 删除商品资讯
	 *  @param \Ziima\MVC\REST\Request $request
	 * $request		sku_id		int		skudi
	 */

	public function delkf(\Ziima\MVC\REST\Request $request) {
		$this->Redis->select(9);
		$key = "spzx_{$this->User->id}_{$request->body->sku_id}";
		$this->Redis->del($key);
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 删除与系统客服对话
	 * @param \Ziima\MVC\REST\Request $request
	 * 
	 */

	public function delsh(\Ziima\MVC\REST\Request $request) {
		$this->Redis->select(9);
		$user = $this->User->id;
		$key = "sh_{$user}_0";
		$this->Redis->del($key);
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 删除列表
	 */
	public function delmessage(\Ziima\MVC\REST\Request $request)
	{
		$key = $this->Redis->keys("spzx_".$this->User->id."_*");
		foreach($key as $v)
		{
			$this->Redis->del($v);
		}
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}
	
	/**
	 * 聊天获取sig
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		appid     string          appid
	 * @request		username     string          username
	 * @request		timestamp     string          timestamp
	 * @request		apptoken     string          apptoken
	 */
	public function get_sig(\Ziima\MVC\REST\Request $request){
		$appid = $request->body->appid;
		$username = $request->body->username;
		$timestamp = $request->body->timestamp;
		$apptoken = $request->body->apptoken;
		$str = md5($appid.$username.$timestamp.$apptoken);
		return [
			'sig' => $str
		];
	}
	
}
