<?php

include_once 'lib/ApiHandler.php';
include_once Z_ROOT . '/Chart.php';
include_once Z_ROOT . '/QR.php';
include_once Z_ROOT . '/Cloud/Kuaidi.php';

/**
 * VIP接口
 */
class vip_handler extends ApiHandler {

	/**
	 * VIP信息
	 * @var \yuemi_main\VipEntity
	 */
	private $Vip = null;

	function __construct(\Ziima\MVC\Context $ctx) {
		parent::__construct($ctx);
	}

	function __auth() {
		parent::__auth();

		if ($this->User === null) {
			throw new \Ziima\MVC\REST\Exception('E_USER', '尚未登录');
		}
		$ret = \yuemi_main\ProcedureInvoker::Instance()->check_user_role($this->User->id);
		if ($ret === null) {
			throw new \Ziima\MVC\REST\Exception('E_DATABASE', '刷新身份失败');
		}
		if ($ret->ReturnValue != 'OK') {
			throw new \Ziima\MVC\REST\Exception($ret->ReturnValue, '刷新身份失败');
		}
		if ($ret->LevelUser == 0) {
			throw new \Ziima\MVC\REST\Exception('E_USER', '禁止登陆用户');
		}
		if ($ret->LevelVip == 0) {
			throw new \Ziima\MVC\REST\Exception('E_VIP', '不是VIP#1');
		}
		$this->Vip = \yuemi_main\VipFactory::Instance()->load($this->User->id);
		if ($this->Vip === null) {
			throw new \Ziima\MVC\REST\Exception('E_VIP', '不是VIP#2');
		}
		if ($this->Vip->status == 0) {
			throw new \Ziima\MVC\REST\Exception('E_VIP', 'VIP身份已过期#1');
		}
		if ($this->Vip->create_time > Z_NOW) {
			throw new \Ziima\MVC\REST\Exception('E_VIP', 'VIP身份已过期#2');
		}
		if ($this->Vip->expire_time < Z_NOW) {
			throw new \Ziima\MVC\REST\Exception('E_VIP', 'VIP身份已过期#3');
		}
	}

	/**
	 * VIP信息
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function info(\Ziima\MVC\REST\Request $request) {
		$stu = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`vip_buff` WHERE `user_id` = %d", $this->User->id);
		$ret = [
			'__code' => 'OK',
			'__message' => '',
			'ChiefId' => $this->Vip->cheif_id,
			'InviteCode' => $this->Vip->invite_code,
			'Status' => $this->Vip->status,
			'Expire' => date('Y-m-d H:i:s', $this->Vip->expire_time),
			'Detail' => []
		];
		foreach ($stu as $n) {
			$ret['Detail'][] = [
				'Coin' => $n['coin'],
				'Start' => date('Y-m-d H:i:s', $n['start_time']),
				'Expire' => date('Y-m-d H:i:s', $n['expire_time']),
				'PayTime' => date('Y-m-d H:i:s', $n['create_time'])
			];
		}
		return $ret;
	}

	/**
	 * 获取我的邀请素材
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		template_id		int			选择的模板ID
	 */
	public function invite_material(\Ziima\MVC\REST\Request $request) {
		$tpl = \yuemi_main\InviteTemplateFactory::Instance()->load($request->body->template_id);
		if ($tpl === null) {
			throw new \Ziima\MVC\REST\Exception('E_TEMPLATE', '没找到模板信息');
		}
		$sav = UPLOAD_ROOT . DIRECTORY_SEPARATOR . 'vip';
		$url = '/vip';
		if (!file_exists($sav))
			mkdir($sav, 0755, true);
		$sav .= DIRECTORY_SEPARATOR . substr($this->Vip->invite_code, 0, 1);
		$url .= '/' . substr($this->Vip->invite_code, 0, 1);
		if (!file_exists($sav))
			mkdir($sav, 0755, true);
		$sav .= DIRECTORY_SEPARATOR . substr($this->Vip->invite_code, 1, 1);
		$url .= '/' . substr($this->Vip->invite_code, 1, 1);
		if (!file_exists($sav))
			mkdir($sav, 0755, true);
		$sav .= DIRECTORY_SEPARATOR . $this->Vip->invite_code . '_' . $tpl->id . '.png';

		$url .= '/' . $this->Vip->invite_code . '_' . $tpl->id . '.png';
		//TODO:检查文件的 filectime 与当前时间差是否超过一个月
		if (file_exists(UPLOAD_ROOT . $url)) {
			$old = strtotime("-1 month"); //一个月前时间
			$img_time = filectime(UPLOAD_ROOT . $url); //文件修改时间
			if ($old > $img_time) {
				//删除30天之前的
				unlink(UPLOAD_ROOT . $url);
			}
		}

//		if (file_exists($sav)) {
//			return [
//				'__code' => 'OK',
//				'__message' => '',
//				'Picture' => URL_RES . '/upload' . $url
//			];
//		}
		$src = UPLOAD_ROOT . $tpl->body_path;
		$src = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $src);
		if (!file_exists($src)) {
			throw new \Ziima\MVC\REST\Exception('E_TEMPLATE', '没找到模板文件 ' . $tpl->body_path);
		}
		$pic = new \Ziima\Drawing\Picture($src);
		if ($tpl->name_enable) {
			if ($tpl->id == 2) {
				$user_name = mb_substr($this->User->name, 0, 8, 'utf-8');
			} else {
				$user_name = mb_substr($this->User->name, 0, 10, 'utf-8');
			}
			$fn = new \Ziima\Drawing\Font('noto', $tpl->name_size, $tpl->name_color, true);
			$pic->drawString($user_name, $tpl->name_x, $tpl->name_y + $tpl->name_size, $fn);
		}

