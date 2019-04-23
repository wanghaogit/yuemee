{:include file="_g/header.tpl" Title="财务/阅币流水":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		阅币流水记录
	</caption>
	<tr>
		<th rowspan="2">ID</th>
		<th colspan="2">用户</th>
		<th>来源</th>
		<th colspan="3">资金</th>
		<th rowspan="2">时间</th>
		<th rowspan="2">备注</th>
	</tr>
	<tr>
		<th>昵称</th>
		<th>手机</th>
		<th>渠道</th>
	
		<th>变动</th>
		<th>原始值</th>
		<th>结果值</th>
	</tr>
	{:foreach from=$Result->Data item=R:}
		<tr>
			<td>{:$R.id:}</td>
			<td>{:$R.user_id:}</td>
			<td></td>
			<td align="center">{:$R.source:}</td>
			
			<td align="right" style="{:if $R.val_delta < 0:}color:#933;{:else:}color:#393;{:/if:}font-weight:700;">{:$R.val_delta:}</td>
			<td align="right">{:$R.val_before:}</td>
			<td align="right">{:$R.val_after:}</td>
			<td align="center">{:$R.create_time:}</td>
			<td>{:$R.message:}</td>
		</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="10">
			{:include file="_g/pager.tpl" Result=$Result:}
		</td>
	</tr>

</table>

{:include file="_g/footer.tpl":}
