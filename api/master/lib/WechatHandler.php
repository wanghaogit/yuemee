<?php
include_once Z_ROOT . '/Database.php';
include_once Z_ROOT . '/Data/MySQL.php';
include_once Z_SITE . '/../../_base/entity/yuemi_main.php';

/**
 * 微信回调处理器
 */
abstract class WechatHandler extends \Ziima\MVC\Wechat\Handler {

	/**
	 * 数据连接
	 * @var \Ziima\Data\DatabaseConnection
	 */
	protected $MySQL;

	/**
	 * Redis连接
	 * @var \Redis
	 */
	protected $Redis;

	/**
	 * MongoDB 连接
	 * @var \MongoDB\Driver\Manager
	 */
	protected $Mongo;

	/**
	 * 调用微信接口的AccessToken
	 * @var string
	 */
	protected $AccessToken;

	/**
	 * 微信记录
	 * @var \yuemi_main\UserWechatEntity
	 */
	protected $Wechat;

	/**
	 * 用户记录
	 * @var \yuemi_main\UserEntity
	 */
	protected $User;

	/**
	 * 日志记录器
	 * @var \Ziima\Tracer
	 */
	protected $Tracer;

	/**
	 * 微信返回的原始用户信息
	 * @var \stdClass
	 */
	protected $WxUserInfo;
	private $__apcu_key_access_token;

	function __construct() {
		parent::__construct();

		set_error_handler([$this, '__h_error']);
		set_exception_handler([$this, '__h_exception']);
		//日志
		$this->Tracer = new \Ziima\Tracer('api');

		// MySql连接
		$this->MySQL = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);

		$this->__apcu_key_access_token = 'wechat.' . WECHAT_APPID . '.access_token';
		$this->AccessToken = apcu_fetch($this->__apcu_key_access_token);
		if ($this->AccessToken === null || $this->AccessToken === false) {
			$rst = $this->invoke_wechat_api('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . WECHAT_APPID . '&secret=' . WECHAT_SECRET);
			if ($rst !== null && is_object($rst) && isset($rst->access_token) && isset($rst->expires_in)) {
				$this->AccessToken = $rst->access_token;
				if (apcu_exists($this->__apcu_key_access_token))
					apcu_delete($this->__apcu_key_access_token);
				apcu_add($this->__apcu_key_access_token, $this->AccessToken, intval($rst->expires_in) - 5);
			}
		}
	}

	public final function __h_error(int $code, string $message) {
		ob_clean();
		$this->error($message);
		ob_flush();
		exit();
	}

	public final function __h_exception(\Throwable $ex) {
		ob_clean();
		$this->error($ex->getMessage());
		ob_flush();
		exit();
	}

	/**
	 * 初始化
	 * @param \Ziima\MVC\Wechat\Request $request
	 */
	public final function __init(\Ziima\MVC\Wechat\Request $request) {
		if (empty($this->AccessToken)) {
			return $this->error('缺少AccessToken');
		}
		$ui = null;
		try {
			$ui = $this->getUserInfo($request->FromUserName);
		} catch (\Exception $ex) {
			return $this->error('getUserInfo失败：' . $ex->getMessage());
		}
		if ($ui === null) {
			return $this->error('getUserInfo失败: $ui = null');
		}if (!is_object($ui)) {
			return $this->error('getUserInfo失败: $ui is ' . gettype($ui));
		}
		if (!isset($ui->openid)) {
			return $this->error('getUserInfo失败: $ui 没有 openid');
		}
		if (!isset($ui->unionid)) {
			return $this->error('getUserInfo失败: $ui 没有 unionid');
		}
		if (!empty($ui->nickname)) {
			$ui->nickname = json_encode($ui->nickname);
			$ui->nickname = preg_replace("/(\\\ud[0-9a-f]{3})|(\\\ue[0-9a-f]{3})/i", "", $ui->nickname);
			$ui->nickname = json_decode($ui->nickname);
		}
		$this->WxUserInfo = $ui;
		$this->Wechat = \yuemi_main\UserWechatFactory::Instance()->loadByUnionId($ui->unionid);
		if ($this->Wechat === null) {
			// 立即注册Web微信
			$ret = \yuemi_main\ProcedureInvoker::Instance()->login_wechat_ex(
					$ui->openid,
					$ui->unionid,
					$ui->nickname,
					$ui->headimgurl,
					$ui->sex,
					0, 0, '',
					$this->Context->Runtime->ticket->ip);
			if ($ret === null)
				return;
			if ($ret->WechatId > 0)
				$this->Wechat = \yuemi_main\UserWechatFactory::Instance()->load($ret->WechatId);
			if ($ret->UserId > 0)
				$this->User = \yuemi_main\UserFactory::Instance()->load($ret->UserId);
		} else if ($this->Wechat->user_id > 0) {
			$this->User = \yuemi_main\UserFactory::Instance()->load($this->Wechat->user_id);
		}
	}

	private function invoke_wechat_api($url) {
		$ctx = stream_context_create([
			'http' => [
				'protocol_version' => '1.0',
				'method' => 'GET',
				'follow_location' => 1,
				'max_redirects' => 20,
				'timeout' => 0.5
			]
		]);
		$src = file_get_contents($url, false, $ctx);
		if (!preg_match('/200 OK/i', $http_response_header[0])) {
			return null;
		}
		$obj = json_decode($src);
		if (json_last_error() > 0) {
			return null;
		}
		if (!is_object($obj)) {
			return null;
		}
		if (isset($obj->errcode)) {
			if (intval($obj->errcode) == 40001) {
				$this->AccessToken = null;
				apcu_delete($this->__apcu_key_access_token);
			}
			throw new \Exception($obj->errmsg, $obj->errcode);
		}
		return $obj;
	}

	protected function getUserInfo(string $openId) {
		$obj = $this->invoke_wechat_api(sprintf(
						'https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN',
						$this->AccessToken,
						$openId));
		if ($obj === null)
			return null;
		if (isset($obj->errcode)) {
			throw new \Exception($obj->errmsg, $obj->errcode);
		}
		return $obj;
	}

	/**
	 * 转换微信授权页面URL
	 * @param string $handler	阅米微站的Handler
	 * @param string $action	阅米微站的Action
	 * @param array $params		其它参数
	 * @param string $state		状态值
	 * @return string
	 */
	protected function getAuthUrl(string $handler, string $action, array $params = null, string $state = null): string {
		$surl = "https://a.yuemee.com/mobile.php?call={$handler}.{$action}";
		if ($params && !empty($params)) {
			foreach ($params as $k => $v) {
				$surl .= "&$k=$v";
			}
		}
		return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . WECHAT_APPID .
				"&redirect_uri=" . urlencode($surl) .
				"&response_type=code&scope=snsapi_userinfo&state=$state#wechat_redirect";
	}

	protected function error(string $msg): \Ziima\MVC\Wechat\TextResponse {
		$r = new Ziima\MVC\Wechat\TextResponse();
		$r->Content = $msg;
		return $r;
	}

}