		if ($tpl->code_enable) {
			$fn = new \Ziima\Drawing\Font('consola', $tpl->code_size, $tpl->code_color, true);
			$pic->drawString($this->Vip->invite_code, $tpl->code_x, $tpl->code_y + $tpl->code_size, $fn);
		}

		if ($tpl->avatar_enable) {
			if (!empty($this->User->avatar)) {
				$avf = UPLOAD_ROOT . $this->User->avatar;
				$avf = str_replace(['/', '\\'], [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $avf);
				if (!file_exists($avf)) {
					throw new \Ziima\MVC\REST\Exception('E_AVATAR', '头像文件丢失');
				}
				$pic->drawIcon($avf, $tpl->avatar_x, $tpl->avatar_y, $tpl->avatar_w, $tpl->avatar_h);
			}
		}
		$qr = new \Ziima\Drawing\QRencode(QR_ECLEVEL_L, 3, 1);
		$qrp = $qr->buildPicture('https://a.yuemee.com/mobile.php?call=invite.index&v=' . $this->Vip->invite_code);
		if ($qrp === null) {
			throw new \Ziima\MVC\REST\Exception('E_QR', '生成二维码失败');
		}
		$pic->drawCanvas($qrp, $tpl->qr_x, $tpl->qr_y, $tpl->qr_w, $tpl->qr_h);
		$pic->setFormat(\Ziima\Drawing\Picture::FORMAT_PNG24);
		$pic->saveAs($sav);
		return [
			'__code' => 'OK',
			'__message' => '',
			'Picture' => URL_RES . '/upload' . $url
		];
	}

	/**
	 * 浏览邀请用户
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		isapp		int			是否安装app
	 * @request		begin		int			开始时时间戳
	 * @request		end			int			结束时间戳
	 * @request		page		int			页数
	 */
	public function userlist(\Ziima\MVC\REST\Request $request) {
		$isapp = $request->body->isapp;
		$begin = $request->body->begin;
		$end = $request->body->end;
		$uid = $this->User->id;
		$ur = URL_RES;
		$sql = "SELECT `uw`.`app_open_id`,`u`.`id` AS `Id`,`u`.`mobile` AS `Mobile`,`u`.`reg_time` AS `Reg_time`,`u`.`name` AS `Name`,CONCAT('{$ur}','/upload',`u`.`avatar`) AS `Avatar`,`u`.`gender` AS `Gender` " .
				"FROM `yuemi_main`.`user` AS `u` LEFT JOIN `yuemi_main`.`user_wechat` AS `uw` ON `uw`.`user_id` = `u`.`id` WHERE `u`.`invitor_id` = {$uid}";

		$whr = [];

		if ($begin !== 0) {
			$whr[] = " `u`.`reg_time` > {$begin} ";
		}
		if ($end !== 0) {
			$whr[] = " `u`.`reg_time` < {$end} ";
		}
		if ($isapp == 2) {
			$whr[] = " `uw`.`app_open_id` > '' ";
		}
		if ($isapp == 1) {
			$whr[] = " `uw`.`app_open_id` = '' ";
		}
		if ($isapp == 3) {
			$whr[] = " `uw`.`user_id` > 0 ";
		}
		if ($whr) {
			$sql .= ' AND ' . implode(' AND ', $whr);
		}

		$re = $this->MySQL->paging($sql, 10, $request->body->page);
		return [
			'__code' => 'OK',
			'__message' => '',
			'List' => $re
		];
	}

