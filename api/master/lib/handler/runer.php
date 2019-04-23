<?php

include_once 'lib/ApiHandler.php';
include_once Z_SITE . '/../../_base/entity/yuemi_sale.php';

/**
 * 运营接口
 */
class runer_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 获取专题页面
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int		专题ID
	 */
	public function get_dpage(\Ziima\MVC\REST\Request $request){
		$page = \yuemi_main\RunPageFactory::Instance()->load($request->body->id);
		if ($page == null){
			throw new \Ziima\MVC\REST\Exception('E_SYSTEM', '系统关键数据丢失');
		}
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
		file_put_contents($dir_tpl . DIRECTORY_SEPARATOR . $page->id . '.tpl', $page->template);
		$te = new \Ziima\Template\TemplateEngine(
				$dir_tpl, $dir_cpl
		);
		$html = $te->execute($page->id . '.tpl', []);

		$sql = "SELECT `block_id` FROM `yuemi_main`.`run_usage` WHERE `page_id` = {$request->body->id}";
		$BlockIds =  $this->MySQL->grid($sql);
		foreach ($BlockIds AS $val){
			$html .= ($this->get_preview($val['block_id']))['Html'];
		}

		return[
			'title'=>$page->name,
			'html' => $html
		];
	}
	/**
	 * 获取页面
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		block_id		int		区块ID
	 */
	public function get_block_data(\Ziima\MVC\REST\Request $request) {
		$block = \yuemi_main\RunBlockFactory::Instance()->load($request->body->block_id);
		if ($block === null) {
			throw new \Ziima\MVC\REST\Exception('E_SYSTEM', '系统关键数据丢失');
		}
		$page = \yuemi_main\RunPageFactory::Instance()->load($block->page_id);
		if ($page === null) {
			throw new \Ziima\MVC\REST\Exception('E_SYSTEM', '系统关键数据丢失');
		}

		$source_id = $block->source_id;
		$source = \yuemi_main\RunSourceFactory::Instance()->load($source_id);
		if ($source === null) {
			return [];
		}
		$result = [];
		if ($source->style == 0) {
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
			$arrO = $type = explode(',', trim($source->driver,','));
			$T = [];
			foreach ($arrO as $key=>$val){
				$T = explode('-', $val);
				$table = '';
				if(count($T) != 4)
					continue;
				if ($T[2] == 'sku'){
					$table = 'sku';
				} else if ($T[2] == 'spu') {
					$table = 'spu';
				} else if ($T[2] == 'esku') {
					$table = 'ext_sku';
				} else if ($T[2] == 'espu') {
					$table = 'ext_spu';
				} else {
					continue;
				}
				$m = $this->MySQL->row("SELECT `file_url` AS Picture , `thumb_url` AS Thumb FROM `yuemi_sale`.`{$table}_material` WHERE `id` = {$T[3]}");
				if (empty($m)){
					$result[$key]['Picture'] = '';
				} else {
					$result[$key]['Picture'] = "https://r.yuemee.com/upload".$m['Picture'];
				}
				$k = $this->MySQL->row("SELECT `title` AS Title ,`price_sale` AS Sale ,`rebate_vip` AS VipRe,`specs` AS `Specs`,`price_market` AS `Market` ,`price_inv` AS Inv ,`depot` AS Depot FROM `yuemi_sale`.`sku` WHERE `id` = {$T[1]}");
				if (empty($k)){
					$result[$key]['Title'] = '';
					$result[$key]['Url'] = '';
					$result[$key]['Id'] = 0;
					$result[$key]['Depot'] = 0;
				} else {
					$result[$key]['Title'] = $k['Title'];
					$result[$key]['Id'] = $T[1];
					$result[$key]['Url'] = 'https://a.yuemee.com/mobile.php?call=runer.page&id='.$T[1];
					$result[$key]['Depot'] = $k['Depot'];
				}
				$result[$key]['Id'] = $T[1];
				$result[$key]['Picture'] = "https://r.yuemee.com/upload".$m['Picture'];
				$result[$key]['Thumb'] = "https://r.yuemee.com/upload".$m['Thumb'];
				$result[$key]['Title'] = $k['Title'];
				$invitor = $this->User->invitor_id;
				if($invitor > 0)
				{
					$result[$key]['Price'] = $k['Inv'];
				}
				$result[$key]['Price'] = $k['Sale'];
				$result[$key]['VipRe'] = $k['VipRe'];

				$result[$key]['Market'] = $k['Market'];
				$result[$key]['Specs'] = array_filter(explode("\n", $k['Specs']));
				$result[$key]['Url'] = 'https://a.yuemee.com/mobile.php?call=runer.item&id='.$T[1];
			}
		} else if ($source->style == 3) { // 待定
			return ;
		} else if ($source->style == 4) { // 专题
			$arrO = $type = explode(',', trim($source->driver,','));
			$T = [];
			foreach ($arrO as $key=>$val){
				$T = explode('-', $val);
				$m = $this->MySQL->row("SELECT `file_url` AS Picture FROM `yuemi_main`.`run_material` WHERE `id` = {$T[2]}");
				if (empty($m)){
					$result[$key]['Picture'] = '';
				} else {
					$result[$key]['Picture'] = "https://r.yuemee.com/upload".$m['Picture'];
				}
				$k = $this->MySQL->row("SELECT `name` AS Title FROM `yuemi_main`.`run_page` WHERE `id` = {$T[1]}");
				if (empty($k)){
					$result[$key]['Title'] = '';
					$result[$key]['Url'] = '';
					$result[$key]['Id'] = 0;
				} else {
					$result[$key]['Title'] = $k['Title'];
	//				$result[$key]['Title']  = $source->id . '|' . $source->style . '|' . $source->driver;
					$result[$key]['Id'] = $T[1];
					$result[$key]['Url'] = 'https://a.yuemee.com/mobile.php?call=runer.page&id='.$T[1];
				}

			}
		} else if ($source->style == 5){
			$iszt = $page->style;
				$max = $block->capacity; //数据容量
				$str = $source->driver;
//				$arr = explode('*', $str);
//				$ztid = $arr[0]; //专题id
//				$row = $this->MySQL->row("SELECT `template` FROM `yuemi_main`.`run_page` WHERE `id` = {$ztid}");
//				$template = $row['template']; //专题模板
				//分类
				$type = explode('*', $str);
				if ($type[0] == 'sp') {
					//商品
					$idlist = explode('|', $type[1]);
					$a = 1;
					foreach ($idlist as $k => $v) {
						if ($a > $max) {
							break;
						}
						$row = $this->getpic($v);
						if (!empty($row)) {
							$res[$k]['Picture'] = $row['Picture'];
						} else {
							$res[$k]['Picture'] = '';
						}

						$res[$k]['Id'] = $k;
						$res[$k]['Title'] = '';
						$res[$k]['Url'] = URL_API . '/mobile.php?call=runer.item&id=' . $v;
						$a++;
					}
					return [
						'data' => $res
					];
				} elseif ($type[0] == 'zt') {
					//专题
					$idlist = explode('|', $type[1]);
					$a = 1;
					foreach ($idlist as $k => $v) {
						if ($a > $max) {
							break;
						}
						$row = $this->get_page_info($v);
						if(!empty($row)){
							$res[$k]['Picture'] = $row['file_url'];
							$res[$k]['Title'] = $row['name'];
						}else{
							$res[$k]['Picture'] = '';
							$res[$k]['Title'] = '';
						}
						$res[$k]['Id'] = $k;
						$res[$k]['Url'] = URL_API . '/mobile.php?call=runer.page&id=' . $v;

						$a++;
					}
					return [
						'data' => $res
					];
				}
		} else if ($source->style == 6 ) {
			$template = $page->template;
				$choid = $block->page_id;
				$url = URL_API . '/mobile.php?call=runer.page&id=' . $choid;
				return [
					'Template' => $template,
					'Pageid' => $choid,
					'URL' => $url
				];
		} else if ($source->style == 7){
			$ur = URL_RES;
				$res = [];
				$template = $page->template;
				$choid = $block->page_id;
				$str = $source->driver;
				$idlist = explode('|', $str);
				$max = $block->capacity; //数据容量
				$url = URL_API . '/mobile.php?call=runer.page&id=' . $choid;
				$a = 1;
				foreach ($idlist as $k => $v) {
					if ($a > $max) {
						break;
					}
					$row = $this->MySQL->row("SELECT CONCAT('{$ur}','/upload',`file_url`) AS `file_url` FROM `yuemi_main`.`run_material` WHERE `id` = {$v}");
					if (!empty($row)) {
						$res['img'][$k] = $row['file_url'];
					} else {
						$res['img'][$k] = '';
					}

					$a++;
				}
				return [
					'Template' => $template,
					'Pageid' => $choid,
					'URL' => $url,
					'Picture' => $res
				];
		} else if ($source->style == 8 ){
			$skuid = $source->driver;
				$get = $this->getpic($skuid);
				$url = $skuid;
				$get['Url'] = $url;
				$get['Title'] = '';
				$get['Id'] = 0;
				return [
					'data' => $get
				];
		}
		return [
			'type' => $source->style,
			'data' => $result,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	private function get_page_info($pageid) {
		$ur = URL_RES;
		$row = $this->MySQL->row("SELECT `pg`.*,CONCAT('{$ur}','/upload',`rm`.`file_url`) AS `file_url` FROM `yuemi_main`.`run_page` AS `pg` " .
				"LEFT JOIN `yuemi_main`.`run_material` AS `rm` ON `rm`.`page_id` = `pg`.`id` WHERE `pg`.`id` = {$pageid} ORDER BY `id` LIMIT 0,1 ");
		return $row;
	}

	/**
	 * 获取模板
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function get_template(\Ziima\MVC\REST\Request $request) {
		$block = \yuemi_main\RunBlockFactory::Instance()->load($request->body->block_id);
		$res = $this->get_preview(7);
		var_dump($res);
		die;
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

	private function getpic($v) {
		$ur = URL_RES;
		$imgs = [];
		//SKU素材
		if (empty($imgs)) {
			$lis = $this->MySQL->row("SELECT CONCAT('{$ur}','/upload',`file_url`) AS `Picture`,`file_url` FROM `yuemi_sale`.`sku_material` WHERE `sku_id` = {$v} AND `is_default` = 1 ORDER BY `is_default` DESC");
			$imgs = array_merge($imgs, $lis);
		}
		//SPU素材
		if (empty($imgs)) {
			$lis = $this->MySQL->row("SELECT CONCAT('{$ur}','/upload',`spu`.`file_url`) AS `Picture`,`spu`.`file_url` FROM `yuemi_sale`.`spu_material` AS spu " .
					"LEFT JOIN `yuemi_sale`.`sku` AS sku ON sku.spu_id = spu.spu_id " .
					"WHERE `sku`.`id` = {$v} AND `spu`.`is_default` = 1 ORDER BY `spu`.`is_default` DESC");
			$imgs = array_merge($imgs, $lis);
		}

		//ext_sku素材
		if (empty($imgs)) {
			$lis = $this->MySQL->row(
					"SELECT CONCAT('{$ur}','/upload',`ekm`.`file_url`) AS `Picture`,ekm.`file_url` FROM `yuemi_sale`.`ext_sku_material` AS ekm " .
					"LEFT JOIN `yuemi_sale`.`ext_sku` AS ek ON ekm.ext_sku_id = ek.id " .
					"WHERE ek.sku_id = {$v} AND `ekm`.`is_default` = 1 ORDER BY `ekm`.`is_default` DESC");
			$imgs = array_merge($imgs, $lis);
		}
		//ext_spu素材
		if (empty($imgs)) {
			$spuid = $this->MySQL->scalar("SELECT spu_id FROM `yuemi_sale`.`sku` WHERE id = " . $v);

			$lis = $this->MySQL->row(
					"SELECT CONCAT('{$ur}','/upload',`epm`.`file_url`) AS `Picture`,epm.`file_url` FROM `yuemi_sale`.`ext_spu_material` AS epm " .
					"LEFT JOIN `yuemi_sale`.`ext_spu` AS ep ON ep.spu_id = epm.ext_spu_id " .
					"WHERE `ep`.`spu_id` = {$spuid} AND epm.is_default ORDER BY `epm`.`is_default` DESC"
			);
			$imgs = array_merge($imgs, $lis);
		}
		return $imgs;
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
		if($block == null){
			return [
				'Html' => '',
				'__code' => 'OK',
				'__message' => ''
			];
		}
		//3.加载绑定组件
		$widget = \yuemi_main\RunWidgetFactory::Instance()->load($block->widget_id);
		if($widget == null){
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
		if($source === null){
			$result = [];
		}elseif ($source->style == 0) {
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
			$arrO = $type = explode(',', trim($source->driver,','));
			$T = [];
			foreach ($arrO as $key=>$val){
				$T = explode('-', $val);
				$m = $this->MySQL->row("SELECT `file_url` AS Picture , `thumb_url` AS Thumb FROM `yuemi_sale`.`{$T[2]}_material` WHERE `id` = {$T[3]}");
				if (empty($m)){
					continue;
				}
				$k = $this->MySQL->row("SELECT `title` AS Title ,`price_sale` AS Sale ,`price_market` AS Market ,`price_inv` AS	Inv,`rebate_vip` AS VipRe FROM `yuemi_sale`.`{$T[0]}` WHERE `id` = {$T[1]} AND `status` = 2 ");
				if (empty($k)){
					continue;
				}
				$result[$key]['Id'] = $T[0];
				$result[$key]['Picture'] = $m['Picture'];
				$result[$key]['Thumb'] = $m['Thumb'];
				$result[$key]['Title'] = $k['Title'];
				if($this->User->level_v > 0){
					$result[$key]['VipRe'] = round($k['VipRe'],2);
				}else{
					$result[$key]['VipRe'] = 0;
				}
				if($this->User->invitor_id > 0)
				{
					$result[$key]['Price'] = $k['Inv'];
				}else{
					$result[$key]['Price'] = $k['Sale'];
				}
				
				$result[$key]['Inv'] = $k['Inv'];
				$result[$key]['Market'] = $k['Market'];
				$result[$key]['Url'] = 'https://a.yuemee.com/mobile.php?call=runer.item&id='.$T[1];
			}
		} else if ($source->style == 3) { // 待定
			$result = [];
		} else if ($source->style == 4) { // 专题
			$arrO = $type = explode(',', trim($source->driver,','));
			$T = [];
			foreach ($arrO as $key=>$val){
				$T = explode('-', $val);
				$m = $this->MySQL->row("SELECT `file_url` AS Picture FROM `yuemi_main`.`run_material` WHERE `id` = {$T[2]}");
				$k = $this->MySQL->row("SELECT `name` AS Title FROM `yuemi_main`.`run_page` WHERE `id` = {$T[1]}");
				$result[$key]['Id'] = $T[0];
				$result[$key]['Picture'] = $m['Picture'];
				$result[$key]['Title'] = $k['Title'];
				$result[$key]['Url'] = 'https://a.yuemee.com/mobile.php?call=runer.page&id='.$T[1];
			}
		}
		//4.2 调用模板引擎，渲染页面
		try {
			$html = $te->execute($widget->id . '.tpl', ['Data' => $result]);
		} catch (\Exception $e) {
			throw new \Ziima\MVC\REST\Exception('E_TEMPLATE', $e->getMessage());
		}
		file_put_contents($dir_htm . DIRECTORY_SEPARATOR . $widget->id . '.html', $html);
		return [
			'Html' => $html,
			'__code' => 'OK',
			'__message' => ''
		];
	}


	/**
	 * 从数据源获取数据
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int		数据源ID
	 */
	public function get_source_data(\Ziima\MVC\REST\Request $request){
		$source_id = $request->body->id;
		$source = \yuemi_main\RunSourceFactory::Instance()->load($source_id);
		$driver = $source->driver;
		$style = $source->style;
		if ($style == 0){ // mysql
			$result = $this->MySQL->row($driver);
		} else if ($style == 1) { // php
			$num = $request->body->number;
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
			$te = new \Ziima\Template\TemplateEngine(
					$dir_tpl, $dir_cpl
			);
			file_put_contents($dir_tpl . DIRECTORY_SEPARATOR . $source->id . '.tpl', $source->driver);
			try {
				$te->compile($source->id . '.tpl');
			} catch (\Exception $e) {
				throw new \Ziima\MVC\REST\Exception('E_TEMPLATE', $e->getMessage());
			}
			//检查模板语法
			file_put_contents($dir_php . DIRECTORY_SEPARATOR . $source->id . '.php', "<?php\n");
			file_put_contents($dir_php . DIRECTORY_SEPARATOR . $source->id . '.php', "function _source_execute(\\Ziima\\Data\\MySQLConnection  \$mysql,array \$args){\n", FILE_APPEND);
			file_put_contents($dir_php . DIRECTORY_SEPARATOR . $source->id . '.php', $source->driver . "\n", FILE_APPEND);
			file_put_contents($dir_php . DIRECTORY_SEPARATOR . $source->id . '.php', "}\n", FILE_APPEND);

			include $dir_php . DIRECTORY_SEPARATOR . $source->id . '.php';
			$result = _source_execute($this->MySQL, ['num' => $num]);
		} else if ($style == 2) { // 商品
			$arrO = $type = explode(',', trim($driver,','));
			$T = [];
			foreach ($arrO as $key=>$val){
				$T = explode('-', $val);
				$m = $this->MySQL->row("SELECT `file_url` AS Picture , `thumb_url` AS Thumb FROM `yuemi_sale`.`{$T[2]}_material` WHERE `id` = {$T[3]}");
				$k = $this->MySQL->row("SELECT `title` AS Title ,`price_sale` AS Sale FROM `yuemi_sale`.`{$T[0]}` WHERE `id` = {$T[1]}");
				$result[$key]['Id'] = $T[0];
				$result[$key]['Picture'] = $m['Picture'];
				$result[$key]['Thumb'] = $m['Thumb'];
				$result[$key]['Title'] = $k['Title'];
				$result[$key]['Price'] = $k['Sale'];
				$result[$key]['Url'] = 'https://a.yuemee.com/mobile.php?call=runer.page&id='.$T[1];
			}
		} else if ($style == 3) { // 待定

		} else if ($style == 4) { // 专题
			$arrO = $type = explode(',', trim($driver,','));
			$T = [];
			foreach ($arrO as $key=>$val){
				$T = explode('-', $val);
				$m = $this->MySQL->row("SELECT `file_url` AS Picture FROM `yuemi_main`.`run_material` WHERE `id` = {$T[2]}");
				$k = $this->MySQL->row("SELECT `name` AS Title FROM `yuemi_main`.`run_page` WHERE `id` = {$T[1]}");
				$result[$key]['Id'] = $T[0];
				$result[$key]['Picture'] = $m['Picture'];
				$result[$key]['Title'] = $k['Title'];
				$result[$key]['Url'] = 'https://a.yuemee.com/mobile.php?call=runer.page&id='.$T[1];
			}
		}
		return [
			'Result' => $result,
			'__code' => 'OK',
			'__message' => ''
		];
	}


	/**
	 * 热搜
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function hotsearch(\Ziima\MVC\REST\Request $request){
		$list = $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`hot_search`  ORDER BY `p_order` LIMIT 10 ");
		return [
			'Result' => $list,
			'__code' => 'OK',
			'__message' => ''
		];
	}

}
