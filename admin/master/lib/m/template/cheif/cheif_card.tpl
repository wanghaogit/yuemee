{:include file="_g/header.tpl" Title="总监":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		总监卡片列表
	</caption>
	<tr>
		<th>ID</th>
		<th>卡号</th>
		<th>金额</th>
		<th>使用者ID</th>
		<th>使用者名称</th>
		<th>使用者手机号</th>
		<th>状态</th>
		<th>使用时间</th>
	</tr>
	{:foreach from=$Data->Data value=v:}
	<tr>
		<td class="zid">{:$v.id:}</td>
		<td>{:$v.serial:}</td>
		<td>{:$v.money:}</td>
		<td align="right">{:$v.rcv_user_id:}</td>
		<td align="center">{:$v.Uname:}</td>
		<td align="center">{:$v.rcv_mobile	:}</td>
		<td>{:if $v.status == 0:}未使用{:else:}已使用{:/if:}</td>
		<td>{:if $v.used_time == 0:}{:else:}{:$v.used_time | number.date:}{:/if:}</td>
	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="8">
			{:include file="_g/pager.tpl" Result=$Data:}
		</td>
	</tr>
</table>

{:include file="_g/footer.tpl":}
