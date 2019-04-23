<?php

session_start();
include_once Z_ROOT . '/Database.php';
include_once Z_ROOT . '/Data/MySQL.php';
include_once Z_SITE . '/../../_base/entity/yuemi_main.php';

/**
 * 微信页面处理器
 */
class MobileHandler extends \Ziima\MVC\Handler {

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
	 * 邀请者
	 * @var \yuemi_main\UserEntity
	 */
	protected $Invitor;

	/**
	 * 受邀者
	 * @var \yuemi_main\UserEntity
	 */
	protected $User;

	/**
	 * 微信
	 * @var \yuemi_main\UserWechatEntity
	 */
	protected $Wechat;

	/**
	 * 日志记录器
	 * @var \Ziima\Tracer
	 */
	protected $Tracer;

	/**
	 * 操作系统类型
	 * @var string
	 */
	protected $OsName = 'android';

	/**
	 * 微信调试信息
	 * @var type 
	 */
	protected $DebugInfo;

	// 构造函数
	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	public function __init() {
		// 实例化
		$this->Tracer = new \Ziima\Tracer('mobile'); // 日志
		$this->MySQL = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER); // MySql连接
		// 判断操作系统
		$UserAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
		if (strpos($UserAgent, 'iphone') || strpos($UserAgent, 'ipad')) {
			$this->OsName = 'ios';
		} elseif (strpos($UserAgent, 'android')) {
			$this->OsName = 'android';
		} else {
			$this->OsName = 'other';
		}
		$this->Context->Response->assign('OsName', $this->OsName);
		// 登录认证：拿到用户微信信息、用户基本信息
		$this->__auth();
		$this->Context->Response->assign('User', $this->User);
		$this->Context->Response->assign('Wechat', $this->Wechat);
	}

	/**
	 * 登录认证处理：拿到用户微信信息、用户基本信息
	 */
	public function __auth() 
	{
		$res = $this->__auth_GetWxInfo();
		$WxOpenId = $res->WeiXinOpenId;

		// 获取邀请者信息
		$invitor_id = 0;
		$InviteVIP = null;
		$vcode = $_GET['v'] ?? '';
		$vcode = trim($vcode);
		if (!empty($vcode)) {
			$InviteVIP = \yuemi_main\VipFactory::Instance()->loadOneByInviteCode($vcode);
		}
		if ($InviteVIP !== null && $InviteVIP->status > 0 && $InviteVIP->expire_time > Z_NOW) {
			$invitor_id = $InviteVIP->user_id;
		}

		// 昵称处理、获取用户微信信息(DB)
		if (!empty($res->nickname)) {
			$res->nickname = json_encode($res->nickname);
			$res->nickname = preg_replace("/(\\\ud[0-9a-f]{3})|(\\\ue[0-9a-f]{3})/i", "", $res->nickname);
			$res->nickname = json_decode($res->nickname);
		}
		$this->Wechat = \yuemi_main\UserWechatFactory::Instance()->loadByUnionId($res->unionid);

		// 新微信用户注册记录
		if ($this->Wechat === null) 
		{
			// 分析邀请码
			$invitor_param = '';
			$invitor_seed = 0;
			if (!empty($vcode)) 
			{
				if (strlen($vcode) > 8) {
					$vcode = substr($vcode, 0, 8);
					$invitor_param = substr($vcode, 8);
				}
				if (!empty($invitor_param)) {
					//TODO:分析归属总经理，查找直营团队，定位直营员工
					$invitor_seed = \Ziima\Radix::Base62()->decode($invitor_param);
					if ($invitor_seed <= 0) {
						$invitor_seed = 0;
					}
				}
			}
			// 微信登录(同时也是注册)
			$ret = \yuemi_main\ProcedureInvoker::Instance()->login_wechat_ex(
					$WxOpenId,
					$res->unionid,
					$res->nickname,
					$res->headimgurl,
					$res->sex,
					$invitor_id, $invitor_seed, $invitor_param,
					$this->Context->Runtime->ticket->ip);
			if ($ret === null) {
				return;
			}
			if ($ret->WechatId > 0) {
				$this->Wechat = \yuemi_main\UserWechatFactory::Instance()->load($ret->WechatId);
			}
		}
		// 更新用户OpenId
		if (isset($this->Wechat->id) && empty($this->Wechat->web_open_id)) {
			$this->MySQL->execute("UPDATE `yuemi_main`.`user_wechat` SET `web_open_id` = '%s' WHERE `id` = %d", $WxOpenId, $this->Wechat->id);
			$this->Wechat->web_open_id = $WxOpenId;
			if ($this->Wechat->user_id > 0) {
				$this->User = \yuemi_main\UserFactory::Instance()->load($this->Wechat->user_id);
			}
		}
		// 读取用户信息
		if (isset($this->Wechat->user_id) && $this->Wechat->user_id > 0 && empty($this->User)) {
			$this->User = \yuemi_main\UserFactory::Instance()->load($this->Wechat->user_id);
		}
		// 更新用户token，如果是空的话
		if (isset($this->User->token) && empty($this->User->token)) {
			$this->User->token = \Ziima\Zid::Default()->token();
			\yuemi_main\UserFactory::Instance()->update($this->User);
		}
		// 更新关系：当前用户还不是VIP，邀请者要必须是VIP
		if (isset($this->User->level_v) && $this->User->level_v < 1 && isset($InviteVIP->status) && $InviteVIP->status > 0 && $InviteVIP->expire_time > Z_NOW) 
		{
			$this->Wechat->invitor_id = $InviteVIP->user_id;
			\yuemi_main\UserWechatFactory::Instance()->update($this->Wechat);
		}
		// 更新用户邀请关系（同步user和user_wechat）
		if (isset($this->User->invitor_id) && isset($this->Wechat->invitor_id) && $this->User->invitor_id != $this->Wechat->invitor_id) {
			$this->User->invitor_id = $this->Wechat->invitor_id;
			\yuemi_main\UserFactory::Instance()->update($this->User);
		}
	}

	/**
	 * 获取用户微信信息
	 */
	public function __auth_GetWxInfo() 
	{
		// 从SESSION中获取用户微信信息
		$UpdateInitiative = $_GET['UpdateInitiative'] ?? "";
		if (isset($_SESSION['WeiXinInfoV4']) && !empty($_SESSION['WeiXinInfoV4']) && $UpdateInitiative != "Yes") {
			$WeiXinInfo = json_decode($_SESSION['WeiXinInfoV4']);
			if (isset($WeiXinInfo->time) && $WeiXinInfo->time > Z_NOW) {
				return $WeiXinInfo;
			}
		}
		// 跳转到微信授权页（如果无code参数的情况下）
		if (!isset($_GET['code']) || empty($_GET['code'])) {
			$this->__auth_GotoWeiXin();
		}
		// 获取用户微信基本信息
		$WeiXinInfo = array();
		$url = sprintf("https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code", WECHAT_APPID, WECHAT_SECRET, $_GET['code']);
		$src = file_get_contents($url);
		$this->DebugInfo[] = $src;
		if ($src === null || $src === false) {
			return $this->__auth_GotoWeiXin();
		}
		$res = json_decode($src);
		if ($res === null || $res === false) {
			return $this->__auth_GotoWeiXin();
		}
		if (!isset($res->openid) || !isset($res->access_token)) {
			return $this->__auth_GotoWeiXin();
		}
		$WeiXinOpenId = $res->openid;
		// 获取用户微信unionid信息
		$url = sprintf('https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN', $res->access_token, $res->openid);
		$src = file_get_contents($url);
		$this->DebugInfo[] = $src;
		if ($src === null || $src === false) {
			return $this->__auth_GotoWeiXin();
		}
		$res = json_decode($src);
		if ($res === null || $res === false) {
			return $this->__auth_GotoWeiXin();
		}
		if (!isset($res->unionid)) {
			return $this->__auth_GotoWeiXin();
		}
		// 组合数据
		$WeiXinInfo = $res;
		$WeiXinInfo->time = Z_NOW + 600;
		$WeiXinInfo->WeiXinOpenId = $WeiXinOpenId;
		$_SESSION['WeiXinInfoV4'] = json_encode($WeiXinInfo);
		return $WeiXinInfo;
	}

	/**
	 * 跳转到微信授权页（如果无code参数的情况下）
	 */
	public function __auth_GotoWeiXin() {
		$redirect_uri = "https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . "&" . rand(100000, 999999);
		$getcode = sprintf("https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect", WECHAT_APPID, urlencode($redirect_uri));
		header('location:' . $getcode);
	}

	public function __close() {
		
	}

	/**
	 * 转换微信授权页面URL
	 * @param string $handler	阅米微站的Handler
	 * @param string $action	阅米微站的Action
	 * @param array $params		其它参数
	 * @param string $state		状态值
	 * @return string
	 */
	protected function getAuthUrl(string $handler, string $action, array $params = null, string $state = null) {
		$surl = "https://a.yuemee.com/mobile.php?call={$handler}.{$action}&" . http_build_query($params);
		return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . WECHAT_APPID .
				"&redirect_uri=" . urlencode($surl) .
				"&response_type=code&scope=snsapi_userinfo&state=$state#wechat_redirect";
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

}
