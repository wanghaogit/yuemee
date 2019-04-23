{:include file="_g/header.tpl" Title="总监":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		{:$good:}-获得佣金
	</caption>
	<tr>
		<th>Item-ID</th>
		<th>Order-ID</th>
		<th>总支配佣金</th>
		<th>平台返利</th>
		<th>自己返利</th>
		<th>分享返利</th>
		<th>总监返利</th>
		<th>总监返利比例</th>
		<th>经理返利</th>
		<th>经理返利比例</th>
		<th>状态</th>
		<th>创建时间</th>
	</tr>
	{:foreach from=$res->Data value=v:}
	<tr>
		<td>{:$v.item_id:}</td>
		<td>{:$v.order_id:}</td>
		<td>￥{:$v.total_profit:}</td>
		<td>￥{:$v.system_profit:}</td>
		<td>￥{:$v.self_profit:}</td>
		<td>￥{:$v.share_profit:}</td>
		<td>￥{:$v.cheif_profit:}</td>
		<td>￥{:$v.cheif_ratio:}</td>
		<td>￥{:$v.director_profit:}</td>
		<td>￥{:$v.director_ratio:}</td>
		<td>{:$v.status:}</td>
		<td>{:$v.create_time | number.datetime:}</td>
	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="12">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>
</table>

{:include file="_g/footer.tpl":}
