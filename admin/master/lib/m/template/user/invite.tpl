{:include file="_g/header.tpl" Title="关系":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		邀请关系列表
	</caption>
	<tr>
		<td>
			搜索
		</td>
		<td colspan="5">
			<form action="/index.php" method="GET">
				<input type="hidden" name="call" value="{:#Z_HANDLER:}.{:#Z_ACTION:}" />
				<input type="hidden" name="p" value="{:$_PARAMS.p:}" />
				被邀请人：<input type="text"  name="u" value="{:$_PARAMS.u:}" />
				被邀请人电话：<input type="text"  name="um" value="{:$_PARAMS.um:}" />
				邀请人：<input type="text"  name="i" value="{:$_PARAMS.i:}" />
				邀请人电话：<input type="text"  name="im" value="{:$_PARAMS.im:}" />
				<input type="submit" value="搜索" />
			</form>
		</td>
	</tr>
	<tr>
		<th>id</th>
		<th>被邀请人</th>
		<th>被邀请人电话</th>
		<th>邀请人</th>
		<th>邀请人电话</th>
		<!--<th>操作</th>-->
	</tr>
			{:foreach from=$res->Data value=v:}
	<tr>
		<td>{:$v.uid:}</td>
		<td><a href="/index.php?call=user.vipinv_pic&uid={:$v.uid:}">{:$v.name | string.key_highlight $_PARAMS.u:}</a></td>
		<td>{:$v.mobile | string.key_highlight $_PARAMS.um:}</td>
		<td><a href="/index.php?call=user.vipinv_pic&uid={:$v.iid:}">{:$v.iname | string.key_highlight $_PARAMS.i:}</a></td>
		<td>{:$v.imobile | string.key_highlight $_PARAMS.im:}</td>
		<!--<td><a href="/index.php?call=user.vipinv_pic&uid={:$v.uid:}">查看族谱</a>-->
		</td>
	</tr>
		{:/foreach:}
	<tr class="paging">
		<td colspan="20">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>
</table>
{:include file="_g/footer.tpl":}