<?php

include_once 'lib/ApiHandler.php';

/**
 * 素材接口
 * @noauth
 */
class material_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * ExtSPU素材列表
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int		ExtSPUID
	 */
	public function ext_spu(\Ziima\MVC\REST\Request $request) {
		return [
			'__code' => 'OK',
			'__message' => '',
			'Pictures' => $this->MySQL->grid(
					"SELECT `id` AS `Id`," .
					"CONCAT('%s','/upload',`file_url`) AS `Url`," .
					"`image_width` AS `Width`," .
					"`image_height` AS `Height` " .
					"FROM `yuemi_sale`.`ext_spu_material` " .
					"WHERE `ext_spu_id` = %d",
					URL_RES,
					$request->body->id
			)
		];
	}

	/**
	 * ExtSKU素材列表
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int		ExtSKUID
	 */
	public function ext_sku(\Ziima\MVC\REST\Request $request) {
		return [
			'__code' => 'OK',
			'__message' => '',
			'Pictures' => $this->MySQL->grid(
					"SELECT `id` AS `Id`," .
					"CONCAT('%s','/upload',`file_url`) AS `Url`," .
					"`image_width` AS `Width`," .
					"`image_height` AS `Height` " .
					"FROM `yuemi_sale`.`ext_sku_material` " .
					"WHERE `ext_sku_id` = %d",
					URL_RES,
					$request->body->id
			)
		];
	}

	/**
	 * SPU素材列表
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int		SPUID
	 */
	public function spu(\Ziima\MVC\REST\Request $request) {
		return [
			'__code' => 'OK',
			'__message' => '',
			'Pictures' => $this->MySQL->grid(
					"SELECT `id` AS `Id`," .
					"CONCAT('%s','/upload',`file_url`) AS `Url`," .
					"`image_width` AS `Width`," .
					"`image_height` AS `Height` " .
					"FROM `yuemi_sale`.`spu_material` " .
					"WHERE `spu_id` = %d",
					URL_RES,
					$request->body->id
			)
		];
	}

	/**
	 * SPU素材列表
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int		SKUID
	 */
	public function sku(\Ziima\MVC\REST\Request $request) {
		return [
			'__code' => 'OK',
			'__message' => '',
			'Pictures' => $this->MySQL->grid(
					"SELECT `id` AS `Id`," .
					"`type` AS `Type`," .
					"CONCAT('%s','/upload',`file_url`) AS `Url`," .
					"`image_width` AS `Width`," .
					"`image_height` AS `Height` " .
					"FROM `yuemi_sale`.`sku_material` " .
					"WHERE `sku_id` = %d",
					URL_RES,
					$request->body->id
			)
		];
	}

	/**
	 * SPU素材列表
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int		SKUID
	 */
	public function page(\Ziima\MVC\REST\Request $request) {
		return [
			'__code' => 'OK',
			'__message' => '',
			'Pictures' => $this->MySQL->grid(
					"SELECT `id` AS `Id`," .
					"1 AS `Type`," .
					"CONCAT('%s','/upload',`file_url`) AS `Url`," .
					"`image_width` AS `Width`," .
					"`image_height` AS `Height` " .
					"FROM `yuemi_main`.`run_material` " .
					"WHERE `page_id` = %d",
					URL_RES,
					$request->body->id
			)
		];
	}

}
