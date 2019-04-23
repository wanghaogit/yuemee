<?php

include "lib/ApiHandler.php";

/**
 * 运营管理
 * @auth
 */
class runer_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	public function index(int $p = 0) {
		
	}

	/**
	 * 应用内--静态--创建
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function spage_add(\Ziima\MVC\REST\Request $request) {
		$RunPageFactory = new \yuemi_main\RunPageFactory(MYSQL_WRITER, MYSQL_READER);
		$RunPageEntity = new \yuemi_main\RunPageEntity();
		$RunPageEntity->alias = $request->body->alias;
		$RunPageEntity->name = $request->body->name;
		$RunPageEntity->parent_id = $request->body->parent_id;
		$RunPageEntity->template = '';
		$RunPageEntity->style = 0;
		$RunPageFactory->insert($RunPageEntity);
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 应用内--静态--修改
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function spage_update(\Ziima\MVC\REST\Request $request) {
		$RunPageFactory = new \yuemi_main\RunPageFactory(MYSQL_WRITER, MYSQL_READER);
		$RunPageEntity = $RunPageFactory->load($request->body->id);
		$RunPageEntity->name = $request->body->name;
		$RunPageFactory->update($RunPageEntity);
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 应用内--静态--删除
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function spage_del(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$this->del($id);
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 应用内--静态--删除
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function dpage_block_del(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$sql1 = "DELETE FROM `yuemi_main`.`run_block` WHERE `id` = {$id}";
		$this->MySQL->execute($sql1);
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 应用内--静态--删除
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function spage_block_del(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$sql1 = "DELETE FROM `yuemi_main`.`run_block` WHERE `id` = {$id}";
		$this->MySQL->execute($sql1);
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 专题--动态--删除
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function dpage_del(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$this->del($id);
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 
	 * @param int $p
	 */
	public function dpage(int $p = 0) {
		
	}

	public function widget(int $p = 0) {
		
	}

	/**
	 * 数据源删除
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function widget_del(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$sql1 = "DELETE FROM `yuemi_main`.`run_widget` WHERE `id` = {$id}";
		$this->MySQL->execute($sql1);
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 数据源删除
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function source_del(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$sql1 = "DELETE FROM `yuemi_main`.`run_source` WHERE `id` = {$id}";
		$this->MySQL->execute($sql1);
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	public function release(int $p = 0) {
		
	}

	private function del($pid) {
		$sql1 = "DELETE FROM `yuemi_main`.`run_page` WHERE `id` = {$pid}";
		$this->MySQL->execute($sql1);
		$sql = "SELECT `id` FROM `yuemi_main`.`run_page` WHERE `parent_id` = {$pid}";
		$IdArr = $this->MySQL->grid($sql);
		if (empty($IdArr))
			return;
		foreach ($IdArr as $val) {
			$id = $val['id'];
			$this->del($id);
		}
		return;
	}

	/**
	 * 数据源显示
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function source_load(\Ziima\MVC\REST\Request $request) {
		$sql = "SELECT * FROM `yuemi_main`.`run_source`";
		$Re = $this->MySQL->grid($sql);
		return [
			'Result' => $Re,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 组件显示
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function widget_load(\Ziima\MVC\REST\Request $request) {
		$sql = "SELECT * FROM `yuemi_main`.`run_widget`";
		$Re = $this->MySQL->grid($sql);
		return [
			'Result' => $Re,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 模块添加数据源
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function block_add_source(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->block_id;
		$source_id = $request->body->source_id;
		$RunBlockEntity = \yuemi_main\RunBlockFactory::Instance()->load($id);
		$RunBlockEntity->source_id = $source_id;
		\yuemi_main\RunBlockFactory::Instance()->update($RunBlockEntity);
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 模块添加组件
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function block_add_widget(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->block_id;
		$widget_id = $request->body->widget_id;
		$RunBlockEntity = \yuemi_main\RunBlockFactory::Instance()->load($id);
		$RunBlockEntity->widget_id = $widget_id;
		\yuemi_main\RunBlockFactory::Instance()->update($RunBlockEntity);
		return [
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 组件测试
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id			int		组件ID
	 */
	public function widget_test(\Ziima\MVC\REST\Request $request) {
		$dir_cpl = Z_SITE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'runner' . DIRECTORY_SEPARATOR . 'c';
		$dir_tpl = Z_SITE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'runner' . DIRECTORY_SEPARATOR . 't';
		$dir_php = Z_SITE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'runner' . DIRECTORY_SEPARATOR . 'p';
		$dir_htm = Z_SITE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'runner' . DIRECTORY_SEPARATOR . 's';
		if (!file_exists($dir_cpl))
			mkdir($dir_cpl, 0755, true);
		if (!file_exists($dir_tpl))
			mkdir($dir_tpl, 0755, true);
		if (!file_exists($dir_php))
			mkdir($dir_php, 0755, true);
		if (!file_exists($dir_htm))
			mkdir($dir_htm, 0755, true);
		include_once Z_ROOT . '/Template.php';
		$id = $request->body->id;
		$te = new \Ziima\Template\TemplateEngine(
				$dir_tpl, $dir_cpl
		);
		$widget = \yuemi_main\RunWidgetFactory::Instance()->load($id);
		// 把组件的模板存到文件
		file_put_contents($dir_tpl . DIRECTORY_SEPARATOR . $widget->id . '.tpl', $widget->template);
		//检查模板语法
		try {
			$te->compile($widget->id . '.tpl');
		} catch (\Exception $e) {
			throw new \Ziima\MVC\REST\Exception('E_TEMPLATE', $e->getMessage());
		}
		return "OK";
	}

	/**
	 * 数据源测试
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id			int		数据源ID
	 */
	public function source_test(\Ziima\MVC\REST\Request $request) {
		$dir_cpl = Z_SITE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'runner' . DIRECTORY_SEPARATOR . 'c';
		$dir_tpl = Z_SITE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'runner' . DIRECTORY_SEPARATOR . 't';
		$dir_php = Z_SITE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'runner' . DIRECTORY_SEPARATOR . 'p';
		$dir_htm = Z_SITE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'runner' . DIRECTORY_SEPARATOR . 's';
		if (!file_exists($dir_cpl))
			mkdir($dir_cpl, 0755, true);
		if (!file_exists($dir_tpl))
			mkdir($dir_tpl, 0755, true);
		if (!file_exists($dir_php))
			mkdir($dir_php, 0755, true);
		if (!file_exists($dir_htm))
			mkdir($dir_htm, 0755, true);
		$source = \yuemi_main\RunSourceFactory::Instance()->load($request->body->id);
		$rst = null;
		switch ($source->style) {
			case 0 :
				$rst = $this->MySQL->grid($source->driver);
				break;
			case 1:
				include_once Z_ROOT . '/Template.php';
				$te = new \Ziima\Template\TemplateEngine(
						$dir_tpl, $dir_cpl
				);
				file_put_contents($dir_tpl . DIRECTORY_SEPARATOR . $source->id . '.tpl', $source->driver);
				//检查模板语法
				try {
					$te->compile($source->id . '.tpl');
				} catch (\Exception $e) {
					throw new \Ziima\MVC\REST\Exception('E_TEMPLATE', $e->getMessage());
				}
				break;
			case 2:
				$sql = $this->get_mysql($source->driver);
				$rst1 = $this->MySQL->grid($sql);
				$rst = $this->get_data($rst1);
				break;
			case 3:
				$block_id = intval($source->driver);
				$Re = $this->MySQL->row("SELECT `id` AS `Id`,'' AS Picture,`name` AS Title FROM `yuemi_sale`.`catagory` WHERE `id` = {$block_id}");
				$Re['Url'] = 'https://a.yuemee.com/mobile.php?call=runer.page&id=' . $Re['Id'];
				return [
					'type' => $source->style,
					'Page' => $Re
				];
			case 4:
				$catid = intval($source->driver);
				return [
					'type' => $source->style,
					'Catagory' =>
					$this->MySQL->row("SELECT `id` AS `Id`,`name` AS `Name` FROM `yuemi_sale`.`catagory` WHERE `id` = {$catid}")
				];
			default :
				break;
		}
		return [
			'type' => $source->style,
			'Result' => $rst,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	private function get_data($data) {
		foreach ($data as $key => $val) {
			$data[$key]['Picture'] = $this->get_url($val['Id']);
		}
		return $data;
	}

	private function get_url($id) {
		$sku_id = ($this->MySQL->row("SELECT `sku_id` FROM `yuemi_sale`.`shelf` WHERE `id` = {$id}"))['sku_id'];
		$spu_id = ($this->MySQL->row("SELECT `spu_id` FROM `yuemi_sale`.`sku` WHERE `id` = {$sku_id}"))['spu_id'];
		$result1 = $this->MySQL->grid("SELECT `file_url` FROM `yuemi_sale`.`shelf_material` WHERE `shelf_id` = {$id}");
		$result2 = $this->MySQL->grid("SELECT `file_url` FROM `yuemi_sale`.`sku_material` WHERE `sku_id` = {$sku_id}");
		$result3 = $this->MySQL->grid("SELECT `file_url` FROM `yuemi_sale`.`spu_material` WHERE `spu_id` = {$spu_id}");
		$R1 = [];
		$R2 = [];
		$R3 = [];
		foreach ($result1 as $key => $val) {
			$R1[$key] = $val['file_url'];
		}
		foreach ($result2 as $key => $val) {
			$R2[$key] = $val['file_url'];
		}
		foreach ($result3 as $key => $val) {
			$R3[$key] = $val['file_url'];
		}
		$array = array_merge($R1, $R2, $R3);
		array_unique($array);
		if (empty($array))
			return;
		return $array[0];
	}

	private function get_mysql($str) {
		$arr = explode('|', $str);
		$whr = [];
		foreach ($arr as $val) {
			$tepArr = explode(':', $val);
			$whr[$tepArr[0]] = $tepArr[1];
		}
		$sql = "SELECT `S`.`id` AS `Id`,`S`.`title` AS `Title`,'' AS `Picture`,'' AS `Url`,'' AS `Action`,"
				. "`S`.`price_sale` AS `Price_Rel`,`S`.`price_user` AS `Price_User`,`S`.`price_vips` AS `Price_Vip`,"
				. "`S`.`rebate_user` AS `Rebate_User`,`S`.`rebate_vip` AS `Rebate_Vip`,"
				. "`S`.`coin_user` AS `Coin_User`,`S`.`coin_vips` AS `Coin_Vip`,"
				. "`SU`.`id` AS `Supplier_Id`,`SU`.`name` AS `Supplier_Name`,"
				. "`CB`.`id` AS `Catagory_B_Id`,`CB`.`name` AS `Catagory_B_Name`,`CB`.`icon` AS `Catagory_B_Logo`,"
				. "`CA`.`id` AS `Catagory_A_Id`,`CA`.`name` AS `Catagory_A_Name`,`CA`.`icon` AS `Catagory_A_Logo`,"
				. "`B`.`id` AS `Brand_Id`,`B`.`name` AS `Brand_Name`,`B`.`logo` AS `Brand_Logo`"
				. "FROM `yuemi_sale`.`shelf` AS `S` "
				. "LEFT JOIN `yuemi_sale`.`catagory` AS `CB` ON `S`.`catagory_id` = `CB`.`id` "
				. "LEFT JOIN `yuemi_sale`.`catagory` AS `CA` ON `CB`.`parent_id` = `CA`.`id` "
				. "LEFT JOIN `yuemi_sale`.`sku` AS `K` ON `S`.`sku_id` = `K`.`id` "
				. "LEFT JOIN `yuemi_main`.`supplier` AS `SU` ON `K`.`supplier_id` = `SU`.`id` "
				. "LEFT JOIN `yuemi_sale`.`spu` AS `P` ON `K`.`spu_id` = `P`.`id` "
				. "LEFT JOIN `yuemi_sale`.`brand` AS `B` ON `P`.`brand_id` = `B`.`id` "
				. "LEFT JOIN `yuemi_sale`.`shelf_material` AS `M` ON `M`.`shelf_id` = `S`.`id` AND `M`.`type` = 0 "
				. "WHERE ";
		if (!$whr['catagory_id'] == '0') {
			$sql .= "`S`.`catagory_id` = {$whr['catagory_id']} ";
		}
		if (!$whr['supplier_id'] == '0') {
			$sql .= "AND `K`.`supplier_id` = {$whr['supplier_id']} ";
		}
		if (!$whr['brand_id'] == '0') {
			$sql .= "AND `P`.`brand_id` = {$whr['brand_id']} ";
		}
		if (!$whr['coin_style'] == '0') {
			$sql .= "AND `S`.`coin_style` > 0 ";
		}
		if (!$whr['limit_style'] == '0') {
			$sql .= "AND `S`.`limit_style` > 0 ";
		}
		if (!$whr['check_vip'] == '0') {
			$sql .= "AND `S`.`check_vip` > 0 ";
		}
		if (!$whr['is_alone'] == '0') {
			$sql .= "AND `S`.`is_alone` > 0 ";
		}
		$sql .= "ORDER BY `{$whr['order']}`"
				. "LIMIT 0,{$whr['num']}";
		return $sql;
	}

	/**
	 * 数据源测试
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		block_id			int		模板ID
	 * @request		page_id				int		页面ID
	 */
	public function do_preview(\Ziima\MVC\REST\Request $request) {
		$block_id = $request->body->block_id;
		return $this->get_preview($block_id);
	}

	private function get_preview($block_id) {
		$dir_cpl = Z_SITE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'runner' . DIRECTORY_SEPARATOR . 'c';
		$dir_tpl = Z_SITE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'runner' . DIRECTORY_SEPARATOR . 't';
		$dir_php = Z_SITE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'runner' . DIRECTORY_SEPARATOR . 'p';
		$dir_htm = Z_SITE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'runner' . DIRECTORY_SEPARATOR . 's';
		if (!file_exists($dir_cpl))
			mkdir($dir_cpl, 0755, true);
		if (!file_exists($dir_tpl))
			mkdir($dir_tpl, 0755, true);
		if (!file_exists($dir_php))
			mkdir($dir_php, 0755, true);
		if (!file_exists($dir_htm))
			mkdir($dir_htm, 0755, true);
		include_once Z_ROOT . '/Template.php';
		include_once Z_ROOT . '/Modifier.php';
		//1.建立模板引擎
		$te = new \Ziima\Template\TemplateEngine(
				$dir_tpl, $dir_cpl
		);
		//2.读出动态页的Block配置
		$block = \yuemi_main\RunBlockFactory::Instance()->load($block_id);
		if ($block == null) {
			return [
				'Html' => '',
				'__code' => 'OK',
				'__message' => ''
			];
		}
		//3.加载绑定组件
		$widget = \yuemi_main\RunWidgetFactory::Instance()->load($block->widget_id);
		if ($widget == null) {
			return [
				'Html' => '',
				'__code' => 'OK',
				'__message' => ''
			];
		}
		//3.1 把组件的模板存到文件
		file_put_contents($dir_tpl . DIRECTORY_SEPARATOR . $widget->id . '.tpl', $widget->template);
		//3.2 检查模板语法
		try {
			$te->compile($widget->id . '.tpl');
		} catch (\Exception $e) {
			throw new \Ziima\MVC\REST\Exception('E_TEMPLATE', $e->getMessage());
		}

		//4.加载绑定数据源
		$source = \yuemi_main\RunSourceFactory::Instance()->load($block->source_id);
		//4.1 SQL类型的数据源，直接取出
		if ($source === null) {
			$result = [];
		} elseif ($source->style == 0) {
			if ($source->type == 1)
				$result = $this->MySQL->row($source->driver);
			else
				$result = $this->MySQL->grid($source->driver);
			//4.2 PHP类型的数据源，放到临时PHP文件中，再包含回来，然后调用相应的函数即可
		} else if ($source->style == 1) {
			file_put_contents($dir_php . DIRECTORY_SEPARATOR . $source->id . '.php', "<?php\n");
			file_put_contents($dir_php . DIRECTORY_SEPARATOR . $source->id . '.php', "function _source_execute(\\Ziima\\Data\\MySQLConnection  \$mysql){\n", FILE_APPEND);
			file_put_contents($dir_php . DIRECTORY_SEPARATOR . $source->id . '.php', $source->driver . "\n", FILE_APPEND);
			file_put_contents($dir_php . DIRECTORY_SEPARATOR . $source->id . '.php', "}\n", FILE_APPEND);

			include $dir_php . DIRECTORY_SEPARATOR . $source->id . '.php';
			$result = _source_execute($this->MySQL);
		} else if ($source->style == 2) { // 商品
			$arrO = $type = explode(',', trim($source->driver, ','));
			$T = [];
			foreach ($arrO as $key => $val) {
				$T = explode('-', $val);
				$m = $this->MySQL->row("SELECT `file_url` AS Picture , `thumb_url` AS Thumb FROM `yuemi_sale`.`{$T[2]}_material` WHERE `id` = {$T[3]}");
				$k = $this->MySQL->row("SELECT `title` AS Title ,`price_sale` AS Sale FROM `yuemi_sale`.`{$T[0]}` WHERE `id` = {$T[1]}");
				$result[$key]['Id'] = $T[0];
				$result[$key]['Picture'] = $m['Picture'];
				$result[$key]['Thumb'] = $m['Thumb'];
				$result[$key]['Title'] = $k['Title'];
				$result[$key]['Price'] = $k['Sale'];
				$result[$key]['Url'] = 'https://a.yuemee.com/mobile.php?call=runer.page&id=' . $T[1];
			}
		} else if ($source->style == 3) { // 待定
		} else if ($source->style == 4) { // 专题
			$arrO = $type = explode(',', trim($source->driver, ','));
			$T = [];
			foreach ($arrO as $key => $val) {
				$T = explode('-', $val);
				$m = $this->MySQL->row("SELECT `file_url` AS Picture FROM `yuemi_main`.`run_material` WHERE `id` = {$T[2]}");
				$k = $this->MySQL->row("SELECT `name` AS Title FROM `yuemi_main`.`run_page` WHERE `id` = {$T[1]}");
				$result[$key]['Id'] = $T[0];
				$result[$key]['Picture'] = $m['Picture'];
				$result[$key]['Title'] = $k['Title'];
				$result[$key]['Url'] = 'https://a.yuemee.com/mobile.php?call=runer.page&id=' . $T[1];
			}
		}
		//4.2 调用模板引擎，渲染页面
		try {
			$html = $te->execute($widget->id . '.tpl', ['Data' => $result]);
		} catch (\Exception $e) {
			throw new \Ziima\MVC\REST\Exception('E_TEMPLATE', $e->getMessage());
		}
		return [
			'Html' => $html,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 生成页面
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function build_html(\Ziima\MVC\REST\Request $request) {
		$page = \yuemi_main\RunPageFactory::Instance()->load($request->body->id);

		$dir_cpl = Z_SITE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'runner' . DIRECTORY_SEPARATOR . 'c';
		$dir_tpl = Z_SITE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'runner' . DIRECTORY_SEPARATOR . 't';
		$dir_php = Z_SITE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'runner' . DIRECTORY_SEPARATOR . 'p';
		$dir_htm = Z_SITE . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'runner' . DIRECTORY_SEPARATOR . 's';
		if (!file_exists($dir_cpl))
			mkdir($dir_cpl, 0755, true);
		if (!file_exists($dir_tpl))
			mkdir($dir_tpl, 0755, true);
		if (!file_exists($dir_php))
			mkdir($dir_php, 0755, true);
		if (!file_exists($dir_htm))
			mkdir($dir_htm, 0755, true);
		include_once Z_ROOT . '/Template.php';
		include_once Z_ROOT . '/Modifier.php';
		$te = new \Ziima\Template\TemplateEngine(
				$dir_tpl, $dir_cpl
		);
		file_put_contents($dir_tpl . DIRECTORY_SEPARATOR . $page->id . '.tpl', $page->template);
		//4.2 调用模板引擎，渲染页面
		try {
			$html = $te->execute($page->id . '.tpl', []);
		} catch (\Exception $e) {
			throw new \Ziima\MVC\REST\Exception('E_TEMPLATE', $e->getMessage());
		}

		$sql = "SELECT `block_id` FROM `yuemi_main`.`run_usage` WHERE `page_id` = {$request->body->id}";
		$BlockIds = $this->MySQL->grid($sql);
		foreach ($BlockIds AS $val) {
			$html .= ($this->get_preview($val['block_id']))['Html'];
		}

		$sql = "SELECT * FROM `yuemi_main`.`run_release` WHERE `page_id` = {$request->body->id}";
		$re = $this->MySQL->row($sql);
		if (empty($re)) {
			$RunReleaseEntity = new \yuemi_main\RunReleaseEntity();
			$RunReleaseEntity->page_id = $request->body->id;
			$RunReleaseEntity->html = $html;
			\yuemi_main\RunReleaseFactory::Instance()->insert($RunReleaseEntity);
		} else {
			$RunReleaseEntity = \yuemi_main\RunReleaseFactory::Instance()->loadOneByPageId($request->body->id);
			$RunReleaseEntity->html = $html;
			\yuemi_main\RunReleaseFactory::Instance()->update($RunReleaseEntity);
		}
		return [
			'rid' => $RunReleaseEntity->id,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 获取HTML
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function get_re_html(\Ziima\MVC\REST\Request $request) {
		$release = \yuemi_main\RunReleaseFactory::Instance()->load($request->body->id);
		return [
			'Html' => $release->html,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 获取分类ID
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function get_catagory(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$sql = "SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = {$id}";
		$re = $this->MySQL->grid($sql);
		if (empty($re)) {
			return [
				'Re' => '',
				'__code' => 'OK',
				'__message' => ''
			];
		}
		return [
			'Re' => $re,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 对话框选商品
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		page_id		int			页码
	 * @request		search		string		关键词
	 */
	public function dlg_select_sku(\Ziima\MVC\REST\Request $request) {
		$sql = "SELECT `id` As Id," .
				"`title` As Title," .
				"'' As Picture " .
				"FROM `yuemi_sale`.`sku` ";
		$whr = [];
		$whr[] = " `status` = 2 ";
		if (strlen($request->body->search) > 0) {
			$whr[] = " `title` LIKE '%" . $this->MySQL->encode($request->body->search) . "%'";
		}
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}
		$t = $this->MySQL->paging($sql, 5, $request->body->page_id);
		foreach ($t->Data as &$item) {
			$sku = \yuemi_sale\SkuFactory::Instance()->load($item['Id']);
			$t0 = $this->MySQL->grid("SELECT 'sku' AS Albumn,`id` As Id,`file_url` AS Picture FROM `yuemi_sale`.`sku_material` WHERE `sku_id` = %d AND `type` = 0 AND `status` !=2", $item['Id']);
			$t1 = $this->MySQL->grid("SELECT 'spu' AS Albumn,`id` As Id,`file_url` AS Picture FROM `yuemi_sale`.`spu_material` WHERE `spu_id` = %d AND `type` = 0 AND `status` !=2", $sku->spu_id);

			$esku = \yuemi_sale\ExtSkuFactory::Instance()->loadOneBySkuId($sku->id);
			if ($esku !== null) {
				$t2 = $this->MySQL->grid("SELECT 'esku' AS Albumn,`id` As Id,`file_url` AS Picture FROM `yuemi_sale`.`ext_sku_material` WHERE `ext_sku_id` = %d AND `type` = 0", $esku->id);
				$t3 = $this->MySQL->grid("SELECT 'espu' AS Albumn,`id` As Id,`file_url` AS Picture FROM `yuemi_sale`.`ext_spu_material` WHERE `ext_spu_id` = %d AND `type` = 0", $esku->ext_spu_id);
			} else {
				$t2 = [];
				$t3 = [];
			}
			$item['Albumn'] = array_merge($t0, $t1, $t2, $t3);
			foreach ($item['Albumn'] as &$pic) {
				$pic['Picture'] = 'https://r.yuemee.com' . '/upload' . $pic['Picture'];
			}
		}
		return [
			'List' => $t->Data
		];
	}

	/**
	 * 对话框选商品
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		page_id		int			页码
	 */
	public function dlg_select_page(\Ziima\MVC\REST\Request $request) {
		$sql = "SELECT P.`id` AS Id,P.`name` AS Title "
				. " FROM `yuemi_main`.`run_page` AS P "
				. " WHERE P.`style` = 1 ";
		$data = $this->MySQL->paging($sql, 1, $request->body->page_id);
		foreach ($data->Data AS $key => $val) {
			$id = $val['Id'];
			$sql1 = "SELECT * FROM `yuemi_main`.`run_material` WHERE `page_id` = {$id}";
			$Pic = $this->MySQL->grid($sql1);
			foreach ($Pic AS $k => $v) {
				$data->Data[$key]['Albumn'][$k]['Picture'] = 'https://r.yuemee.com' . '/upload' . $v['file_url'];
				$data->Data[$key]['Albumn'][$k]['index'] = $v['id'];
			}
		}
		return [
			'List' => $data->Data
		];
	}

	/**
	 * 新增热搜
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function new_hot(\Ziima\MVC\REST\Request $request) {
//		$HotSearchEntity = new \yuemi_sale\HotSearchEntity();
//		$HotSearchEntity->title = $request->body->title;
//		$HotSearchEntity->color = $request->body->color;
//		$HotSearchEntity->size = (int)$request->body->size;
//		$HotSearchEntity->p_order = (int)$request->body->p_order;
//		$HotSearchFactory = new \yuemi_sale\HotSearchFactory(MYSQL_WRITER, MYSQL_READER);
//		$HotSearchFactory->insert($HotSearchEntity);
		$time = time();
		$this->MySQL->execute("INSERT INTO `yuemi_sale`.`hot_search` (title,color,size,p_order,create_time) ".
				"VALUES ('{$request->body->title}','{$request->body->color}',{$request->body->size},{$request->body->p_order},{$time})");
	
		return 'OK';
	}
	
	/**
	 * 删除热搜
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function del_hot(\Ziima\MVC\REST\Request $request){
		$this->MySQL->execute("DELETE FROM `yuemi_sale`.`hot_search` WHERE `id` = {$request->body->id}");
		return 'OK';
	}

}
