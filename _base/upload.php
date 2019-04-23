<?php

/**
 * 上传公共文件
 */
include Z_ROOT . '/Chart.php';

/**
 * 输出错误信息
 * @param string $code
 * @param string $message
 */
function _E(string $code, string $message = '') {
	echo json_encode([
		'__code' => $code,
		'__message' => $message
			], JSON_UNESCAPED_UNICODE);
	ob_flush();
	exit;
}

/**
 * 上传请求
 */
class UploadRequest extends \Ziima\MVC\REST\Request {

	/**
	 * 接口应用
	 * @var \yuemi_main\AppletEntity
	 */
	public $Applet;

	/**
	 * 用户
	 * @var \yuemi_main\UserEntity
	 */
	public $User;

	/**
	 * 管理员
	 * @var \yuemi_main\RbacAdminEntity
	 */
	public $Admin;

	/**
	 * 角色
	 * @var \yuemi_main\RbacRoleEntity
	 */
	public $Role;

	/**
	 * 供应商
	 * @var \yuemi_main\SupplierEntity
	 */
	public $Supplier;

	/**
	 * 各种初始化
	 */
	public function init() {
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			_E('E_PROTOCOL', '要求POST提交');
		}
		$this->__udid = $_POST['__udid'] ?? '';
		$this->__timestamp = $_POST['__timestamp'] ?? '';
		$this->__applet_token = $_POST['__applet_token'] ?? '';
		$this->__access_token = $_POST['__access_token'] ?? '';
		if (!preg_match('/^[a-z0-9]{12,32}$/i', $this->__udid)) {
			_E('E_PROTOCOL', '无效的 uuid 参数');
		}
		if (!preg_match('/^\d{14}$/', $this->__timestamp)) {
			_E('E_PROTOCOL', '无效的 timestamp 参数');
		}
		//TODO:检查时间戳，应该在 正负30秒内
		if (!preg_match('/^[a-z0-9]{12,32}$/i', $this->__applet_token)) {
			_E('E_PROTOCOL', '无效的 applet_token 参数');
		}
		if (!preg_match('/^[a-z0-9]{12,32}$/i', $this->__access_token)) {
			_E('E_PROTOCOL', '无效的 access_token 参数');
		}

