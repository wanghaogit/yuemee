{:include file="_g/header.tpl" Title="用户":}
<link rel="stylesheet" type="text/css" href="/styles/user/usershow.css" />
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>广告用户</caption>
	<tr>
		<th>ID</th>
		<th>来源</th>
		<th>手机</th>
		<th>姓名</th>
		<th>微信</th>
		<th>订单号</th>
		<th>地区</th>
		<th>地址</th>
		<th>记录时间</th>
		<th>状态</th>
	</tr>
	{:foreach from=$res->Data value=v:}
	<tr>
		<td>{:$v.id:}</td>
		<td>{:$v.source:}</td>
		<td>{:$v.mobile:}</td>
		<td>{:$v.name:}</td>
		<td>{:$v.weixin:}</td>
		<td>{:$v.order_id:}</td>
		<td>{:$v.province:}-{:$v.city:}-{:$v.country:}</td>
		<td>{:$v.address:}</td>
		<td>{:$v.create_time | number.datetime:}</td>
		<td>{:$v.status | boolean.iconic:}</td>
	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="10">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>
</table>
<script>

</script>
{:include file="_g/footer.tpl":}
