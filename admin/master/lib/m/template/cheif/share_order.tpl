{:include file="_g/header.tpl" Title="总监":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		分享订单列表
	</caption>
	<tr>
		<th>ID</th>
		<th>商品名</th>
		<th>缩略图</th>
		<th>购买人</th>
		<th>分类</th>
		<th>供应商</th>
		<th>数量</th>
		<th>单价</th>
		<th>总价</th>
		<th>返佣用户</th>
		<th>返佣金额</th>
	</tr>
	{:foreach from=$res->Data value=v:}
	<tr>
		<td class="zid">{:$v.id:}</td>
		<td>{:$v.title:}</td>
		<td><img src="https://r.yuemee.com/upload{:$v.picture:}" style="width:100px;height:80px;" class="imgs"></td>
		<td>{:$v.buyname:}</td>
		<td>{:$v.catname:}</td>
		<td>{:$v.suname:}</td>
		<td>{:$v.qty:}</td>
		<td>￥{:$v.price:}</td>
		<td>￥{:$v.money:}</td>
		<td>{:$v.rename:}</td>
		<td>{:$v.rebate_vip:}</td>
	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="11">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>
</table>
<img id="imgb" src="" style="width:300px;height:300px;z-index:999;position:absolute;display:none;left:0px;top:0px;" />
<script>
	$('.imgs').mouseover(function () {
		var X = $(this).offset().left;
		var Y = $(this).offset().top;
		$('#imgb').css('left', X - 50 + 'px').css('top', Y + 'px');
		$('#imgb').attr('src', $(this).attr('src')).show();
	}).mouseout(function () {
		$('#imgb').hide();
	});
</script>
{:include file="_g/footer.tpl":}
