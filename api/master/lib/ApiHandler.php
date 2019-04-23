<?php
include_once Z_ROOT . '/Database.php';
include_once Z_ROOT . '/Data/MySQL.php';
include_once Z_SITE . '/../../_base/entity/yuemi_main.php';
include_once Z_SITE . '/../../_base/redis.php';

class ApiHandler extends \Ziima\MVC\Handler {

	/**
	 * 数据连接
	 * @var \Ziima\Data\MySQLConnection
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
	 * 当前用户
	 * @var \yuemi_main\UserEntity
	 */
	protected $User;

	/**
	 * 当前微信登陆
	 * @var \yuemi_main\UserWechatEntity
	 */
	protected $Wechat;

	/**
	 * 当前设备
	 * @var \yuemi_main\DeviceEntity
	 */
	protected $Device;

	/**
	 * 当前调用的Applet
	 * @var \yuemi_main\AppletEntity
	 */
	protected $Applet;
	/**
	 * 日志记录器
	 * @var \Ziima\Tracer
	 */
	protected $Tracer;
	
	/**
	 * 
	 * @var type 
	 */
	protected $Cacher;

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
		// 日志
		$this->Tracer = new \Ziima\Tracer('api');
		// MySql连接
		$this->MySQL = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);
		$this->Redis = new \Redis();
		$this->Redis->connect(REDIS_HOST, REDIS_PORT);
		if(!empty(REDIS_AUTH)){
			$this->Redis->auth(REDIS_AUTH);
		}
		$this->Cacher = new CacheManager($this->MySQL,$this->Redis);
	}

	public function __auth() {
		if ($this->Context->Runtime->ticket->handler === 'oa' && in_array($this->Context->Runtime->ticket->action, ['make_cheif_card'])) {
			return;
		}
		if ($this->Context->Runtime->ticket->handler === 'user' && in_array($this->Context->Runtime->ticket->action, ['spread'])) {
			return;
		}
		if ($this->User === null) {
			throw new \Ziima\MVC\REST\Exception('E_AUTH', '尚未登录');
		}
		if ($this->User->level_u == 0) {
			throw new \Ziima\MVC\REST\Exception('E_AUTH', '无效身份');
		}
	}

	public function __close() {

	}

	public function __init() {
		$this->Applet = \yuemi_main\AppletFactory::Instance()->loadByToken($this->Context->Request->__applet_token);
		if ($this->Applet === null) {
			throw new \Ziima\MVC\REST\Exception('E_APPLET', '应用Token无效');
		}
		if ($this->Applet->status != 2) {
			throw new \Ziima\MVC\REST\Exception('E_APPLET_STATUS', '应用状态无效');
		}
		$this->Device = \yuemi_main\DeviceFactory::Instance()->loadByUdid($this->Context->Request->__udid);
		if (!empty($this->Context->Request->__access_token)) {
			$this->User = \yuemi_main\UserFactory::Instance()->loadOneByToken($this->Context->Request->__access_token);
			if ($this->User !== null) {
				$this->Wechat = \yuemi_main\UserWechatFactory::Instance()->loadOneByUserId($this->User->id);
			}
		}
	}

}
