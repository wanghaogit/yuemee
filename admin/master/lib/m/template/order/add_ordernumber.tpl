{:include file="_g/header.tpl" Title="订单":}
<form action='/index.php?call=order.add_ordernumber' method='post'>
	<input type="hidden" name="id" value="{:$res.id:}"/>
	<input type="text" name="trans_id" value="{:$res.trans_id:}" />
	<input type='submit' value='修改' />
</form>
{:include file="_g/footer.tpl":}