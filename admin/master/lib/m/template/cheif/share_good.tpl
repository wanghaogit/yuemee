{:include file="_g/header.tpl" Title="总监":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		{:$my.name:}-分享商品列表
	</caption>
	<tr>
		<th>ID</th>
		<th>商品名</th>
		<th>缩略图</th>
		<th>分享人</th>
		<th>分享时间</th>
		<th>操作</th>
			{:foreach from=$res value=v:}
	<tr>
		<td class="zid">{:$v.id:}</td>
		<td>{:$v.title:}</td>
		<td><img src="{:$v.image_url:}" style="width:100px;height:80px;" class="imgs"></td>
		<td>{:$my.name:}</td>
		<td>{:$v.create_time | number.datetime:}</td>
		<td>
			<a href="/index.php?call=cheif.share_good_order&uid={:$my.id:}&sku_id={:$v.sku_id:}">关联订单</a>
			|
			<a href="/index.php?call=cheif.share_good_get_money&uid={:$my.id:}&sku_id={:$v.sku_id:}">获得佣金</a>
		</td>
	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="6">

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
