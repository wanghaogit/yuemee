<?php

include "lib/ApiHandler.php";

/**
 * spu管理接口
 */
class spu_handler extends ApiHandler {

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	/**
	 * 
	 * @param \Ziima\MVC\REST\Request $request
	 * @request    id     int     大分类ID
	 * @request	   spu_id	int		spu id
	 * @slient
	 */
	public function big(\Ziima\MVC\REST\Request $request) {
		$spuid = $request->body->spu_id;
		$id = $request->body->id;
		$sql1 = "SELECT * FROM `yuemi_sale`.`spu` WHERE id = {$spuid}";
		$spudata = $this->MySQL->grid($sql1);
		//分割成颜色和尺码
		$specs = $spudata[0]['specs'];
		$more_specs = $this->spec($specs);	//只返名称，然后通过接口调具体规格

		$res = $more_specs['guige'][$id];
		$name = $more_specs['name'][$id];
		return['res' => $res, 'name' => $name];
	}

	//规格和规格名分开
	public function spec($specs) {
		if (empty($specs)) {
			return '';
		}
		$spec = array_filter(preg_split('/[;\r\n]+/s', $specs));
		//根据逗号细分
//		var_dump($spec);die;
//		echo count($spec);die;
		for ($i = 0; $i < count($spec); $i ++) {
			$qian = explode(':', $spec[$i]);
			$name[] = $qian[0];
			$guige[] = explode(',', $qian[1]);
		}
		$ok_specs['name'] = $name;
		$ok_specs['guige'] = $guige;
		return $ok_specs;
	}

}