	/**
	 * 推广订单统计
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		begin			int			开始时间戳
	 * @request		end				int			结束时间戳
	 * @request		status			int			订单状态
	 * @request		keyword			string		查询关键字u's
	 * @request		page			int			分页
	 */
	public function inviteorder(\Ziima\MVC\REST\Request $request) {
		$uid = $this->User->id;
		$begin = $request->body->begin;
		$end = $request->body->end;
		$status = $request->body->status;
		$keyword = $request->body->keyword;
		$whr = [];
		$sql = "SELECT `o`.`id` AS `order_id`,`o`.`trans_id`,`o`.`status`,`o`.`create_time`,`o`.update_time,`oi`.*,`ca`.`name` AS `catname`,`u`.`name` AS `rename`,`uu`.`name` AS `buyname`,`su`.`name` AS `suname` FROM `yuemi_sale`.`order_item` AS `oi` " .
				"LEFT JOIN `yuemi_sale`.`share` AS `s` ON `s`.`id` = `oi`.`share_id` " .
				" LEFT JOIN `yuemi_sale`.`catagory` AS `ca` ON `ca`.`id` = `oi`.`catagory_id`" .
				" LEFT JOIN `yuemi_main`.`supplier` AS `su` ON `su`.`id` = `oi`.`supplier_id` " .
				" LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `oi`.`rebate_user` " .
				" LEFT JOIN `yuemi_sale`.`order` AS `o` ON `o`.`id` = `oi`.`order_id` " .
				" LEFT JOIN `yuemi_main`.`user` AS `uu` ON `uu`.`id` = `o`.`user_id` " .
				"WHERE `s`.`user_id` = {$uid} AND `oi`.`catagory_id` = 701 OR `oi`.`catagory_id` = 7 ";

		if ($keyword !== '') {
			$whr[] = " `oi`.`title` LIKE '%{$keyword}%' ";
		}

		if ($status == 1) {
			$whr[] = " `o`.status IN (35,42,13,14,15) ";
		}
		if ($status == 2) {
			$whr[] = " `o`.status IN (7,12,11) ";
		}
		if ($begin !== 0) {
			$whr[] = " `o`.create_time > {$begin} ";
		}
		if ($end !== 0) {
			$whr[] = " `o`.create_time < {$end} ";
		}
		if ($whr) {
			$sql .= ' AND ' . implode(' AND ', $whr);
		}
		$re = $this->MySQL->paging($sql, 20, $request->body->page);
		foreach ($re->Data as $k => $v) {
			if ($re->Data[$k]['rebate_vip'] > 0) {
				$re->Data[$k]['yjl'] = $re->Data[$k]['rebate_vip'] / $re->Data[$k]['money'];
			} else {
				$re->Data[$k]['yjl'] = 0;
			}
			$re->Data[$k]['picture'] = URL_RES . "/upload" . $re->Data[$k]['picture'];
		}
		return [
			'__code' => 'OK',
			'__message' => '',
			'List' => $re,
			'status' => $request->body->status
		];
	}

