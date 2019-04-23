<?php

include_once Z_ROOT . '/Database.php';
include_once Z_ROOT . '/Data/MySQL.php';
include_once Z_SITE . '/../../_base/redis.php';
include_once Z_SITE . '/../../_base/entity/yuemi_main.php';
include_once Z_SITE . '/../../_base/entity/yuemi_sale.php';

/**
 * 后台处理器
 * @auth
 */
class AdminHandler extends \Ziima\MVC\Handler {

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
	 * 缓存管理器
	 * @var CacheManager
	 */
	protected $Cacher;

	/**
	 * 当前前端用户
	 * @var \yuemi_main\UserEntity
	 */
	protected $User = null;

	/**
	 * 当前管理员
	 * @var \yuemi_main\RbacAdminEntity
	 */
	protected $Admin = null;

	/**
	 * 当前管理员角色
	 * @var \yuemi_main\RbacRoleEntity
	 */
	protected $Role = null;

	// 构造函数
	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
		// 浏览器判断，限定Google浏览器访问
		$HttpUserAgent = strtolower($_SERVER["HTTP_USER_AGENT"]);
		if ($this->Context->Runtime->ticket->handler != 'visit_hint') {
			// 是否Google浏览器
			if (!strpos($HttpUserAgent, "chrome") || strpos($HttpUserAgent, "edge")) {
				throw new \Ziima\MVC\Redirector('/?call=visit_hint.index');
			}
			// 版本号
			$TempArr = explode("Chrome/", $_SERVER["HTTP_USER_AGENT"]);
			$TempArr = explode(".", $TempArr[1]);
			$TempArr = intval($TempArr[0]);
			if ($TempArr < 64) {
				// throw new \Ziima\MVC\Redirector('/?call=visit_hint.index&type=1');
			}
		}
		// 模板传值
		$this->MySQL = new \Ziima\Data\MySQLConnection(MYSQL_WRITER, MYSQL_READER);
		$this->Redis = new \Redis();
		$this->Redis->connect(REDIS_HOST, REDIS_PORT);
		if (!empty(REDIS_AUTH)) {
			$this->Redis->auth(REDIS_AUTH);
		}
		$this->Cacher = new CacheManager($this->MySQL, $this->Redis);
	}

	// 认证
	public function __auth() {
		if ($this->Context->Runtime->ticket->handler === 'default' && in_array($this->Context->Runtime->ticket->action, ['login', 'quit'])) {
			return;
		}
		$uid = $_SESSION['UserId'] ?? 0;
		$aid = $_SESSION['AdminId'] ?? 0;

		if ($uid < 1 || $aid < 1) {
			throw new \Ziima\MVC\Redirector('/index.php?call=default.login');
		}
		$this->User = \yuemi_main\UserFactory::Instance()->load($uid);
		$this->Admin = \yuemi_main\RbacAdminFactory::Instance()->load($aid);
		if ($this->Admin !== null) {
			$this->Role = \yuemi_main\RbacRoleFactory::Instance()->load($this->Admin->role_id);
		}
		$this->Context->Response->assign('User', $this->User);
		$this->Context->Response->assign('Admin', $this->Admin);
		$this->Context->Response->assign('Role', $this->Role);
		$this->Context->Response->assign('Cacher', $this->Cacher);

		
		
//		********************************************超级重要的分割线********************************************************************
		
		//id为1的全部可以访问
		//别删!!!!!留着救命
		if ($this->User->id == 1) {
			return;
		}
//		********************************************千万千万千万别删********************************************************************
		
		
		//1、权限检查的必要步骤：用户是否存在
		if ($this->Admin === null) {
			throw new \Ziima\MVC\Redirector('/index.php?call=default.login');
		}
		if ($this->Admin->status == 0) {
			throw new \Ziima\MVC\Redirector('/index.php?call=default.login');
		}
		//2、加载缓存，降低服务器压力
		$this->Cacher->loadRbacRule();
		$this->Cacher->loadRbacTarget();
		//3、分次查询，先查管理目标，用PHP循环代替数据库查询
		$target_id = 0;

		if ($this->Cacher->target) {
			foreach ($this->Cacher->target as $id => $target) {
				if ($target['mvc_handler'] == $this->Context->Runtime->ticket->handler && $target['mvc_action'] == $this->Context->Runtime->ticket->action) {
					$target_id = $id;
					break;
				}
			}
		}
		//4、防止老出现错误页面
		if ($target_id == 0) {
			//实际是没有配置权限的
			return;
		}
		if ($target_id == 1) {
			//首页都可以进
			return;
		}
		
		//5、用多次查询和PHP循环代替LEFT JOIN
		$acl_found = false;
		$acl_view = 0;
		$acl_edit = 0;
		$acl_delete = 0;
		
		foreach ($this->Cacher->rbac_rule as $id => $rule) {
			if ($rule['target_id'] == $target_id && $rule['role_id'] == $this->Admin->role_id) {
				$acl_found = true;
				$acl_view = $rule['acl_view'];
				$acl_edit = $rule['acl_edit'];
				$acl_delete = $rule['acl_delete'];
				break;
			}
		}
		//6、最终检查权限，权限合成算法，继续给自己留后门
		if ($acl_found) {
			if ($acl_view == 2) {
				//拒绝
				throw new \Ziima\MVC\Redirector('/index.php?call=default.error');
			} elseif ($acl_view == 0) {
				//继承
				$target_pid = 0;
				$tt = $this->get_pid($target_id);

				$acl_view = 0;
				$acl_edit = 0;
				$acl_delete = 0;
				foreach ($this->Cacher->rbac_rule as $id => $rule) {
					if ($rule['target_id'] == $tt && $rule['role_id'] == $this->Admin->role_id) {
						$acl_found = true;
						$acl_view = $rule['acl_view'];
						$acl_edit = $rule['acl_edit'];
						$acl_delete = $rule['acl_delete'];
						break;
					}
				}
				if ($acl_view == 2) {
					throw new \Ziima\MVC\Redirector('/index.php?call=default.error');
				}
			} elseif($acl_view == 1){
				//自身有访问权限
				return;
			}
		} else {
			//自身没配置权限，看看父级有没有配置
			$row = $this->MySQL->row("SELECT `parent_id` FROM `yuemi_main`.`rbac_role` WHERE `id` = {$this->Admin->role_id}");
			if (!empty($row) && $row['parent_id'] !== 0) {
				//递归查询有设置权限的父级
				$acl_view = $this->get_view($this->Admin->role_id, $target_id);
				
				if ($acl_view == 2) {
					throw new \Ziima\MVC\Redirector('/index.php?call=default.error');
				} elseif ($acl_view == 0) {
					//继承
					$target_pid = 0;
					$tt = $this->get_pid($target_id);
					
					$acl_view = 0;
					$acl_edit = 0;
					$acl_delete = 0;
					foreach ($this->Cacher->rbac_rule as $id => $rule) {
						if ($rule['target_id'] == $tt && $rule['role_id'] == $this->Admin->role_id) {
							$acl_found = true;
							$acl_view = $rule['acl_view'];
							$acl_edit = $rule['acl_edit'];
							$acl_delete = $rule['acl_delete'];
							break;
						}
					}
					if ($acl_view == 2) {
						//父级群没权限
						throw new \Ziima\MVC\Redirector('/index.php?call=default.error');
					}
				} elseif ($acl_view == 1) {
					//父级群有访问权限1
					return;
				} else {
					//父级群没有配置权限
					throw new \Ziima\MVC\Redirector('/index.php?call=default.error');
				}
			} else {
				//没有父级，自身也没有配置权限，先没有权限
				throw new \Ziima\MVC\Redirector('/index.php?call=default.error');
			}
		}
	}

	//获取顶级父target
	private function get_pid($id) {
		foreach ($this->Cacher->target as $k => $target) {
			if ($target['id'] == $id) {
				$target_pid = $target['parent_id'];
				if ($target_pid == 0) {
					return $id;
				} else {
					return $this->get_pid($target_pid);
				}
				break;
			}
		}
	}

	//获取父级查看权限
	private function get_view($role_id, $target_id) {
		if ($role_id == 0) {
			return -1;
		}
		$row = $this->MySQL->row("SELECT `parent_id` FROM `yuemi_main`.`rbac_role` WHERE `id` = {$role_id}");
		$pid = $row['parent_id'];
		
		if ($pid != 0) {
			//还是子类
			$acl_view = -1;
			foreach ($this->Cacher->rbac_rule as $id => $rule) {
				if ($rule['target_id'] == $target_id && $rule['role_id'] == $pid) {
					$acl_view = $rule['acl_view'];
					break;
				}
			}
			if ($acl_view == -1) {
				return $this->get_view($pid, $target_id);
			} else {
				return $acl_view;
			}
		} else {
			//顶级父类
			$acl_view = -1;
			foreach ($this->Cacher->rbac_rule as $id => $rule) {
				if ($rule['target_id'] == $target_id && $rule['role_id'] == $pid) {
					$acl_view = $rule['acl_view'];
					break;
				}
			}
			return $acl_view;
		}
	}

	public function __close() {
		
	}

	public function __init() {
		
	}

}
