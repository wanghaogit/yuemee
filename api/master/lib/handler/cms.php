<?php

include_once 'lib/ApiHandler.php';

/**
 * 资讯接口
 */
class cms_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 资讯列表
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id			int			ID
	 */
	public function list(\Ziima\MVC\REST\Request $request) {
		$ur = URL_RES;
		$res = $this->MySQL->grid(
				"SELECT `ca`.`id` AS `Id`, `ca`.`title` AS `Title`,`ca`.`create_time` AS `Time`,CONCAT('{$ur}','/upload',`cm`.`file_url`) AS `Picture`,`ca`.`Content` " .
				"FROM `yuemi_main`.`cms_article` AS `ca` LEFT JOIN `yuemi_main`.`cms_material` AS `cm` ON `cm`.`article_id` = `ca`.`id` " .
				"WHERE `ca`.`column_id` = {$request->body->id} " .
				" GROUP BY `ca`.`id` ORDER BY `ca`.`create_time` DESC");
		return [
			'__code' => 'OK',
			'__message' => '',
			'List' => $res
		];
	}

	/**
	 * 资讯内容
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id			int			ID
	 */

	public function content(\Ziima\MVC\REST\Request $request) {
		$res = $this->MySQL->row(
				" SELECT `ca`.`id` AS `Id`,`ca`.`title` AS `Title`,`ca`.`create_time` AS `Time`,`ca`.`content` AS `Content` " .
				"FROM  `yuemi_main`.`cms_article` AS `ca` " .
				"WHERE `ca`.`id` = {$request->body->id} ");
		return [
			'__code' => 'OK',
			'__message' => '',
			'Cms' => $res
		];
	}

}