	/**
	 * 买家管理列表
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		keyword			char			查询关键字
	 */
	public function userbuy(\Ziima\MVC\REST\Request $request) {
		$uid = $this->User->id;

		$keyword = $request->body->keyword;
		$whr = [];
		$sql = "SELECT `o`.`trans_id`,`o`.`id` AS `order_id`,`o`.`status`,`o`.`create_time`,`o`.update_time,`oi`.*,`ca`.`name` AS `catname`,`u`.`name` AS `rename`,`uu`.`name` AS `buyname`,`su`.`name` AS `suname` FROM `yuemi_sale`.`order_item` AS `oi` " .
				"LEFT JOIN `yuemi_sale`.`share` AS `s` ON `s`.`id` = `oi`.`share_id` " .
				" LEFT JOIN `yuemi_sale`.`catagory` AS `ca` ON `ca`.`id` = `oi`.`catagory_id`" .
				" LEFT JOIN `yuemi_main`.`supplier` AS `su` ON `su`.`id` = `oi`.`supplier_id` " .
				" LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `oi`.`rebate_user` " .
				" LEFT JOIN `yuemi_sale`.`order` AS `o` ON `o`.`id` = `oi`.`order_id` " .
				" LEFT JOIN `yuemi_main`.`user` AS `uu` ON `uu`.`id` = `o`.`user_id` " .
//				" LEFT JOIN `yuemi_main`.`user` AS `buyu` ON `buyu`.`id` = `o`.`user_id` ".
				"WHERE `s`.`user_id` = {$uid} AND `oi`.`catagory_id` <> 701 AND `oi`.`catagory_id` <> 7 ";

		if ($keyword !== '') {
			$whr[] = " `oi`.`title` LIKE '%{$keyword}%' ";
		}

		if ($whr) {
			$sql .= ' AND ' . implode(' AND ', $whr);
		}

		$re = $this->MySQL->paging($sql, 20, $request->body->page);
		foreach ($re->Data as $k => $v) {
			if ($re->Data[$k]['rebate_vip'] > 0) {
				$re->Data[$k]['yjl'] = $re->Data[$k]['rebate_vip'] / $re->Data[$k]['money'];
			} else {
				$re->Data[$k]['yjl'] = 0;
			}
			$re->Data[$k]['picture'] = URL_RES . "/upload" . $re->Data[$k]['picture'];
		}
		return [
			'__code' => 'OK',
			'__message' => '',
			'List' => $re
		];
	}

	/**
	 * 我的素材
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		keyword			char			查询关键字
	 * @request		catagory			int			分类id
	 */
	public function mypicture(\Ziima\MVC\REST\Request $request) {
		$uid = $this->User->id;
		$key = $request->body->keyword;
		$catagory = $request->body->catagory;
		$whr = [];
		if ($key !== '') {
			$whr[] = " `sk`.`title` LIKE '%$key%' ";
		}

		if ($catagory !== 0) {
			$catlist = $this->MySQL->grid("SELECT `id` FROM `yuemi_sale`.`catagory` WHERE `parent_id` = {$catagory}");
			$arr = '(';
			foreach ($catlist AS $v) {
				$arr .= (int) $v['id'] . ',';
			}
//			$arr = rtrim($arr,',');
			$arr .= $catagory;
			$arr .= ')';
			$whr[] = " `sk`.`catagory_id` in {$arr}";
		}
		$ur = URL_RES;
		$sql = "SELECT `um`.`id`,`um`.`user_id`,`um`.`sku_id`,`um`.`create_time`" .
				",CONCAT('{$ur}','/upload',`um`.`thumb_url`) AS `Url`,`sk`.`title` FROM `yuemi_main`.`user_material` " .
				"AS `um` LEFT JOIN `yuemi_sale`.`sku` AS `sk` ON `sk`.`id` = `um`.`sku_id` WHERE `um`.`user_id` = {$uid} ";

		if ($whr) {
			$sql .= ' AND ' . implode(' AND ', $whr);
		}
		$re = $this->MySQL->paging($sql, 10, $request->body->page);
		$arr = $re->Data;
		$num = count($arr);
		$isor = [];
		$retur = [];
		$key = 0;
		for ($i = 0; $i < $num; $i++) {
			$skuid = $arr[$i]['sku_id'];
			if (in_array($skuid, $isor)) {
				array_push($retur[$skuid], $arr[$i]);
			} else {
				$retur[$skuid][0] = $arr[$i];
				array_push($isor, $skuid);
			}
		}
		$a = 0;
		$retur = array_values($retur);
		$re->Data = $retur;
		return [
			'__code' => 'OK',
			'__message' => '',
			'List' => $re
		];
	}

