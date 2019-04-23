{:include file="_g/header.tpl" Title="VIP":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		用户地址列表
	</caption>
	<tr>
		<td>
			搜索
		</td>
		<td colspan="8">
			<form action="/index.php" method="GET">
				<input type="hidden" name="call" value="{:#Z_HANDLER:}.{:#Z_ACTION:}" />
				<input type="hidden" name="p" value="{:$_PARAMS.p:}" />
				用户姓名：<input type="text"  name="n" value="{:$_PARAMS.n:}" />
				联系电话：<input type="text"  name="m" value="{:$_PARAMS.m:}" />
				地区：<input type="text" class="input-region" id="region" name="r" value="{:$_PARAMS.r:}"/>
				<script>
					$('#region').createRegionSelector({
						level: 'country'
					});
				</script>
				<input type="submit" value="搜索" />
			</form>
		</td>
	</tr>
	<tr>
		<th>id</th>
		<th>用户id</th>
		<th>用户名</th>
		<th>地区</th>
		<th>详细地址</th>
		<th>联系人</th>
		<th>联系电话</th>
		<th>状态</th>
		<th>操作</th>
	</tr>
	{:foreach from=$list->Data value=developer:}
		<tr>
			<td class="zid">{:$developer.id:}</td>
			<td class="zid">{:$developer.user_id:}</td>
			<td class="zid">{:$developer.uname | string.key_highlight $_PARAMS.n:}</td>
			<td class="zid">{:$developer.province:}-{:$developer.city:}-{:$developer.country:}</td>
			<td class="zid">{:$developer.address:}</td>
			<td class="zid">{:$developer.contacts:}</td>
			<td class="zid">{:$developer.mobile | string.key_highlight $_PARAMS.m:}</td>
			<td class="zid">{:$developer.status | boolean.iconic:}</td>
			<td class="zid"><a href="/index.php?call=user.user_address_info&id={:$developer.user_id:}">修改</a></td>
		</tr>
	{:/foreach:}
	<tr class="pager">
		<td colspan="9">
			{:include file="_g/pager.tpl" Result=$list:}
		</td>
	</tr>
</table>

{:include file="_g/footer.tpl":}