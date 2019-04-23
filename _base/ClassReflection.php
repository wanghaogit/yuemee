<?php
/**
 * 类反射
 */
class ClassReflection
{
	private $ClassName = null;

	/**
	 * 类反射详情
	 * @var \ClassReflectionDetails
	 */
	private $ClassDetails = null;

	/**
	 * 构造函数
	 * @param string $classname 类名
	 */
	public function __construct(string $classname) {
		$this->ClassName = $classname;
	}

	/**
	 * 获取类详情
	 * @param int $GetMethodsType	读取方法类型：0-读取全部类型的方法 1-public 2-protected 3-private
	 * @param string $MethodsIgnorePrefix 忽略方法前缀（以某前缀开头的方法不返回）
	 * @return \ClassReflectionDetails
	 */
	public function getClassDetails(int $GetMethodsType = 0, string $MethodsIgnorePrefix = null) : \ClassReflectionDetails
	{
		if ($this->ClassDetails == null) {
			$this->ClassDetails = new ClassReflectionDetails();
			// 读取类基本信息
			$class = new ReflectionClass($this->ClassName);
			$this->ClassDetails->name = $this->ClassName;
			$this->ClassDetails->annotation = $class->getDocComment();
			// 格式化注释数据
			$AnnotationFormat = $this->formatClassAnnotation($this->ClassDetails->annotation);
			$this->ClassDetails->action = $AnnotationFormat['action']??'';
			// 读取方法列表
			$GetMethodsType = intval($GetMethodsType);
			switch ($GetMethodsType)
			{
				case 1: $templist = $class->getMethods(ReflectionMethod::IS_PUBLIC); break;
				case 2: $templist = $class->getMethods(ReflectionMethod::IS_PROTECTED); break;
				case 3: $templist = $class->getMethods(ReflectionMethod::IS_PRIVATE); break;
				default: $templist = $class->getMethods(); break;
			}
			$IgnoreLen = strlen($MethodsIgnorePrefix);
			foreach ($templist AS $info) {
				if ($MethodsIgnorePrefix != null) {
					if (substr($info->name,0,$IgnoreLen) == $MethodsIgnorePrefix) continue;
				}
				$this->ClassDetails->methods_list[$info->name] = $this->getMethodsDetails($info->name);
			}
		}
		return $this->ClassDetails;
	}

	/**
	 * 获取方法信息
	 * @param string $MethodsName 方法名称
	 */
	public function getMethodsDetails(string $MethodsName) : \ClassReflectionMethodsDetails
	{
		$MethodsDetails = new ClassReflectionMethodsDetails();
		$method = new ReflectionMethod($this->ClassName, $MethodsName);
		$MethodsDetails->annotation = $method->getDocComment();
		// 格式化注释数据
		$AnnotationFormat = $this->formatMethodAnnotation($MethodsDetails->annotation);
		$MethodsDetails->action = $AnnotationFormat['action']??'';
		$MethodsDetails->params = $AnnotationFormat['params']??null;
		return $MethodsDetails;
	}

	/** ***************************************** 私有方法 ***************************************** **/

	/**
	 * 格式化类注释
	 * @parem $AnnotationStr 注释字符串
	 * @return array|null
	 */
	private function formatClassAnnotation(string $AnnotationStr) : ?array {
		$AnnotationArr = null;
		$AnnotationStr = str_replace("\r", "\n", $AnnotationStr);
		$RowList = explode("\n", $AnnotationStr);
		foreach ($RowList AS $row) {
			$row = $this->formatClearRow($row);
			if (empty($row)) continue;
			// 功能注释
			if (substr($row[0],0,1) != '@' && (!isset($AnnotationArr['action']) || empty($AnnotationArr['action']))) {
				$AnnotationArr['action'] = $row[0];
			}
		}
		return $AnnotationArr;
	}

	/**
	 * 格式化方法注释
	 * @parem $AnnotationStr 注释字符串
	 * @return array|null
	 */
	private function formatMethodAnnotation(string $AnnotationStr) : ?array {
		$AnnotationArr = null;
		$AnnotationStr = str_replace("\r", "\n", $AnnotationStr);
		$RowList = explode("\n", $AnnotationStr);
		foreach ($RowList AS $row) {
			$row = $this->formatClearRow($row);
			if (empty($row[0])) continue;
			// 功能注释
			if (substr($row[0],0,1) != '@' && (!isset($AnnotationArr['action']) || empty($AnnotationArr['action']))) {
				$AnnotationArr['action'] = $row[0];
			}
			// 参数列表
			if (substr($row[0],0,1) == '@') {
				$paraminfo[0] = $row[0];
				$paraminfo[1] = $row[1]??'';
				$paraminfo[2] = $row[2]??'';
				$paraminfo[3] = $row[3]??'';
				$paraminfo[4] = $row[4]??'';
				$paraminfo[5] = $row[5]??'';
				$AnnotationArr['params'][] = $paraminfo;
			}
		}
		return $AnnotationArr;
	}

	/**
	 * 格式化数据时行清理
	 * @return string|null
	 */
	private function formatClearRow(string $row) : array {
		$row = trim($row);
		$row = trim($row, '/**');
		$row = trim($row, '/*');
		$row = trim($row, '*/');
		$row = trim($row, '**/');
		$row = trim($row, '*');
		$row = trim($row);
		$row = preg_replace("/[\t\r\n ]{1,}/isu", ' ', $row);
		return explode(' ', $row);
	}
}

/**
 * 类反射详情
 */
class ClassReflectionDetails
{
	/**
	 * 名称
	 * @var string
	 */
	public $name;

	/**
	 * 注释
	 * @var string
	 */
	public $annotation;

	/**
	 * 功能说明（由annotation解析得到）
	 * @var string
	 */
	public $action;

	/**
	 * 方法列表
	 * @var array
	 */
	public $methods_list;
}

/**
 * 方法反射详情
 */
class ClassReflectionMethodsDetails
{
	/**
	 * 注释
	 * @var string
	 */
	public $annotation;

	/**
	 * 功能说明（由annotation解析得到）
	 * @var string
	 */
	public $action;

	/**
	 * 参数列表（由annotation解析得到）
	 * @var array
	 */
	public $params;
}