	/**
	 * 卡位状态
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function apply_info(\Ziima\MVC\REST\Request $request) {
		$r = [
			'Cheif' => null,
			'Director' => null
		];

		$c = $this->MySQL->row("SELECT * FROM `yuemi_main`.`cheif_buff` WHERE `user_id` = %d AND `type` = 3 ORDER BY `id` DESC LIMIT 1", $this->User->id);

		if ($c) {
			$r['Cheif'] = [
				'OrderId' => $c['order_id'],
				'Status' => $c['pay_status'],
				'CreateTime' => $c['create_time']
			];
		}
		$d = $this->MySQL->row("SELECT * FROM `yuemi_main`.`director_buff` WHERE `user_id` = %d AND `type` = 3 ORDER BY `id` DESC LIMIT 1", $this->User->id);
		if ($d) {
			$r['Director'] = [
				'OrderId' => $d['order_id'],
				'Status' => $d['pay_status'],
				'CreateTime' => $d['create_time']
			];
		}
		return $r;
	}

	/**
	 * 卡位总监
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function apply_chief(\Ziima\MVC\REST\Request $request) {
		$ret = \yuemi_main\ProcedureInvoker::Instance()->check_user_role($this->User->id);
		if ($ret === null || $ret === false) {
			throw new \Ziima\MVC\REST\Exception('E_DATABASE', '系统内部错误');
		}
		if ($ret->LevelUser == 0) {
			throw new \Ziima\MVC\REST\Exception('E_USER', '此账号被禁用');
		}
		if ($ret->LevelCheif > 0) {
			throw new \Ziima\MVC\REST\Exception('E_OTHER', '已是总监身份');
		}
		if ($ret->LevelDirector > 0) {
			throw new \Ziima\MVC\REST\Exception('E_OTHER', '已是总经理身份');
		}
		$t = $this->MySQL->row("SELECT * FROM `yuemi_main`.`cheif_buff` WHERE `user_id` = %d");
		if ($t) {
			throw new \Ziima\MVC\REST\Exception('E_UNFINISH', '还有未完成申请');
		}
		$buf = new \yuemi_main\CheifBuffEntity();
		$buf->user_id = $this->User->id;
		$buf->type = 3;
		$buf->pay_channel = 2;
		$buf->pay_status = 1;
		$buf->pay_time = 0;
		$buf->order_id = \Ziima\Zid::Default()->order('K', 'C');
		$buf->money = 3999;
		$buf->create_time = Z_NOW;
		$buf->start_time = 0;
		$buf->expire_time = 0;
		$buf->create_from = $this->Context->Runtime->ticket->ip;
		\yuemi_main\CheifBuffFactory::Instance()->insert($buf);
		if ($buf->id < 1) {
			throw new \Ziima\MVC\REST\Exception('E_DATABASE', '系统内部错误');
		}
		return [
			'__code' => 'OK',
			'__message' => '',
			'OrderId' => $buf->order_id
		];
	}

	/**
	 * 卡位总经理
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function apply_director(\Ziima\MVC\REST\Request $request) {
		$ret = \yuemi_main\ProcedureInvoker::Instance()->check_user_role($this->User->id);
		if ($ret === null || $ret === false) {
			throw new \Ziima\MVC\REST\Exception('E_DATABASE', '系统内部错误');
		}
		if ($ret->LevelUser == 0) {
			throw new \Ziima\MVC\REST\Exception('E_USER', '此账号被禁用');
		}
		if ($ret->LevelDirector > 0) {
			throw new \Ziima\MVC\REST\Exception('E_OTHER', '已是总经理身份');
		}
		$t = $this->MySQL->row("SELECT * FROM `yuemi_main`.`director_buff` WHERE `user_id` = %d");
		if ($t) {
			throw new \Ziima\MVC\REST\Exception('E_UNFINISH', '还有未完成申请');
		}
		$buf = new \yuemi_main\DirectorBuffEntity();
		$buf->user_id = $this->User->id;
		$buf->type = 3;
		$buf->pay_channel = 2;
		$buf->pay_status = 1;
		$buf->pay_time = 0;
		$buf->order_id = \Ziima\Zid::Default()->order('K', 'D');
		$buf->money = 120000;
		$buf->create_time = Z_NOW;
		$buf->start_time = 0;
		$buf->expire_time = 0;
		$buf->create_from = $this->Context->Runtime->ticket->ip;
		\yuemi_main\DirectorBuffFactory::Instance()->insert($buf);
		if ($buf->id < 1) {
			throw new \Ziima\MVC\REST\Exception('E_DATABASE', '系统内部错误');
		}
		return [
			'__code' => 'OK',
			'__message' => '',
			'OrderId' => $buf->order_id
		];
	}

	/**
	 * 用户微信详情
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		id		int			子订单ID
	 */
	public function user_wechatinfo(\Ziima\MVC\REST\Request $request) {
		$id = $request->body->id;
		$sql = "SELECT `uw`.`avatar`,`o`.`addr_mobile` AS `mobile`,`o`.`addr_name` AS `usera`,`uw`.`name` AS `userv` FROM `yuemi_sale`.`order_item` AS `oi` " .
				"LEFT JOIN `yuemi_sale`.`order` AS `o` " .
				"ON `o`.`id` = `oi`.`order_id` LEFT JOIN `yuemi_main`.`user_wechat` AS `uw` " .
				"ON `uw`.`user_id` = `o`.`user_id` WHERE `oi`.`id` = {$id} ";
		$res = $this->MySQL->row($sql);
		return [
			'data' => $res
		];
	}

