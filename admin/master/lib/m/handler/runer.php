<?php

include "lib/AdminHandler.php";

/**
 * 运营管理
 * @auth
 */
class runer_handler extends AdminHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	public function index(int $p = 0) {
		
	}

	/**
	 * 应用内--静态
	 * @param int $p
	 */
	public function spage(int $p = 0, int $parent_id = 0) {
		$sql = "SELECT `id`,`parent_id`,`name`,`alias` FROM `yuemi_main`.`run_page` WHERE `style` = 0 AND `id` = {$parent_id}";
		$clobj = null;
		if ($parent_id > 0) {
			$clobj = $this->MySQL->row($sql);
		}
		$sql1 = "SELECT `id`,`parent_id`,`name`,`alias` FROM `yuemi_main`.`run_page` WHERE `style` = 0 AND `parent_id` = {$parent_id}";
		$Result = $this->MySQL->paging($sql1, 30, $p);
		return[
			'parent_id' => $parent_id,
			'ParentCatagory' => $clobj,
			'Result' => $Result,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 应用内--静态--模块创建
	 * @param int $page_id
	 */
	public function spage_block_create(int $page_id = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$RunBlockFactory = new \yuemi_main\RunBlockFactory(MYSQL_WRITER, MYSQL_READER);
			$RunBlockEntity = new \yuemi_main\RunBlockEntity();
			$RunBlockEntity->alias = $this->MySQL->encode($_POST['alias']) ?? '';
			$RunBlockEntity->name = $this->MySQL->encode($_POST['name']) ?? '';
			$RunBlockEntity->page_id = $page_id;
			$RunBlockEntity->preview = '';
			$RunBlockEntity->source_type = intval($_POST['source_type']);
			$RunBlockEntity->sizer = intval($_POST['sizer']);
			$RunBlockEntity->width = intval($_POST['width']);
			$RunBlockEntity->height = intval($_POST['height']);
			$RunBlockEntity->capacity = intval($_POST['capacity']);
			$RunBlockEntity->thumbnail = '';
			if (!$RunBlockFactory->insert($RunBlockEntity)) {
				throw new \Exception('插入表RunPage失败！');
			}
			throw new \Ziima\MVC\Redirector('/index.php?call=runer.spage_block&page_id=' . $page_id);
		}
		$sql1 = "SELECT	`name` FROM `yuemi_main`.`run_page` WHERE `id` = {$page_id}";
		$name = ($this->MySQL->row($sql1))['name'];
		return[
			'page_name' => $name,
			'page_id' => $page_id,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 应用内--静态--模块
	 * @param int $p
	 */
	public function spage_block(int $p = 0, int $page_id = 0) {
		$sql = "SELECT `b`.*,`s`.`name` AS `source_name` "
				. "FROM `yuemi_main`.`run_block` AS `b` "
				. "LEFT JOIN `yuemi_main`.`run_source` AS `s` ON `b`.`source_id` = `s`.`id` "
				. "WHERE `b`.`page_id` = {$page_id}";
		$Result = $this->MySQL->paging($sql, 30, $p);
		$sql1 = "SELECT	`name`,`parent_id` FROM `yuemi_main`.`run_page` WHERE `id` = {$page_id}";
		$name = ($this->MySQL->row($sql1))['name'];
		$parent_id = ($this->MySQL->row($sql1))['parent_id'];
		return[
			'page_name' => $name,
			'page_id' => $page_id,
			'parent_id' => $parent_id,
			'Result' => $Result,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 应用内--静态--创建
	 * @param int $id
	 */
	public function spage_block_update(int $id = 0, int $page_id = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$RunBlockFactory = new \yuemi_main\RunBlockFactory(MYSQL_WRITER, MYSQL_READER);
			$RunBlockEntity = $RunBlockFactory->load($id);
			$RunBlockEntity->alias = $this->MySQL->encode($_POST['alias']);
			$RunBlockEntity->name = $this->MySQL->encode($_POST['name']);
			$RunBlockEntity->source_type = intval($_POST['source_type']);
			$RunBlockEntity->sizer = intval($_POST['sizer']);
			$RunBlockEntity->width = intval($_POST['width']);
			$RunBlockEntity->height = intval($_POST['height']);
			$RunBlockEntity->capacity = intval($_POST['capacity']);
			$RunBlockEntity->thumbnail = '';
			if (!$RunBlockFactory->update($RunBlockEntity)) {
				throw new \Exception('插入表RunPage失败！');
			}
			throw new \Ziima\MVC\Redirector('/index.php?call=runer.spage_block&page_id=' . $page_id);
		}
		$Sql = "SELECT * FROM `yuemi_main`.`run_block` WHERE `id` = {$id}";
		$Re = $this->MySQL->row($Sql);
		return[
			'page_id' => $page_id,
			'Result' => $Re,
			'id' => $id,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 专题--动态
	 * @param int $p
	 * @param int $parent_id
	 */
	public function dpage(int $p = 0, int $parent_id = 0) {
		$sql = "SELECT `id`,`parent_id`,`name`,`alias` FROM `yuemi_main`.`run_page` WHERE `style` = 1 AND `id` = {$parent_id}";
		$clobj = null;
		if ($parent_id > 0) {
			$clobj = $this->MySQL->row($sql);
		}
		$sql1 = "SELECT `id`,`parent_id`,`name`,`alias` FROM `yuemi_main`.`run_page` WHERE `style` = 1 AND `parent_id` = {$parent_id}";
		$Result = $this->MySQL->paging($sql1, 30, $p);
		return[
			'parent_id' => $parent_id,
			'ParentCatagory' => $clobj,
			'Result' => $Result,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 专题--动态--模块
	 * @param int $p
	 */
	public function dpage_block(int $p = 0, int $page_id = 0) {
		$sql = "SELECT `b`.*,`s`.`name` AS `source_name`,`w`.`name` AS `widget_name` "
				. "FROM `yuemi_main`.`run_block` AS `b` "
				. "LEFT JOIN `yuemi_main`.`run_source` AS `s` ON `b`.`source_id` = `s`.`id` "
				. "LEFT JOIN `yuemi_main`.`run_widget` AS `w` ON `b`.`widget_id` = `w`.`id` "
				. "WHERE `b`.`page_id` = {$page_id}";
		$Result = $this->MySQL->paging($sql, 30, $p);
		$sql1 = "SELECT	`name`,`parent_id` FROM `yuemi_main`.`run_page` WHERE `id` = {$page_id}";
		$name = ($this->MySQL->row($sql1))['name'];
		$parent_id = ($this->MySQL->row($sql1))['parent_id'];
		return[
			'page_name' => $name,
			'page_id' => $page_id,
			'parent_id' => $parent_id,
			'Result' => $Result,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 专题--动态--模块创建
	 * @param int $page_id
	 */
	public function dpage_block_create(int $page_id = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$RunBlockFactory = new \yuemi_main\RunBlockFactory(MYSQL_WRITER, MYSQL_READER);
			$RunBlockEntity = new \yuemi_main\RunBlockEntity();
			$RunBlockEntity->alias = intval($_POST['alias']) ?? '';
			$RunBlockEntity->name = intval($_POST['name']) ?? '';
			$RunBlockEntity->page_id = $page_id;
			$RunBlockEntity->preview = '';
			$RunBlockEntity->source_type = intval($_POST['source_type']);
			$RunBlockEntity->sizer = intval($_POST['sizer']);
			$RunBlockEntity->width = intval($_POST['width']);
			$RunBlockEntity->height = intval($_POST['height']);
			$RunBlockEntity->capacity = intval($_POST['capacity']);
			$RunBlockEntity->thumbnail = '';
			if (!$RunBlockFactory->insert($RunBlockEntity)) {
				throw new \Exception('插入表RunPage失败！');
			}
			$RunUsageEntity = new \yuemi_main\RunUsageEntity();
			$RunUsageEntity->block_id = $RunBlockEntity->id;
			$RunUsageEntity->page_id = $page_id;
			\yuemi_main\RunUsageFactory::Instance()->insert($RunUsageEntity);
			throw new \Ziima\MVC\Redirector('/index.php?call=runer.spage_block&page_id=' . $page_id);
		}
		$sql1 = "SELECT	`name` FROM `yuemi_main`.`run_page` WHERE `id` = {$page_id}";
		$name = ($this->MySQL->row($sql1))['name'];
		return[
			'page_name' => $name,
			'page_id' => $page_id,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 应用内--静态--创建
	 * @param int $id
	 */
	public function dpage_block_update(int $id = 0, int $page_id = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$RunBlockFactory = new \yuemi_main\RunBlockFactory(MYSQL_WRITER, MYSQL_READER);
			$RunBlockEntity = $RunBlockFactory->load($id);
			$RunBlockEntity->alias = intval($_POST['alias']);
			$RunBlockEntity->name = $this->MySQL->encode($_POST['name']);
			$RunBlockEntity->source_type = intval($_POST['source_type']);
			$RunBlockEntity->sizer = intval($_POST['sizer']);
			$RunBlockEntity->width = intval($_POST['width']);
			$RunBlockEntity->height = intval($_POST['height']);
			$RunBlockEntity->capacity = intval($_POST['capacity']);
			$RunBlockEntity->thumbnail = '';
			if (!$RunBlockFactory->update($RunBlockEntity)) {
				throw new \Exception('插入表RunPage失败！');
			}
			throw new \Ziima\MVC\Redirector('/index.php?call=runer.spage_block&page_id=' . $page_id);
		}
		$Sql = "SELECT * FROM `yuemi_main`.`run_block` WHERE `id` = {$id}";
		$Re = $this->MySQL->row($Sql);
		return[
			'page_id' => $page_id,
			'Result' => $Re,
			'id' => $id,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 专题--动态--创建
	 * @param int $parent_id
	 */
	public function dpage_create(int $parent_id = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$RunPageFactory = new \yuemi_main\RunPageFactory(MYSQL_WRITER, MYSQL_READER);
			$RunPageEntity = new \yuemi_main\RunPageEntity();
			$RunPageEntity->alias = intval($_POST['alias']) ?? '';
			$RunPageEntity->name = $this->MySQL->encode($_POST['name']) ?? '';
			$RunPageEntity->parent_id = $parent_id;
			$RunPageEntity->template = $this->MySQL->encode($_POST['template']) ?? '';
			$RunPageEntity->style = 1;
			if (!$RunPageFactory->insert($RunPageEntity)) {
				throw new \Exception('插入表RunPage失败！');
			}
			throw new \Ziima\MVC\Redirector('/index.php?call=runer.dpage&parent_id=' . $parent_id);
		}
		return[
			'parent_id' => $parent_id,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 专题--动态--编辑
	 * @param int $id
	 */
	public function dpage_update(int $id = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$RunPageFactory = new \yuemi_main\RunPageFactory(MYSQL_WRITER, MYSQL_READER);
			$RunPageEntity = $RunPageFactory->load($id);
			$RunPageEntity->alias = intval($_POST['alias']);
			$RunPageEntity->name = $this->MySQL->encode($_POST['name']);
			$RunPageEntity->template = str_replace("\n\r","",$_POST['template']); 
			if (!$RunPageFactory->update($RunPageEntity)) {
				throw new \Exception('插入表RunPage失败！');
			}
			throw new \Ziima\MVC\Redirector('/index.php?call=runer.dpage&parent_id=' . $RunPageEntity->parent_id);
		}
		$img = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`run_material` WHERE `page_id` = {$id}");
		$Sql = "SELECT * FROM `yuemi_main`.`run_page` WHERE `id` = {$id}";
		$Re = $this->MySQL->row($Sql);
		return[
			'Result' => $Re,
			'id' => $id,
			'img' => $img,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	public function widget(int $p = 0) {
		$sql = "SELECT * FROM `yuemi_main`.`run_widget`";
		$Result = $this->MySQL->paging($sql, 30, $p);
		return [
			'Result' => $Result
		];
	}

	/**
	 * 数据源--添加
	 */
	public function widget_create(int $block_id = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$RunWidgetFactory = new \yuemi_main\RunWidgetFactory(MYSQL_WRITER, MYSQL_READER);
			$RunWidgetEntity = new \yuemi_main\RunWidgetEntity();
			$RunWidgetEntity->name = $this->MySQL->encode($_POST['name']) ?? '';
			$RunWidgetEntity->alias = intval($_POST['alias']) ?? '';
			$RunWidgetEntity->source_type = intval($_POST['source_type']) ?? 0;
			$RunWidgetEntity->sizer = intval($_POST['sizer']) ?? 0;
			$RunWidgetEntity->width = intval($_POST['width']) ?? 0;
			$RunWidgetEntity->height = intval($_POST['height']) ?? 0;
			$RunWidgetEntity->capacity = intval($_POST['capacity']) ?? 0;
			$RunWidgetEntity->template = intval($_POST['template']) ?? 0;
			if (!$RunWidgetFactory->insert($RunWidgetEntity)) {
				throw new \Exception('插入表RunWidget失败！');
			}
			if ($block_id != 0) {
				$RunBlockEntity = \yuemi_main\RunBlockFactory::Instance()->load($block_id);
				$RunBlockEntity->widget_id = $RunWidgetEntity->id;
				\yuemi_main\RunBlockFactory::Instance()->update($RunBlockEntity);
			}
			throw new \Ziima\MVC\Redirector('/index.php?call=runer.widget');
		}
		return[
			'block_id' => $block_id,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 数据源--添加
	 */
	public function widget_update(int $widget_id = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$RunWidgetFactory = new \yuemi_main\RunWidgetFactory(MYSQL_WRITER, MYSQL_READER);
			$RunWidgetEntity = $RunWidgetFactory->load($widget_id);
			$RunWidgetEntity->name = $this->MySQL->encode($_POST['name']) ?? '';
			$RunWidgetEntity->alias = $_POST['alias'];
			$RunWidgetEntity->source_type = intval($_POST['source_type']) ?? 0;
			$RunWidgetEntity->sizer = intval($_POST['sizer']) ?? 0;
			$RunWidgetEntity->width = intval($_POST['width']) ?? 0;
			$RunWidgetEntity->height = intval($_POST['height']) ?? 0;
			$RunWidgetEntity->capacity = intval($_POST['capacity']) ?? 0;
			$RunWidgetEntity->template = $_POST['template'];
			if (!$RunWidgetFactory->update($RunWidgetEntity)) {
				throw new \Exception('插入表RunWidget失败！');
			}
			throw new \Ziima\MVC\Redirector('/index.php?call=runer.widget');
		}
		$Sql = "SELECT * FROM `yuemi_main`.`run_widget` WHERE `id` = {$widget_id}";
		$Re = $this->MySQL->row($Sql);
		return[
			'widget_id' => $widget_id,
			'Result' => $Re,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 查看数据源
	 * @param int $p
	 * @param int $page_id
	 */
	public function source(int $p = 0, int $page_id = 0, string $w = '') {
		if ($page_id == 0) {
			$sql = "SELECT * FROM `yuemi_main`.`run_source`";
			$name = '';
		} else {
			$sql = "SELECT `s`.*,`b`.`name` AS `block_name` FROM `yuemi_main`.`run_source` AS `s` "
					. "LEFT JOIN `yuemi_main`.`run_block` AS `b` ON `s`.`id` = `b`.`page_id` "
					. "WHERE `s`.`id` IN (SELECT `source_id` FROM `yuemi_main`.`run_usage` WHERE `page_id` = {$page_id})";
			$sql1 = "SELECT `name` FROM `yuemi_main`.`run_page` WHERE `id` = {$page_id}";
			$name = ($this->MySQL->row($sql1))['name'];
		}
		$Result = $this->MySQL->paging($sql, 30, $p);
		return [
			'where' => $w,
			'page_name' => $name,
			'page_id' => $page_id,
			'Result' => $Result
		];
	}

	/**
	 * 数据源--添加
	 */
	public function source_create(int $page_id = 0, int $block_id = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$RunSourceFactory = new \yuemi_main\RunSourceFactory(MYSQL_WRITER, MYSQL_READER);
			$RunSourceEntity = new \yuemi_main\RunSourceEntity();
			$RunSourceEntity->alias = intval($_POST['alias']);
			$RunSourceEntity->name = $this->MySQL->encode($_POST['name']);
			$RunSourceEntity->driver = $this->MySQL->encode($_POST['driver']);
			$RunSourceEntity->style = intval($_POST['style']);
			$RunSourceEntity->type = intval($_POST['type']);
			if (!$RunSourceFactory->insert($RunSourceEntity)) {
				throw new \Exception('插入表RunPage失败！');
			}
			if ($page_id !== 0) {
				
			}
			if ($block_id != 0) {
				$RunBlockEntity = \yuemi_main\RunBlockFactory::Instance()->load($block_id);
				$RunBlockEntity->source_id = $RunSourceEntity->id;
				\yuemi_main\RunBlockFactory::Instance()->update($RunBlockEntity);
			}
			throw new \Ziima\MVC\Redirector('/index.php?call=runer.source');
		}
		$sql1 = "SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = 0";
		$ReC = $this->MySQL->grid($sql1);
		$sql2 = "SELECT * FROM `yuemi_main`.`supplier`";
		$ReS = $this->MySQL->grid($sql2);
		$sql3 = "SELECT * FROM `yuemi_main`.`brand`";
		$ReB = $this->MySQL->grid($sql3);
		$sql4 = "SELECT * FROM `yuemi_main`.`run_block`";
		$ReRB = $this->MySQL->grid($sql4);
		$ress = $this->MySQL->grid("SELECT `title`,`id` FROM `yuemi_sale`.`sku`");
		$imgss = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`run_material`");
		$ztlist = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`run_page` WHERE `style` = 1 ");
		return[
			'ztlist' => $ztlist,
			'img' => $imgss,
			'ReB' => $ReB,
			'ReS' => $ReS,
			'ReC' => $ReC,
			'ReRB' => $ReRB,
			'res' => $ress,
			'block_id' => $block_id,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	/**
	 * 数据源--添加
	 */
	public function source_update(int $id, int $page_id = 0, int $block_id = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$RunSourceFactory = new \yuemi_main\RunSourceFactory(MYSQL_WRITER, MYSQL_READER);
			$RunSourceEntity = $RunSourceFactory->load($id);
			$RunSourceEntity->alias = $this->MySQL->encode($_POST['alias']);
			$RunSourceEntity->name = $this->MySQL->encode($_POST['name']);
			$RunSourceEntity->driver = $this->MySQL->encode($_POST['driver']);
			$RunSourceEntity->style = intval($_POST['style']);
			$RunSourceEntity->type = intval($_POST['type']);
			if (!$RunSourceFactory->update($RunSourceEntity)) {
				throw new \Exception('插入表RunPage失败！');
			}
			throw new \Ziima\MVC\Redirector('/index.php?call=runer.source');
		}
		$sql = "SELECT * FROM `yuemi_main`.`run_source` WHERE `id` = {$id}";
		$Result = $this->MySQL->row($sql);
		$sql1 = "SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = 0";
		$ReC = $this->MySQL->grid($sql1);
		$sql2 = "SELECT * FROM `yuemi_main`.`supplier`";
		$ReS = $this->MySQL->grid($sql2);
		$sql3 = "SELECT * FROM `yuemi_main`.`brand`";
		$ReB = $this->MySQL->grid($sql3);
		return[
			'ReB' => $ReB,
			'ReS' => $ReS,
			'ReC' => $ReC,
			'id' => $id,
			'Result' => $Result,
			'page_id' => $page_id,
			'block_id' => $block_id,
			'__code' => 'OK',
			'__message' => ''
		];
	}

	public function release(int $p = 0) {
		
	}

	private function get_str(int $cid) {
		$arr = [];
		while ($cid) {
			$arr[] = ($this->get_catagory($cid))['str'];
			$cid = ($this->get_catagory($cid))['pid'];
		}
		$count = count($arr);
		$str = '';
		for ($i = $count; $i > 0; $i--) {
			$str .= $arr[$i - 1];
		}
		return $str;
	}

	private function get_catagory(int $cid) {
		$sql = "SELECT * FROM `yuemi_sale`.`catagory` WHERE `id` = {$cid}";
		$re = $this->MySQL->row($sql);
		$pid = $re['parent_id'];
		$sql1 = "SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = {$pid}";
		$re1 = $this->MySQL->grid($sql1);
		$sql2 = "SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = {$cid}";
		if (empty($this->MySQL->row($sql2))) {
			$str = '<select onchange="get_catagory(this.value,this)" name="catagory_id" id="catagory_id" style="width:100px;background: white;">';
		} else {
			$str = '<select onchange="get_catagory(this.value,this)" style="width:100px;background: white;">';
		}
		foreach ($re1 as $val) {
			if ($val['id'] == $cid) {
				$str .= '<option value="' . $val['id'] . '" selected="selected" >' . $val['name'] . '</option>';
			} else {
				$str .= '<option value="' . $val['id'] . '">' . $val['name'] . '</option>';
			}
		}
		$str .= '</select>';
		return [
			'pid' => $pid,
			'str' => $str
		];
	}

	private function getpic($v) {
		$imgs = [];
		//SKU素材
		if (empty($imgs)) {
			$lis = $this->MySQL->row("SELECT `thumb_url`,`file_url` FROM `yuemi_sale`.`sku_material` WHERE `sku_id` = {$v} AND `is_default` = 1");
			$imgs = array_merge($imgs, $lis);
		}
		//SPU素材
		if (empty($imgs)) {
			$lis = $this->MySQL->row("SELECT `spu`.`thumb_url`,`spu`.`file_url` FROM `yuemi_sale`.`spu_material` AS spu " .
					"LEFT JOIN `yuemi_sale`.`sku` AS sku ON sku.spu_id = spu.spu_id " .
					"WHERE `sku`.`id` = {$v} AND `spu`.`is_default` = 1");
			$imgs = array_merge($imgs, $lis);
		}

		//ext_sku素材
		if (empty($imgs)) {
			$lis = $this->MySQL->row(
					"SELECT ekm.`thumb_url`,ekm.`file_url` FROM `yuemi_sale`.`ext_sku_material` AS ekm " .
					"LEFT JOIN `yuemi_sale`.`ext_sku` AS ek ON ekm.ext_sku_id = ek.id " .
					"WHERE ek.sku_id = {$v} AND `ekm`.`is_default` = 1");
			$imgs = array_merge($imgs, $lis);
		}
		//ext_spu素材
		if (empty($imgs)) {
			$spuid = $this->MySQL->scalar("SELECT spu_id FROM `yuemi_sale`.`sku` WHERE id = " . $v);

			$lis = $this->MySQL->row(
					"SELECT epm.`thumb_url`,epm.`file_url` FROM `yuemi_sale`.`ext_spu_material` AS epm " .
					"LEFT JOIN `yuemi_sale`.`ext_spu` AS ep ON ep.spu_id = epm.ext_spu_id " .
					"WHERE `ep`.`spu_id` = {$spuid} AND epm.is_default = 1"
			);
			$imgs = array_merge($imgs, $lis);
		}
		return $imgs;
	}

	public function getsku(int $p = 0) {
		$res = $this->MySQL->paging("SELECT `id`,`title` FROM `yuemi_sale`.`sku`", 30, $p);
		return [
			'res' => $res
		];
	}

	public function hotsearch() {
		return [
			'res' => $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`hot_search` ORDER BY `p_order`")
		];
	}

	public function update_hot(int $id = 0) {
		if ($this->Context->Runtime->ticket->postback) {
			$title = $this->MySQL->encode($_POST['title']);
			$color = $this->MySQL->encode($_POST['color']);
			$size = intval($_POST['size']);
			$p_order = inval($_POST['p_order']);
			$ids = inval($_POST['id']);
			$this->MySQL->execute("UPDATE `yuemi_sale`.`hot_search` " .
					"SET `title` = '{$title}',`color` = " .
					"'{$color}',`size` = {$size},`p_order` = {$p_order} WHERE `id` = {$ids} ");
			throw new \Ziima\MVC\Redirector('/index.php?call=runer.hotsearch');
		}
		return [
			'res' => $this->MySQL->row("SELECT * FROM `yuemi_sale`.`hot_search` WHERE `id` = {$id}")
		];
	}
	
	public function spread(int $p = 0){
		$sql = " SELECT `read`.*,`r`.`province`,`r`.`city`,`r`.`country` FROM `yuemi_sale`.`spread_userinfo` AS `read` LEFT JOIN `yuemi_main`.`region` AS `r` ON `r`.`id` = `read`.`region_id` ";
		$sql .= " ORDER BY `read`.`id` DESC ";
		$res = $this->MySQL->paging($sql,30,$p);
		return [
			'res' => $res
		];
	}

}
