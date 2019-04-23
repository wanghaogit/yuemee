<?php

include "lib/AdminHandler.php";

/**
 * 资讯管理
 * @auth
 */
class cms_handler extends AdminHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 列表
	 */
	public function index(int $p = 0) {
		$sql = "SELECT a.*,c.name AS cname " .
				"FROM `yuemi_main`.`cms_article` AS a " .
				"LEFT JOIN `yuemi_main`.`cms_column` AS c ON a.column_id = c.id "
		;
		$sql .= " ORDER BY `id` DESC";
		$res = $this->MySQL->paging($sql, 25, $p);

		foreach ($res->Data as $arr) {
			$list['id'] = $arr['id'];
			$list['title'] = $arr['title'];
			$list['cname'] = $arr['cname'];
			$list['status'] = $arr['status'];
			$list['create_time'] = date('Y-m-d H:i:s', $arr['create_time']);
			$c[] = $list;
		}
		if (empty($c)) {
			$c = '';
		}
		return [
			'Result' => $res,
			'content' => $c
		];
	}

	/*
	 * 栏目管理
	 */

	public function column() {
		$sql = "SELECT * FROM `yuemi_main`.`cms_column`";
		$res = $this->MySQL->grid($sql);
		return [
			'res' => $res
		];
	}

	/*
	 * 发布咨询
	 */

	public function create_cms() {
		$sql = "SELECT * FROM `yuemi_main`.`cms_column`";
		$res = $this->MySQL->grid($sql);
		return [
			'res' => $res
		];
	}

	/*
	 * 修改
	 * @param int $id
	 * @return type
	 * @throws \Ziima\MVC\Redirector
	 */

	public function update_cms(int $id = 0) {
		$sql = "SELECT * FROM `yuemi_main`.`cms_article` WHERE id='" . $id . "'";
		$res = $this->MySQL->row($sql);
		$sql1 = "SELECT * FROM `yuemi_main`.`cms_column` ";
		$res1 = $this->MySQL->grid($sql1);
		$mats = [
			'Article' => $this->MySQL->grid("SELECT `id`,`file_url`,`thumb_url` FROM `cms_material` WHERE `article_id` = %d", $id),
			'Column' => $this->MySQL->grid("SELECT `id`,`file_url`,`thumb_url` FROM `cms_material` WHERE `column_id` = %d", $res['column_id'])
		];
		return [
			'res' => $res,
			'cate' => $res1,
			'Materials' => $mats
		];
	}

	/*
	 * 审核
	 * @param int $id
	 * return type
	 * @throws \Ziima\MVC\Redirector
	 */

	public function examine(int $id = 0) {
		$sql = "SELECT * FROM `yuemi_main`.`cms_article` WHERE id='" . $id . "'";
		$res = $this->MySQL->row($sql);
		$sql1 = "SELECT name FROM `yuemi_main`.`cms_column` AS c LEFT JOIN `yuemi_main`.`cms_article` AS a ON a.column_id = c.id WHERE a.id = '" . $id . "'";
		$res1 = $this->MySQL->scalar($sql1);
		return [
			'res' => $res,
			'cate' => $res1
		];
	}

}
