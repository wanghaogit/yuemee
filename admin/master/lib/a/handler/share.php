<?php

include "lib/ApiHandler.php";

/**
 * 分享管理接口
 */
class share_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 创建模板
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		title				string		模板名称
	 * @request		multi				int			是否多品
	 */
	public function template_create(\Ziima\MVC\REST\Request $request) {
		$title = $this->MySQL->row("SELECT id FROM `yuemi_sale`.`share_template` WHERE name = '%s'", $this->MySQL->encode($request->body->title));

		if (!empty($title)) {
			throw new \Ziima\MVC\REST\Exception('E_DATABASE', '模板名称不能重复');
		}
//		if (!$this->MySQL->execute("INSERT INTO `yuemi_sale`.`share_template` (`name`,`is_multiple`,`title_config`,`material_config`,`name_config`,`avatar_config`,`price_config`,`market_config`,`create_time`,`create_user`,`create_from`) VALUES "
//						. "('%s',%d,'%s','%s','%s','%s','%s','%s',%d,'%s','%s')",
//						$this->MySQL->encode($request->body->title), $request->body->multi,
//						/* title_config */ "22,116,549,65,150,20,#000",
//						/* material_config */ "1,0,262,600,600,0",
//						/* name_config */ "1,123,24,22,#000",
//						/* avatar_config */ "1,26,25,78,78",
//						/* price_config */ "1,295,920,22,red",
//						/* market_config */ "1,295,881,22,#999",
//						time(),
//						$this->User->id,
//						$this->Context->Runtime->ticket->ip
//				)
//		) 
		$ShareTemplateEntity = new yuemi_sale\ShareTemplateEntity();
		$ShareTemplateEntity->create_from = $this->Context->Runtime->ticket->ip;
		$ShareTemplateEntity->body_path = "/template/share/share_1.png";
		$ShareTemplateEntity->body_url = "/template/share/share_1.png";
		$ShareTemplateEntity->body_width = 600;
		$ShareTemplateEntity->body_height = 1044;
		$ShareTemplateEntity->create_time = time();
		$ShareTemplateEntity->create_user = $this->User->id;
		$ShareTemplateEntity->avatar_config = "1,26,25,78,78";
		$ShareTemplateEntity->market_config = "1,295,881,22,#999";
		$ShareTemplateEntity->material_config = "1,0,262,600,600,0";
		$ShareTemplateEntity->title_config = "22,116,549,65,150,20,#000";
		$ShareTemplateEntity->price_config = "1,295,920,22,red";
		$ShareTemplateEntity->name_config = "1,123,24,22,#000";
		$ShareTemplateEntity->name = $this->MySQL->encode($request->body->title);
		$ShareTemplateEntity->is_multiple = $request->body->multi;
		$ShareTemplateFactory = new \yuemi_sale\ShareTemplateFactory(MYSQL_WRITER, MYSQL_READER);
		if (!$ShareTemplateFactory->insert($ShareTemplateEntity)) {
			throw new \Exception('插入表release失败！');
		}
		$id = $this->MySQL->row("SELECT `id` FROM `yuemi_sale`.`share_template` ORDER BY `id` DESC");
//		{
//			throw new \Ziima\MVC\REST\Exception('E_DATABASE', '数据库错误了');
//		}
		return [
			'__code' => 'OK',
			'__message' => '',
			'Id' => $id['id'],
			'Title' => $request->body->title
		];
	}

	/**
	 * 保存模板布局
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id					int		模板ID
	 * 
	 * @request		title_x				int		商品文案X
	 * @request		title_y				int		商品文案Y
	 * @request		title_w				int		商品文案W
	 * @request		title_h				int		商品文案H
	 * @request		title_length		int		商品文案长度
	 * @request		title_size			int		商品文案字体大小
	 * @request		title_color			string	商品文案颜色
	 * 
	 * @request		material_count		string	商品素材内容
	 * @request		material_x			int		商品素材X
	 * @request		material_y			int		商品素材Y
	 * @request		material_w			int		商品素材W
	 * @request		material_h			int		商品素材H
	 * @request		material_padding	int		商品素材边距
	 * 
	 * @request		name_open			int		个人昵称是否显示
	 * @request		name_x				int		个人昵称X
	 * @request		name_y				int		个人昵称Y
	 * @request		name_size			int		个人昵称字体大小
	 * @request		name_color			string	个人昵称颜色
	 * 
	 * @request		avatar_open			int		个人头像是否显示
	 * @request		avatar_x			int		个人头像X
	 * @request		avatar_y			int		个人头像Y
	 * @request		avatar_w			int		个人头像W
	 * @request		avatar_h			int		个人头像H
	 * 
	 * @request		price_open			int		平台价格是否显示	 
	 * @request		price_x				int		平台价格X
	 * @request		price_y				int		平台价格Y
	 * @request		price_size			int		平台价格字体大小
	 * @request		price_color			string	平台价格颜色
	 * 
	 * @request		market_open			int		参考价格是否显示
	 * @request		market_x			int		参考价格X
	 * @request		market_y			int		参考价格Y
	 * @request		market_size			int		参考价格字体大小
	 * @request		market_color		string	参考价格字体颜色
	 */
	public function template_save(\Ziima\MVC\REST\Request $request) {

		if (empty($request->body->id)) {
			throw new \Ziima\MVC\REST\Exception('E_TEMPLATE', '没找到指定ID的模板');
		}

		$title['title_x'] = $request->body->title_x;
		$title['title_y'] = $request->body->title_y;
		$title['title_w'] = $request->body->title_w;
		$title['title_h'] = $request->body->title_h;
		$title['title_length'] = $request->body->title_length;
		$title['title_size'] = $request->body->title_size;
		$title['color'] = $this->MySQL->encode($request->body->title_color);
		$title = implode(',', $title);

		$material['material_count'] = $this->MySQL->encode($request->body->material_count);
		$material['material_x'] = $request->body->material_x;
		$material['material_y'] = $request->body->material_y;
		$material['material_w'] = $request->body->material_w;
		$material['material_h'] = $request->body->material_h;
		$material['material_padding'] = $request->body->material_padding;
		$material = implode(',', $material);

		$name['name_open'] = $request->body->name_open;
		$name['name_x'] = $request->body->name_x;
		$name['name_y'] = $request->body->name_y;
		$name['name_size'] = $request->body->name_size;
		$name['name_color'] = $this->MySQL->encode($request->body->name_color);
		$name = implode(',', $name);

		$avatar['avatar_open'] = $request->body->avatar_open;
		$avatar['avatar_x'] = $request->body->avatar_x;
		$avatar['avatar_y'] = $request->body->avatar_y;
		$avatar['avatar_w'] = $request->body->avatar_w;
		$avatar['avatar_h'] = $request->body->avatar_h;
		$avatar = implode(',', $avatar);

		$price['price_open'] = $request->body->price_open;
		$price['price_x'] = $request->body->price_x;
		$price['price_y'] = $request->body->price_y;
		$price['price_size'] = $request->body->price_size;
		$price['price_color'] = $this->MySQL->encode($request->body->price_color);
		$price = implode(',', $price);

		$market['market_open'] = $request->body->market_open;
		$market['market_x'] = $request->body->market_x;
		$market['market_y'] = $request->body->market_y;
		$market['market_size'] = $request->body->market_size;
		$market['market_color'] = $this->MySQL->encode($request->body->market_color);
		$market = implode(',', $market);


		$this->MySQL->execute(
				"UPDATE `yuemi_sale`.`share_template` SET `title_config` = '%s',`material_config` = '%s',`name_config` = '%s',`avatar_config` = '%s', `price_config` = '%s',`market_config` = '%s'  WHERE `id` = %d",
				$title,
				$material,
				$name,
				$avatar,
				$price,
				$market,
				$request->body->id
		);
		return "OK";
	}

}
