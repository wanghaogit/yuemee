<?php

include "lib/AdminHandler.php";

/**
 * 分享管理
 * @auth
 */
class share_handler extends AdminHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	public function index(int $p = 0,string $n = '',string $g = '') {
		$sql = "SELECT s.*, u.name AS Name, u2.name AS Dname, t.name AS Tname, sku.title AS Title ".
				"FROM `yuemi_sale`.`share` AS s ".
				"LEFT JOIN `yuemi_main`.`user` AS u ON s.user_id = u.id ".
				"LEFT JOIN `yuemi_main`.`user` AS u2 ON s.director_id = u2.id ".
				"LEFT JOIN `yuemi_main`.`team` AS t ON s.team_id = t.id ".
				"LEFT JOIN `yuemi_sale`.`sku` AS sku ON s.sku_id = sku.id ";
		$whr = [];
		if ($n !== '') {
			$whr[] = " `u`.`name` LIKE '%{$n}%' ";
		}
		if ($g !== '') {
			$whr[] = " `sku`.`title` LIKE '%{$g}%' ";
		}
		if ($whr) {
			$sql .=  ' WHERE '. implode(' AND ', $whr);
		}
		$sql .= " ORDER BY `s`.`id` DESC ";
		$list = $this->MySQL->paging($sql,20,$p);
		return [
			'data' => $list
		];
	}

	public function template() {
		return [
			'Templates' => $this->MySQL->grid("SELECT * FROM `yuemi_sale`.`share_template`")
		];
	}

	public function picture(int $p = 0) {
		$res = $this->MySQL->paging("SELECT u.*,sku.title AS name FROM `yuemi_main`.`user_material` AS u LEFT JOIN `yuemi_sale`.`sku` AS sku ON u.sku_id = sku.id ORDER BY u.id DESC", 30, $p);

		return [
			'Pic' => $res
		];
	}

	public function update_template( int $id) {
		//$id = intval($_GET['id']);
		$res = $this->MySQL->row("SELECT * FROM `yuemi_sale`.`share_template` WHERE id = {$id}");

		$title = explode(',', $res['title_config']);
		$material = explode(',', $res['material_config']);
		$name = explode(',', $res['name_config']);
		$avatar = explode(',', $res['avatar_config']);
		$price = explode(',', $res['price_config']);
		$market = explode(',', $res['market_config']);
		return [
			'Tpl' => $res,
			'title' => $title,
			'material' => $material,
			'name' => $name,
			'avatar' => $avatar,
			'price' => $price,
			'market' => $market
		];
	}

}
