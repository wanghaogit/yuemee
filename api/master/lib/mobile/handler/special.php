<?php
include Z_SITE . "/lib/MobileHandler.php";

/**
 * 专题
 */
class special_handler extends MobileHandler
{
	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 首页
	 * http://a.ym.cn/mobile.php?call=special.index
	 */
	public function index()
	{
		
	}

	/**
	 * 专题详情
	 * @param int $id
	 */
	public function detail(int $id = 0)
	{
		$page = \yuemi_main\RunPageFactory::Instance()->load($id);
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

		$sql = "SELECT `block_id` FROM `yuemi_main`.`run_usage` WHERE `page_id` = {$id}";
		$BlockIds =  $this->MySQL->grid($sql);
		foreach ($BlockIds AS $val){
			$html .= ($this->get_preview($val['block_id']))['Html'];
		}
		return[
			'title'=>$page->name,
			'html' => $html
		];
		
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
				$k = $this->MySQL->row("SELECT `title` AS Title ,`price_sale` AS Sale ,`price_market` AS Market FROM `yuemi_sale`.`{$T[0]}` WHERE `id` = {$T[1]}");
				if (empty($k)){
					continue;
				}
				$result[$key]['Id'] = $T[0];
				$result[$key]['Picture'] = $m['Picture'];
				$result[$key]['Thumb'] = $m['Thumb'];
				$result[$key]['Title'] = $k['Title'];
				$result[$key]['Price'] = $k['Sale'];
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
	
}
