<?php
include_once Z_SITE . "/lib/AdminHandler.php";
include_once Z_SITE . '/../../_base/ClassReflection.php';

/**
 * 开发者工具
 * @auth
 */
class teach_tools_handler extends AdminHandler
{
	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 开发者工具首页
		 * http://z.ym.cn/index.php?call=teach_tools.index
	 */
	public function index()
	{
		$ProcList = array();
		$DbList = array('yuemi_main', 'yuemi_sale');
		foreach ($DbList AS $DbName)
		{
			$Sql = "SELECT db, name, type, comment FROM mysql.proc WHERE db = '{$DbName}' AND `type` = 'PROCEDURE'";
			$PList = $this->MySQL->grid($Sql);
			$ProcList[$DbName] = $PList;
		}
		return ['ProcList' => $ProcList];
	}

	/**
	 * 存储过程详情
	 * @param string $DbName	数据库名称
	 * @param string $ProcName	存储过程名称
	 */
	public function procedure_info(string $DbName, string $ProcName)
	{
		$Sql = "SELECT ORDINAL_POSITION,PARAMETER_MODE,PARAMETER_NAME,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH,NUMERIC_PRECISION,NUMERIC_SCALE ";
		$Sql .= "FROM information_schema.PARAMETERS WHERE SPECIFIC_SCHEMA = '{$DbName}' AND SPECIFIC_NAME = '{$ProcName}' ";
		$Sql .= "ORDER BY ORDINAL_POSITION ASC";
		$ParamList = $this->MySQL->grid($Sql);
		return ['ParamList' => $ParamList];
	}

}
