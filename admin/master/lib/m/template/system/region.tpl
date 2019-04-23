{:include file="_g/header.tpl" Title="系统/地区":}

<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		系统地区
		{:if $Current:}
			<a href="/index.php?call=system.region&t={:$Current.id | string.cut 0,2:}0000">返回</a>
		{:/if:}
	</caption>
	<tr>
		<th>模板ID</th>
		<th>省/市</th>
		<th>市/辖</th>
		<th>县/区</th>
		<th>区/镇</th>
	</tr>
	{:foreach from=$Result->Data item=R:}
		<tr>
			<td><a href="/index.php?call=system.region&t={:$R.id:}">{:$R.id:}</a></td>
			<td>{:$R.province:}</td>
			<td align="center">{:$R.city:}</td>
			<td align="center">{:$R.country:}</td>
			<td align="center">{:$R.district:}</td>
		</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="10">
			{:include file="_g/pager.tpl" Result=$Result:}
		</td>
	</tr>

</table>


{:include file="_g/footer.tpl":}
