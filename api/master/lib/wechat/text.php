<?php
include Z_SITE . '/lib/WechatHandler.php';

class text_handler extends WechatHandler {
	private $_KEYWORD_MAP = [
		'测试'	=> '_test_page',
		'test'	=> '_test_page',
		'name'	=> '_test_name'

	];
	function __construct() {
		parent::__construct();
	}
	
	public function execute(\Ziima\MVC\Wechat\Request $request) : ?\Ziima\MVC\Wechat\Response{
		if(empty($request->Content)){
			return $this->error('你说啥？');
		}
		
		foreach($this->_KEYWORD_MAP as $k => $m){
			if(strpos($request->Content, $k) !== false){
				return $this->$m($request);
			}
		}
		return $this->error('你说“' . $request->Content . '”，但是我听不懂啊。');
	}
	
	private function _test_page(\Ziima\MVC\Wechat\Request $request) : ?\Ziima\MVC\Wechat\Response{
		$itm = new \Ziima\MVC\Wechat\Article();
		$itm->Url = "https://a.yuemee.com/mobile.php?call=default.invite&v=eglic01";
		$itm->Title = "测试邀请注册流程";
		$itm->Description = "这是一个测试页面，只是用来测试从微信公众号跳转到普通Web页面。再跳转到授权页面。最后回到入驻表单。";
		$itm->PicUrl = "http://img1.gtimg.com/ninja/2/2018/04/ninja152263746924464.jpg";
		$atl = new \Ziima\MVC\Wechat\ArticleResponse();
		$atl->Articles->add($itm);
		return $atl;
	}
	
	private function _test_name(\Ziima\MVC\Wechat\Request $request) : ?\Ziima\MVC\Wechat\Response{
		$WxName = isset($this->WxUserInfo->nickname) ? $this->WxUserInfo->nickname : '';
		$WxName = preg_replace('/\\\\x[a-z0-9]+/i', '', $WxName);
		$WxName = str_replace(['\\','\'','"','&','%'], ['','','','',''], $WxName);
		
		return $this->error("原昵称：{$this->WxUserInfo->nickname}\n修整后：{$WxName}");
	}
}
