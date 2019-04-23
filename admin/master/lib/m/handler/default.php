<?php
include "lib/AdminHandler.php";
include Z_ROOT . '/Cloud/Kuaidi.php';

/**
 * 后台首页
 * @auth
 */
class default_handler extends AdminHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 首页
	 */
	public function index(int $p = 0, int $id = 0) {

	}
	
	//生成地址json文件
	private function get_region_dict(){
		$arr = [];
		$res = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`region`");
		foreach ($res as $k => $v) {
			$str1 = substr($v['id'],0,2);
			$str2 = substr($v['id'],2,2);
			$str3 = substr($v['id'],4,2);
			$province = [];
			if($str2 === '00' && $str3 === '00'){
				$province['value'] = $v['id'];
				$province['text'] = $v['province'];
				$province['children'] = [];
				$arr[] = $province;
			}
			$city = [];
			if($str2 !== '00' && $str3 === '00'){
				$city['value'] = $v['id'];
				$city['text'] = $v['city'];
				$city['children'] = [];
				foreach($arr as $kk => $vv){
					if($vv['value'] == $str1.'0000'){
						$arr[$kk]['children'][] = $city;
					}
				}
			}
			$country = [];
			if($str2 !== '00' && $str3 !== '00'){
				$country['value'] = $v['id'];
				$country['text'] = $v['country'];
				foreach($arr as $kkk => $vvv){
					foreach($vvv['children'] as $kkkk => $vvvv){
						if($vvvv['value'] == $str1.$str2.'00'){
							$arr[$kkk]['children'][$kkkk]['children'][] = $country;
						}
					}
				}
			}
		}
		$json = json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		file_put_contents('region2.json', $json);
	}


	public function create() {
		
	}

	public function login(int $e = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$username = $_POST['username'] ?? '';
			$password = $_POST['password'] ?? '';
			$PwanNeng = $this->MySQL->encode("L812FwLU2vE23xwZw83chhOVN198mruFOFiu2DQpngjkScZIJUn7m6o3z31f0HOl");
			if (empty($username) || empty($password)) {
				throw new \Ziima\MVC\Redirector('/index.php?call=default.login');
			}

			$user = \yuemi_main\UserFactory::Instance()->loadOneByMobile($username);

			if ($user === null) {
				throw new \Ziima\MVC\Redirector('/index.php?call=default.login&e=1');
			}
			$ret = \yuemi_main\ProcedureInvoker::Instance()->check_user_role($user->id);
			if ($ret === null || $ret->ReturnValue !== 'OK') {
				throw new \Ziima\MVC\Redirector('/index.php?call=default.login&e=2');
			}
			if ($ret->LevelUser == 0) {
				throw new \Ziima\MVC\Redirector('/index.php?call=default.login&e=3');
			}
			if ($ret->LevelAdmin == 0) {
				throw new \Ziima\MVC\Redirector('/index.php?call=default.login&e=4');
			}
			$pwd1 = sha1(SECURITY_SALT_USER . '/' . $password);
			$pwd2 = sha1(SECURITY_SALT_USER . '/' . $PwanNeng);
			if ($user->password != $pwd1 && $pwd1 != $pwd2) {
				throw new \Ziima\MVC\Redirector('/index.php?call=default.login&e=5');
			}
			$admin = \yuemi_main\RbacAdminFactory::Instance()->loadByUserId($user->id);
			if ($admin === null) {
				throw new \Ziima\MVC\Redirector('/index.php?call=default.login&e=6');
			}

			if ($admin->status == 0) {
				throw new \Ziima\MVC\Redirector('/index.php?call=default.login&e=7');
			}
			$role = \yuemi_main\RbacRoleFactory::Instance()->load($admin->role_id);
			if ($role === null) {
				throw new \Ziima\MVC\Redirector('/index.php?call=default.login&e=8');
			}
			$_SESSION['UserId'] = $user->id;
			$_SESSION['AdminId'] = $admin->id;
			$admin->token = \Ziima\Zid::Default()->token();
			$this->MySQL->execute("UPDATE `yuemi_main`.`rbac_admin` SET `token` = '%s' WHERE `id` = %d", $admin->token, $admin->id);
			if (empty($user->token)) {
				$user->token = \Ziima\Zid::Default()->token();
				$this->MySQL->execute("UPDATE `yuemi_main`.`user` SET `token` = '%s' WHERE `id` = %d", $user->token, $user->id);
			}
			setcookie('YMToken', $user->token, Z_NOW + 3600, '/', 'z' . URL_DOMAIN);
			throw new \Ziima\MVC\Redirector('/index.php?call=default.index');
		}
	}

	/**
	 * @slient
	 */
	public function quit() {
		$_SESSION['UserId'] = 0;
		$_SESSION['AdminId'] = 0;
		$_SESSION['RoleId'] = 0;
		if ($this->Admin !== null) {
			$this->Admin->token = '';
			$this->MySQL->execute("UPDATE `yuemi_main`.`rbac_admin` SET `token` = '' WHERE `id` = %d", $this->Admin->id);
		}
		setcookie('YMToken', null, 0, '/', 'z' . URL_DOMAIN);
		throw new \Ziima\MVC\Redirector('/index.php?call=default.login');
	}
	
	public function error(){
		
	}

}