		$this->_init_applet();
		$this->_init_user();
		$this->_init_dir();
	}

	private function _init_applet() {
		$this->Applet = \yuemi_main\AppletFactory::Instance()->loadByToken($this->__applet_token);
		if ($this->Applet === null) {
			_E('E_ACL', '非法的 applet_token 参数');
		}
		if ($this->Applet->status != 2) {
			_E('E_ACL', '当前 applet_token 无权');
		}
	}

	private function _init_user() {
		$this->User = \yuemi_main\UserFactory::Instance()->loadOneByToken($this->__access_token);
		if ($this->User === null) {
			_E('E_ACL', '当前 access_token 已过期');
		}
		if ($this->User->level_u == 0) {
			_E('E_ACL', '当前 access_token 缺少必要身份');
		}
		if ($this->User->level_a > 0) {
			$this->Admin = \yuemi_main\RbacAdminFactory::Instance()->loadByUserId($this->User->id);
			if ($this->Admin !== null) {
				$this->Role = \yuemi_main\RbacRoleFactory::Instance()->load($this->Admin->role_id);
			}
		}
		if ($this->User->level_s > 0) {
			$this->Supplier = \yuemi_main\SupplierFactory::Instance()->loadOneByUserId($this->User->id);
		}
	}

	private function _init_dir() {
		if (!defined('UPLOAD_ROOT')) {
			_E('E_SYSTEM', '未定义上传根目录');
		}
		if (!file_exists(UPLOAD_ROOT)) {
			@mkdir(UPLOAD_ROOT, 0755, true);
		}
		if (!file_exists(UPLOAD_ROOT)) {
			_E('E_SYSTEM', '上传根目录不存在 ' . UPLOAD_ROOT);
		}
		if (!is_dir(UPLOAD_ROOT)) {
			_E('E_SYSTEM', '上传根目录配置不是一个文件夹');
		}
	}

	function _init_task(UploadTask $task) {
		$task->File = $_FILES['file'] ?? null;
		if ($task->File === null) {
			_E('E_PROTOCOL', '缺少文件数据');
		}
		if ($task->File['error'] != 0) {
			_E('E_UPLOAD', '文件上传失败');
		}
		if ($task->_get_size_min() > 0 && $task->File['size'] < $task->_get_size_min() * 1024) { //检查文件尺寸下限
			_E('E_UPLOAD', '文件大小低于 ' . $task->_get_size_min() . ' KB ');
		}
		if ($task->_get_size_max() > 0 && $task->File['size'] > $task->_get_size_max() * 1024) {
			_E('E_UPLOAD', '文件大小超过 ' . $task->_get_size_max() . ' KB');
		}
		$tmp = explode('.', $task->File['name']);
		$task->Ext = strtolower(trim($tmp[count($tmp) - 1]));
	}

	function _init_serial(UploadTask $task): bool {
		$task->Serial = \Ziima\Zid::Default()->serial();

		$task->Target = UPLOAD_ROOT . DIRECTORY_SEPARATOR . $task->_get_group();
		$task->Uri = '/' . $task->_get_group();

		$task->Target .= DIRECTORY_SEPARATOR . substr($task->Serial, 0, 2) . DIRECTORY_SEPARATOR . substr($task->Serial, 2, 2);
		$task->Uri .= '/' . substr($task->Serial, 0, 2) . '/' . substr($task->Serial, 2, 2);
		$task->Target = str_replace(['\\', '/'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $task->Target);
		if (!file_exists($task->Target)) {
			mkdir($task->Target, 0755, true);
		}
		if (!file_exists($task->Target)) {
			_E('E_PROTOCOL', '准备目录 ' . $this->Uri . ' 失败');
		}

		$task->Target .= DIRECTORY_SEPARATOR . $task->Serial;
		$task->Uri .= '/' . $task->Serial;
		$task->ThumbTarget = $task->Target;
		$task->ThumbUri = $task->Uri;

		$task->Target .= '.' . $task->Ext;
		$task->Uri .= '.' . $task->Ext;
		$task->ThumbTarget .= '-thumb.' . $task->Ext;
		$task->ThumbUri .= '-thumb.' . $task->Ext;

		if (file_exists($task->Target)) {
			return false;
		} else {
			return true;
		}
	}

	private function _init_range(UploadTask $task) {
		$rmin = $task->_get_range_min();
		$rmax = $task->_get_range_max();
		if ($rmin !== null || $rmax !== null) {
			$sz = getimagesize($task->File['tmp_name']);
			if ($sz === false || $sz === null) {
				_E('E_PICTURE', '上传图片文件格式错误#1');
				return;
			}
			$task->ImageWidth = $sz[0];
			$task->ImageHeight = $sz[1];

			if ($rmin !== null) {
				if ($rmin->width > 0 && $task->ImageWidth < $rmin->width) {
					_E('E_PICTURE', '上传的图片宽度小于 ' . $rmin->width);
				}
				if ($rmin->height > 0 && $task->ImageHeight < $rmin->height) {
					_E('E_PICTURE', '上传的图片高度小于 ' . $rmin->height);
				}
			}
			if ($rmax !== null) {
				if ($rmax->width > 0 && $task->ImageWidth > $rmax->width) {
					_E('E_PICTURE', '上传的图片宽度大于 ' . $rmax->width);
				}
				if ($rmax->height > 0 && $task->ImageHeight > $rmax->height) {
					_E('E_PICTURE', '上传的图片高度大于 ' . $rmax->height);
				}
			}
		}
	}

	/**
	 * 准备上传头像任务
	 * @return \UTAvatar
	 */
	public function execute(UploadTask $task, \Closure $success, $param = null) {
		$this->_init_task($task);
		while (!$this->_init_serial($task)) {
			;
		}
		$this->_init_range($task);
		$rsz = $task->_get_range_force();
		$rtb = $task->_get_range_thumb();
		if ($rsz !== null || $rtb !== null) {
			$pic = null;
			try {
				$pic = new \Ziima\Drawing\Picture($task->File['tmp_name']);
			} catch (\Exception $ex) {
				_E('E_PICTURE', '上传图片文件格式错误#2');
				return;
			}
			if ($pic === null) {
				_E('E_PICTURE', '上传图片文件格式错误#3');
				return;
			}
			if ($rtb !== null && $rtb->width > 0 && $rtb->height > 0) {
				$pic = new \Ziima\Drawing\Picture($task->File['tmp_name']);
				$pic->thumbnail($rtb->width, $rtb->height);
				$pic->saveAs($task->ThumbTarget);
				unset($pic);
				$pic = null;
			} else {
				$task->ThumbTarget = '';
				$task->ThumbUri = '';
			}
			if ($rsz !== null && $rsz->width > 0 && $rsz->height > 0) {
				if ($pic == null)
					$pic = new \Ziima\Drawing\Picture($task->File['tmp_name']);
				$pic->thumbnail($rsz->width, $rsz->height);
				$pic->saveAs($task->Target);
				$task->ImageHeight = $rsz->height;
				$task->ImageWidth = $rsz->width;
				unset($pic);
				$pic = null;
			} else {
				if (!move_uploaded_file($task->File['tmp_name'], $task->Target)) {
					_E('E_IO', '保存文件失败');
				}
			}
		} else {
			if (!move_uploaded_file($task->File['tmp_name'], $task->Target)) {
				_E('E_IO', '保存文件失败');
			}
		}
		$success->call($this, $task, $param);
	}

}

/**
 * 上传项目
 */
abstract class UploadTask {

	/**
	 * 上传数据
	 * @var array
	 */
	public $File;

	/**
	 * 保存路径
	 * @var string
	 */
	public $Target;