	/**
	 * 续费
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function renew(\Ziima\MVC\REST\Request $request) {
		$uid = $this->User->id;
		$Re = \yuemi_main\ProcedureInvoker::Instance()->make_coin_vip($uid, $this->Context->Runtime->ticket->ip);
		if ($Re->ReturnValue == 'OK') {
			return ['__code' => 'OK', '__message' => ''];
		} else {
			return ['__code' => $Re->ReturnValue, '__message' => $Re->ReturnMessage];
		}
	}

	/**
	 * 今日收入
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function today_get(\Ziima\MVC\REST\Request $request) {
		$uid = $this->User->id;
		$list = $this->MySQL->grid("SELECT `self_profit`,`update_time` FROM `yuemi_sale`.`rebate` AS `r` WHERE `r`.`share_user_id` = {$uid}");
		$arr = [];
		$toarr = [];
		$now = date("Y-m-d", time());
		foreach ($list as $v) {
			if ($now == date("Y-m-d", $v['update_time'])) {
				$toarr[] = $v['self_profit'];
			}
			$arr[] = $v['self_profit'];
		}
		$sum = array_sum($arr);
		$tosum = array_sum($toarr);
		return [
			'__code' => 'OK',
			'__message' => '',
			'sum' => $sum,
			'today_sum' => $tosum
		];
	}

	/**
	 * 我的邀请人
	 *  @param \Ziima\MVC\REST\Request $request
	 *  @request		app					int			是否安装APP
	 *  @request		start_time			int			开始时间
	 *  @request		end_time			int			结束时间
	 */
	public function my_invite(\Ziima\MVC\REST\Request $request) {

		$uid = $this->User->id;
		$sql = "SELECT u.name AS Name,u.level_v AS Vip,u.mobile AS Mobile,reg_time AS Time,u.avatar AS Img FROM `yuemi_main`.`user` AS u " .
				"LEFT JOIN `yuemi_main`.`user_wechat` AS uw ON uw.user_id = u.id ";
		$whr = [];
		if ($request->body->app > 0) {
			$whr[] = "uw.app_open_id is not null ";
		}
		if ($request->body->start_time > 0) {
			$whr[] = "u.reg_time > {$request->body->start_time} ";
		}
		if ($request->body->end_time > 0) {
			$whr[] = "u.reg_time < {$request->body->end_time} ";
		}
		$whr[] = "u.invitor_id = {$uid} ";
		if ($whr) {
			$sql .= ' WHERE ' . implode(' AND ', $whr);
		}

		$sql .= ' ORDER BY u.`id` DESC ';

		$child = $this->MySQL->grid($sql);
		if (!empty($child)) {
			foreach ($child as $arr) {
				$list['Name'] = $arr['Name'];
				$list['Vip'] = $arr['Vip'];
				$list['Mobile'] = $arr['Mobile'];
				$list['Time'] = $arr['Time'];
				$list['Img'] = URL_RES . '/upload' . $arr['Img'];
				$res[] = $list;
			}
		} else {
			$res = '';
		}
		return [
			'__code' => 'OK',
			'__message' => '',
			'List' => $res
		];
	}

