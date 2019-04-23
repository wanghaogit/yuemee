<?php
include_once Z_SITE . "/lib/AdminHandler.php";
include_once Z_SITE . '/../../_base/ClassReflection.php';

/**
 * API测试
 * @auth
 */
class test_api_handler extends AdminHandler
{
	private $DirPath = Z_SITE . "/../../api/master/lib/handler/";

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 首页（列表）
         * http://z.ym.cn/index.php?call=test_api.index
	 */
	public function index()
	{
		$FileList = $this->get_files($this->DirPath);
		$ClassList = array();
		foreach ($FileList AS $FileName)
		{
			if (!file_exists($this->DirPath . $FileName . '.php')) {
				continue;
			}
			include_once $this->DirPath . $FileName . '.php';
			$ClassReflection = new ClassReflection("{$FileName}_handler");
			$ClassInfo = $ClassReflection->getClassDetails(1, '_');
			$ClassInfo->name = str_replace('_handler', '', $ClassInfo->name);
			$ClassList[] = $ClassInfo;
		}
		return ['ClassList' => $ClassList];
	}

	/**
	 * POST提交测试页
	 */
	public function post(string $class, string $action)
	{
		include_once $this->DirPath . $class . '.php';
		$ClassReflection = new ClassReflection("{$class}_handler");
		$ClassInfo = $ClassReflection->getClassDetails(1, '_');
		$ClassInfo->name = str_replace('_handler', '', $ClassInfo->name);
		return ['ClassInfo' => $ClassInfo, 'ActionInfo' => $ClassInfo->methods_list[$action], 'TimeNow'=> date("YmdHis")];
	}

	/**
	 * 返回目录下所有文件名
	 * @param string $DirPath
	 * @return array
	 */
	private function get_files(string $DirPath)
	{
		$FileList = array();
		$DirPath = str_replace("\\", "/", $DirPath);
		if (substr($DirPath, -1) != "/") {
			$DirPath .= "/";
		}
		$P = opendir($DirPath);
		if (!$P) {
			return $FileList;
		}
		while ($name = readdir($P)) {
			if (empty($name) || substr($name, -4) != '.php') {
				continue;
			}
			if (is_file($DirPath . $name)) {
				$name = basename($name);
				$name = str_replace('.php', '', $name);
				$FileList[] = $name;
			}
		}
		closedir($P);
		asort($FileList);
		return $FileList;
	}

}