	/**
	 * 访问相对路径
	 * @var string
	 */
	public $Uri;

	/**
	 * 缩略图保存路径
	 * @var string
	 */
	public $ThumbTarget;

	/**
	 * 缩略图访问相对路径
	 * @var string
	 */
	public $ThumbUri;

	/**
	 * 源文件名
	 * @var string
	 */
	public $Name;

	/**
	 * 扩展名
	 * @var string
	 */
	public $Ext;

	/**
	 * 文件序列号
	 * @var string
	 */
	public $Serial;

	/**
	 * 图片宽度
	 * @var int
	 */
	public $ImageWidth;

	/**
	 * 图片高度
	 * @var int
	 */
	public $ImageHeight;

	public abstract function _get_group(): string;

	public function _get_size_min(): int {
		return 1;
	}

	public function _get_size_max(): int {
		return 2048;
	}

	public function _get_width_min(): int {
		return 0;
	}

	public function _get_width_max(): int {
		return 0;
	}

	public function _get_height_min(): int {
		return 0;
	}

	public function _get_height_max(): int {
		return 0;
	}

	public function _get_range_min(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(64, 64);
	}

	public function _get_range_max(): ?\Ziima\Drawing\Size {
		return null;
	}

	public function _get_range_force(): ?\Ziima\Drawing\Size {
		return null;
	}

	public function _get_range_thumb(): ?\Ziima\Drawing\Size {
		return null;
	}

}

/**
 * 个人头像
 */
final class UTAvatar extends UploadTask {

	public function _get_group(): string {
		return 'avatar';
	}

	public function _get_size_min(): int {
		return 1;
	}

	public function _get_size_max(): int {
		return 512;
	}

	public function _get_range_force(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(120, 120);
	}

}

/**
 * 身份证照片
 */
final class UTCert extends UploadTask {

	public function _get_group(): string {
		return "cert";
	}

	public function _get_size_min(): int {
		return 16;
	}

	public function _get_size_max(): int {
		return 1024;
	}

	public function _get_range_min(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(200, 100);
	}

}

final class UTShare extends UploadTask {

	public function _get_group(): string {
		return "share";
	}

	public function _get_size_min(): int {
		return 16;
	}

	public function _get_size_max(): int {
		return 2048;
	}

	public function _get_range_min(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(500, 500);
	}

	public function _get_range_force(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(800, 800);
	}

	public function _get_range_thumb(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(320, 320);
	}

}

final class UTSku extends UploadTask {

	public function _get_group(): string {
		return "sku";
	}

	public function _get_size_min(): int {
		return 16;
	}

	public function _get_size_max(): int {
		return 2048;
	}

	public function _get_range_min(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(500, 500);
	}

	public function _get_range_force(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(800, 800);
	}

	public function _get_range_thumb(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(320, 320);
	}

}

/**
 * SKU内容图
 */
final class UTSkuContent extends UploadTask {

	public function _get_group(): string {
		return "sku";
	}

	public function _get_size_min(): int {
		return 1;
	}

	public function _get_size_max(): int {
		return 512;
	}

	public function _get_range_min(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(10, 10);
	}

	public function _get_range_max(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(800, 1000);
	}

}

/**
 * 邀请模板
 */
final class UTInviteTemplate extends UploadTask {

	public function _get_group(): string {
		return "template";
	}

	public function _get_size_min(): int {
		return 16;
	}

	public function _get_size_max(): int {
		return 1024;
	}

	public function _get_range_min(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(360, 550);
	}

	public function _get_range_max(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(720, 1100);
	}

}

/**
 * 分享模板
 */
final class UTShareTemplate extends UploadTask {

	public function _get_group(): string {
		return "template";
	}

	public function _get_size_min(): int {
		return 4;
	}

	public function _get_size_max(): int {
		return 512;
	}

	public function _get_range_min(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(360, 200);
	}

	public function _get_range_max(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(750, 1300);
	}

}

/**
 * 营业执照
 */
final class UTCorp extends UploadTask {

	public function _get_group(): string {
		return "cert";
	}

	public function _get_size_min(): int {
		return 32;
	}

	public function _get_size_max(): int {
		return 1024;
	}

	public function _get_range_min(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(200, 100);
	}

}

/**
 * CMS图
 */
final class UTCMS extends UploadTask {

	public function _get_group(): string {
		return "cms";
	}

	public function _get_size_min(): int {
		return 1;
	}

	public function _get_size_max(): int {
		return 512;
	}

	public function _get_range_max(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(1920, 1920);
	}

	public function _get_range_thumb(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(80, 80);
	}
}

/**
 * 运营专题
 */
final class UTRuner extends UploadTask {

	public function _get_group(): string {
		return "run";
	}

	public function _get_size_min(): int {
		return 1;
	}

	public function _get_size_max(): int {
		return 1024;
	}

	public function _get_range_max(): ?\Ziima\Drawing\Size {
		return new \Ziima\Drawing\Size(1920, 1080);
	}

}