	/**
	 * 销售明细
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		begin					int			开始
	 * @request		end						int			结束
	 * @request		catagory_id				int			分类
	 * @request		order					int			排序
	 */
	public function sell_info(\Ziima\MVC\REST\Request $request) {
		$ur = URL_RES;
		$sql = " SELECT `s`.`id` AS `share`,`k`.`title`,CONCAT('{$ur}','/upload',`oi`.`picture`) AS picture,`k`.`rebate_vip`,`k`.`price_sale`,`oi`.`qty`,`s`.`create_time`,`s`.`sku_id` FROM `yuemi_sale`.`order_item` AS `oi` " .
				" LEFT JOIN `yuemi_sale`.`share` AS `s` ON `oi`.`share_id` = `s`.`id` AND `s`.`sku_id` = `oi`.`sku_id` " .
				" LEFT JOIN `yuemi_sale`.`sku` AS `k` ON `k`.`id` = `s`.`sku_id` " .
				"WHERE `s`.`user_id` = {$this->User->id} ";

		$whr = [];
		if ($request->body->begin > 0) {
			$whr[] = " `s`.`create_time` > {$request->body->begin} ";
		}
		if ($request->body->end > 0) {
			$whr[] = " `s`.`create_time` < {$request->body->end} ";
		}
		if ($request->body->catagory_id > 0) {
			$pcat = $request->body->catagory_id;
			$ccat = $this->MySQL->grid("SELECT `id` FROM `yuemi_sale`.`catagory` WHERE `parent_id` = {$pcat}");
			$str = '';
			foreach ($ccat as $k => $v) {
				$str .= $v['id'] . ',';
			}
			$newstr = substr($str, 0, strlen($str) - 1);
			$laststr = $pcat . ',' . $newstr;
			$whr[] = " `k`.`catagory_id` IN ({$laststr})";
		}
		if ($whr) {
			$sql .= ' AND ' . implode(' AND ', $whr);
		}
		$res = $this->MySQL->grid($sql);
		$SkuId = [];
		foreach ($res as $k => $v) {
			if (!in_array($v['sku_id'], $SkuId)) {
				$SkuId[] = $v['sku_id'];
			}
		}
		$data = [];
		foreach ($SkuId as $k => $v) {
			$data[$v] = [];
		}
		foreach ($SkuId as $k => $v) {
			foreach ($res as $kk => $vv) {
				if ($vv['sku_id'] == $v) {
					array_push($data[$v], $vv);
				}
			}
		}

		foreach ($data as $k => $v) {
			$num = 0;
			foreach ($v as $kk => $vv) {
				$num += $vv['qty'];
				$data[$k]['Share'] = $vv['share'];
				unset($data[$k][$kk]);
			}
			$data[$k]['AllQty'] = $num;
			$data[$k]['RebateVipAll'] = $vv['rebate_vip'] * $num;
			$data[$k]['Title'] = $vv['title'];
			$data[$k]['Picture'] = $vv['picture'];
			$data[$k]['RebateVip'] = $vv['rebate_vip'];
			$data[$k]['PriceSale'] = $vv['price_sale'];
			$data[$k]['CreateTime'] = $vv['create_time'];
			$data[$k]['SkuId'] = $vv['sku_id'];
		}
		$arr = [];
		foreach ($data as $k => $v) {
			$arr[] = $v;
		}
		if ($request->body->order == 1) {
			array_multisort(array_column($arr, 'AllQty'), SORT_ASC, $arr);
		}
		if ($request->body->order == 2) {
			array_multisort(array_column($arr, 'AllQty'), SORT_DESC, $arr);
		}

		return [
			'res' => $arr
		];
	}

