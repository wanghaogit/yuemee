<?php
/**
 * 各种状态机
 */
class StateMachine
{
	/**
	 * 订单状态列表
	 * @parem $ArrayFlip 是否对调Key和Value（正常情况下为 num->name 对调后为 name->num）
	 * @return array
	 */
	public static function order($ArrayFlip = false)
	{
		$list['-1'] = "购物车";
		$list[0] = "新订单";
		$list[1] = "待支付";
		$list[2] = "已支付";
		$list[3] = "未知";
		$list[4] = "待发货";
		$list[5] = "运输中";
		$list[6] = "已签收";
		$list[7] = "已确认";
		$list[8] = "已评价";
		$list[11] = "主动关闭";
		$list[12] = "后台关闭";
		$list[13] = "退款关闭";
		$list[14] = "后台取消";
		$list[15] = "供应商关闭";
		$list[16] = "物流丢件";
		$list[17] = "丢件确认";
		$list[18] = "丢件退款";
		$list[21] = "售后申请";
		$list[22] = "同意退货";
		$list[23] = "拒绝退货";
		$list[24] = "售后完成";
		$list[25] = "售后评价";
		$list[31] = "售后申请";
		$list[32] = "同意退货";
		$list[33] = "拒绝退货";
		$list[34] = "售后完成";
		$list[35] = "售后评价";
		if ($ArrayFlip == true) {
			return array_flip($list);
		}
		return $list;
	}
}
