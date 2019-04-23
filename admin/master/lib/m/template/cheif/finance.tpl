{:include file="_g/header.tpl" Title="总监":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		总监账户
	</caption>
	<tr>
		<th>id</th>
		<th>Userid</th>
		<th>姓名</th>
		<th>手机</th>
		<th>间接招聘佣金</th>
		<th>团队管理佣金</th>
		<th>伯乐奖/招</th>
		<th>伯乐奖/销</th>
		<th>礼包佣金状态</th>
		<th>解冻时间</th>
		<th>操作</th>
	</tr>
	{:foreach from=$data->Data value=v:}
	<tr>
		<td>{:$v.id:}</td>
		<td>{:$v.user_id:}</td>
		<td>{:$v.name:}</td>
		<td>{:$v.mobile:}</td>
		<td>{:$v.recruit_self:}</td>
		<td>{:$v.deduct_self:}</td>
		<td>{:$v.recruit_bole:}</td>
		<td>{:$v.deduct_bole:}</td>
		<td>{:if $v.thew_status == 0:}冻结{:else:}解冻{:/if:}</td>
		<td>{:$v.thew_time:}</td>
		<td>
			{:if $v.thew_status == 0:}
			<a>解冻</a>
			{:else:}
			<a>冻结</a>
			{:/if:}
		</td>
	</tr>
	{:/foreach:}
	<!--tr class="summary">
		<td>小计</td>
		<td>￥123.45</td>
		<td colspan="3"></td>
	</tr-->
	<tr class="pager">
		<td colspan="12">
			{:include file="_g/pager.tpl" Result=$data:}
		</td>
	</tr>
</table>

{:include file="_g/footer.tpl":}