	/**
	 * 销售详情-买家列表
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function sell_info_buyer(\Ziima\MVC\REST\Request $request) {
		$share = $request->body->share;
		$ur = URL_RES;
		$sql = " SELECT `o`.`create_time`,`oi`.`qty`,`u`.`name`,`u`.`mobile`,CONCAT('{$ur}','/upload',`u`.`avatar`) AS `avatar`,`oi`.`sku_id`,`o`.`user_id` FROM `yuemi_sale`.`order_item` AS `oi` LEFT JOIN `yuemi_sale`.`order` AS `o` ON `o`.`id` = `oi`.`order_id` LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `o`.`user_id`" .
				" WHERE `oi`.`share_id` = {$share} ";
		$res = $this->MySQL->grid($sql);
		$user = [];
		$list = [];
		foreach ($res as $k => $v) {
			if (!in_array($v['user_id'], $user)) {
				$user[] = $v['user_id'];
				$list[$v['user_id']] = $v;
			} else {
				$list[$v['user_id']]['qty'] += $v['qty'];
			}
		}
		return [
			'list' => $list
		];
	}

	/**
	 * 快递信息
	 * @param \Ziima\MVC\REST\Request $request
	 */
	public function wuliu(\Ziima\MVC\REST\Request $request) {
		$OrderId = $request->body->order_id;
		$sql2 = "SELECT trans_com, trans_id, `status`,`address_id`,`addr_region`,`addr_detail`,`addr_name`,`addr_mobile`,`t_amount`,`t_online`,`create_time` FROM `yuemi_sale`.`order` WHERE `id` = '{$OrderId}'";
		$status = $this->MySQL->row($sql2);
		$Kd = new \Cloud\Kuaidi\Platofrm(KUAIDI_KEY, KUAIDI_TOKEN);
		$ReData = $Kd->trace($status['trans_com'], $status['trans_id']);
		if (isset($ReData['data']) && is_array($ReData['data']) && count($ReData['data']) > 0) {
			$data = $ReData;
			$com = $this->MySQL->scalar("SELECT `name` FROM `yuemi_main`.`kuaidi` WHERE `alias` = '{$data['com']}'");
			if ($com == '') {
				$com = '简称,' . $data['com'];
			}
			$nu = $data['nu'];
			$arr = [];
			$arr['time'] = '';
			$arr['context'] = '物流公司：' . $com . '&nbsp;&nbsp;快递单号：' . $nu;
			$arr['ftime'] = '';

			array_unshift($ReData['data'], $arr);
			$Result['KuaiDi'] = $ReData['data'];
		}
		return [
			'list' => $ReData
		];
	}

	private function invite_id($mid) {
		$data = $this->MySQL->grid("SELECT `id` FROM `yuemi_main`.`user` WHERE `invitor_id` = {$mid} ");
		if (!empty($data)) {
			$list = [];
			foreach ($data as $v) {
				$child = $this->invite_id($v['id']);
				$list[] = $v['id'];
				if($child){
					foreach ($child as $kk => $vv) {
						$list[] = $vv;
					}
				}
			}
			return $list;
		}
	}

	/**
	 * 直招列表
	 * @param \Ziima\MVC\REST\Request $request
	 * @request		page		int			分页
	 */
	public function select_vip(\Ziima\MVC\REST\Request $request) {
		$ur = URL_RES;
		$uid = $this->User->id;
		$sql = "SELECT `u`.`name`,CONCAT('{$ur}','/upload',`u`.`avatar`) AS `avatar`,`u`.`mobile`,`u`.`level_v`,`u`.`reg_time`,`v`.`status` FROM `yuemi_main`.`user` AS `u`" .
				" LEFT JOIN `yuemi_main`.`vip` AS `v` ON `v`.`user_id` = `u`.`id` " .
				" WHERE `u`.`invitor_id` = {$uid}";
		$res = $this->MySQL->paging($sql, 10, $request->body->page);
		$zlist = $this->MySQL->grid("SELECT `id` FROM `yuemi_main`.`user` WHERE `invitor_id` = {$uid}");
		$idlist = $this->invite_id($uid);
		$count = count($idlist);
		$zcount = count($zlist);
		return [
			'res' => $res,
			'count' => $count,
			'zcount' => $zcount
		];
	}

}
