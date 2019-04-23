{:include file="_g/header.tpl" Title="系统":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		短信记录
	</caption>
	<tr>
		<th>Id</th>
		<th>类型</th>
		<th>手机号</th>
		<th>验证码</th>
		<th>发送时间</th>
		<th>过期时间</th>
	</tr>
	{:foreach from=$DataList item=Data:}
		<tr>
			<td>{:$Data->id:}</td>
			<td>{:$Data->type:}</td>
			<td>{:$Data->mobile:}</td>
			<td>{:$Data->code:}</td>
			<td>{:$Data->create_time | number.datetime:}</td>
			<td>{:$Data->expire_time | number.datetime:}</td>
		</tr>
	{:/foreach:}
</table>
{:include file="_g/footer.tpl":}
