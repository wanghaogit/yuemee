{:include file="_g/header.tpl" Title="银行卡":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		用户银行卡管理
	</caption>
	<tr>
		<td>
			搜索
		</td>
		<td colspan="13">
			<form action="/index.php" method="GET">
				<input type="hidden" name="call" value="{:#Z_HANDLER:}.{:#Z_ACTION:}" />
				<input type="hidden" name="p" value="{:$_PARAMS.p:}" />
				用户名：<input type="text"  name="n" value="{:$_PARAMS.n:}" />
				卡号：<input type="text"  name="c" value="{:$_PARAMS.c:}" />
				<input type="submit" value="搜索" />
			</form>
		</td>
	</tr>
	<tr>
		<th>id</th>
		<th>用户id</th>
		<th>用户名</th>
		<th>银行id</th>
		<th>银行名称</th>
		<th>开放地区id</th>
		<th>开户行名称</th>
		<th>卡号</th>
		<th>用户状态</th>
		<th>创建时间</th>
		<th>创建IP</th>
		<th>审核人</th>
		<th>审核时间</th>
		<th>操作</th>
	</tr>
	{:foreach from=$list->Data value=B:}
		<tr>
			<td>{:$B.id:}</td>
			<td>{:$B.user_id:}</td>
			<td>{:$B.user_name | string.key_highlight $_PARAMS.n:}</td>
			<td>{:$B.bank_id:}</td>
			<td>{:$B.name:}</td>
			<td>{:$B.province:}-{:$B.city:}-{:$B.country:}</td>
			<td>{:$B.bank_name:}</td>
			<td>{:$B.card_no | string.key_highlight $_PARAMS.c:}</td>
			<td>{:if $B.status == 0 :}<span>删除</span>{: elseif $B.status == 1 :}<span>可用</span>{: elseif $B.status == 2 :}<span>正确</span>{: else :}<span>错误</span>{: /if :}</td>
			<td>{:$B.create_time | number.datetime:}</td>
			<td>{:$B.create_from:}</td>
			<td>{:$B.aname:}</td>
			<td>{:$B.audit_time | number.datetime:}</td>
			<td><a href="/index.php?call=user.user_bank_info&id={:$B.user_id:}">修改</a></td>
		</tr>
	{:/foreach:}
	<tr class="pager">
		<td colspan="14">
			{:include file="_g/pager.tpl" Result=$list:}
		</td>
	</tr>
</table>

{:include file="_g/footer.tpl":}