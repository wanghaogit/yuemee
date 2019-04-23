{:include file="_g/header.tpl" Title="VIP":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		用户账目
	</caption>
	<tr>
		<td>
			搜索
		</td>
		<td colspan="12">
			<form action="/index.php" method="GET">
				<input type="hidden" name="call" value="{:#Z_HANDLER:}.{:#Z_ACTION:}" />
				<input type="hidden" name="p" value="{:$_PARAMS.p:}" />
				用户昵称：<input type="text"  name="n" value="{:$_PARAMS.n:}" />
				手机：<input type="text"  name="m" value="{:$_PARAMS.m:}" />
				<input type="submit" value="搜索" />
			</form>
		</td>
	</tr>
	<tr>
		<th rowspan="2">用户ID</th>
		<th rowspan="2">昵称</th>
		<th rowspan="2">手机</th>
		<th rowspan="2">实名</th>
		<th rowspan="2">余额</th>
		<th rowspan="2">阅币</th>
		<th colspan="4">招聘佣金</th>
		<th colspan="3">销售佣金</th>
	</tr>
	<tr>
		<th>直接</th>
		<th>间接</th>
		<th>解冻</th>
		<th>时间</th>
		<th>自买</th>
		<th>分享</th>
		<th>团队</th>
	</tr>
	{:foreach from=$list->Data value=F:}
		<tr>
			<td>{:$F.user_id:}</td>
			<td>{:$F.user_name | string.key_highlight $_PARAMS.n:}</td>
			<td>{:$F.user_mobile | string.key_highlight $_PARAMS.m:}</td>
			<td>{:$F.card_name:}</td>
			<td align="right"><a href="/index.php?call=finance.tally_money&uid={:$F.user_id:}">{:$F.money:}</a></td>
			<td align="right"><a href="/index.php?call=finance.tally_coin&uid={:$F.user_id:}">{:$F.coin:}</td>
			
			<td align="right"><a href="/index.php?call=finance.tally_recruit&t=1&uid={:$F.user_id:}">{:$F.recruit_dir:}</a></td>
			<td align="right"><a href="/index.php?call=finance.tally_recruit&t=2&uid={:$F.user_id:}">{:$F.recruit_alt:}</a></td>
			<td align="center">{:$F.thew_status | boolean.iconic:}</td>
			<td>{:if $F.thew_launch != '0000-00-00 00:00:00':}{:$F.thew_launch:}{:/if:}</td>
			
			<td align="right"><a href="/index.php?call=finance.tally_profit&t=1&uid={:$F.user_id:}">{:$F.profit_self:}</a></td>
			<td align="right"><a href="/index.php?call=finance.tally_profit&t=2&uid={:$F.user_id:}">{:$F.profit_share:}</a></td>
			<td align="right"><a href="/index.php?call=finance.tally_profit&t=3&uid={:$F.user_id:}">{:$F.profit_team:}</a></td>
		</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="19">
			{:include file="_g/pager.tpl" Result=$list:}
		</td>
	</tr>
</table>

{:include file="_g/footer.tpl":}