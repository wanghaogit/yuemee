{:include file="_g/header.tpl" Title="订单":}
<form action="/index.php?call=order.regive" method="post">
	<input name="" />
	补发物流单号：<input type="text" name="fix_trans" />
	补发消息：<input type="text" name="fix_message" />
	收货地址：<input type="text" name="fix_message" />
	地区：<input type="text" name="fix_message" />
	详细地址：<input type="text" name="fix_message" />
	联系人：<input type="text" name="fix_message" />
	联系电话：<input type="text" name="fix_message" />
	<input type="submit" value="发货" />
</form>
{:include file="_g/footer.tpl":}