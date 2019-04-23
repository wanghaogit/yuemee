{:include file="_g/header.tpl" Title="财务/阅币流水":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		招聘费流水记录（{:if $t == 1:}直接{:else:}间接{:/if:}）
	</caption>
	<tr>
		<th>ID</th>
		<th>用户ID</th>
		<th>用户名</th>
		<th>目标子账户</th>
		<th>资金来源</th>
		<th>关联ID</th>
		<th>原始值</th>
		<th>变动</th>
		<th>结果值</th>	
		<th>创建时间</th>
		<th>创建IP</th>
		<th>变化原因</th>
	</tr>
	{:foreach from=$Result->Data item=R:}
		<tr>
			<td>{:$R.id:}</td>
			<td>{:$R.user_id:}</td>
			<td>{:$R.name:}</td>
			<td align="center">{:$R.target:}</td>
			<td align="center">{:$R.source:}</td>
			<td align="center">{:$R.source_id:}</td>
			<td align="right">{:$R.val_before:}</td>
			<td align="right" style="{:if $R.val_delta < 0:}color:#933;{:else:}color:#393;{:/if:}font-weight:700;">{:$R.val_delta:}</td>
			<td align="right">{:$R.val_after:}</td>
			<td align="center">{:$R.create_time:}</td>
			<td>{:$R.create_from:}</td>
			<td align="right">{:$R.message:}</td>
		</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="12">
			{:include file="_g/pager.tpl" Result=$Result:}
		</td>
	</tr>

</table>

{:include file="_g/footer.tpl":}
