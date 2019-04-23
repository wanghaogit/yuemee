{:include file="_g/header.tpl" Title="VIP":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption style="text-align: left;">
		VIP管理
	</caption>
	<tr>
		<th>id</th>
		<th>用户id</th>
		<th>用户名</th>
		<th>购买订单id</th>
		<th>钻石流水id</th>
		<th>支付钻石</th>
		<th>创建时间</th>
		<th>到期时间</th>
		<th>操作</th>
	</tr>
	{:foreach from=$res->Data value=B:}
	<tr>
		<td>{: $B.id :}</td>
		<td>{: $B.user_id :}</td>
		<td>{: $B.name :}</td>
		<td><a href="#">{: $B.order_id :}</a></td>
		<td>{: $B.tally_id :}</td>
		<td>{: $B.coin :}</td>
		<td>{: $B.create_time :}</td>
		<td>{: $B.expire_time :}</td>
		<td></td>
	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="20">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>
</table>

{:include file="_g/footer.tpl":}