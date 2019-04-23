{:include file="_g/header.tpl" Title="VIP":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		分享佣金列表
	</caption>
	<tr>
		<td>
			搜索
		</td>
		<td colspan="11">
			<form action="/index.php" method="GET">
				<input type="hidden" name="call" value="{:#Z_HANDLER:}.{:#Z_ACTION:}" />
				<input type="hidden" name="p" value="{:$_PARAMS.p:}" />
			
				<input type="submit" value="搜索" />
			</form>
		</td>
	</tr>
	<tr>
		<th>详情订单</th>
		<th>主订单</th>
		<th>商品</th>
		<th>缩略图</th>
		<th>购买人</th>
		<th>分享人</th>
		<th>归属总监</th>
		<th>归属总经理</th>
		<th>状态</th>
		<th>创建时间</th>
		<th>佣金</th>
		<th>备用</th>
	</tr>
	{:foreach from=$res->Data value=v:}
		<tr>
			<td>{:$v.item_id:}</td>
			<td>{:$v.order_id:}</td>
			<td>{:$v.title:}</td>
			<td><img src="https://r.yuemee.com/upload{:$v.file_url:}" style="width:200px;height:200px;" class="imgs" /></td>
			<td>{:$v.buyname:}</td>
			<td>{:$v.sharename:}</td>
			<td>{:$v.cheifname:}</td>
			<td>{:$v.directorname:}</td>
			<td>{:$v.status:}</td>
			<td>{:$v.create_time:}</td>
			<td>
				总可支配佣金：{:$v.total_profit:}<br/>
				平台返利金额：{:$v.system_profit:}<br/>
				自己返利金额：{:$v.self_profit:}<br/>
				分享返利金额：{:$v.share_profit:}<br/>
				经理返利比例：{:$v.cheif_ratio:}<br/>
				总监返利比例：{:$v.cheif_profit:}<br/>
				经理返利比例：{:$v.director_ratio:}<br/>
				经理返利金额：{:$v.director_profit:}
			</td>
			<td></td>
		</tr>
	{:/foreach:}
	<tr class="pager">
		<td colspan="12">
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