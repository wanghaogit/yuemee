{:include file="_g/header.tpl" Title="经理":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		<a href="/index.php?call=director.create" class="button" style="float:left;">录入总经理</a>
		<a href="/index.php?call=cheif.cheif_card" class="button" style="float:right;">总监卡</a>
		经理列表
	</caption>
	<tr>
		<td>
			搜索
		</td>
		<td colspan="8">
			<form action="/index.php" method="GET">
				<input type="hidden" name="call" value="{:#Z_HANDLER:}.{:#Z_ACTION:}" />
				<input type="hidden" name="p" value="{:$_PARAMS.p:}" />
				昵称：<input type="text"  name="n" value="{:$_PARAMS.n:}" />
				手机：<input type="text"  name="m" value="{:$_PARAMS.m:}" />
				到期时间：
				<input type="text" class="input-date" id="search_time_start" name="search_time_start" readonly="readonly" value="{:$search_time_start | number.datetime:}" />
				-
				<input type="text" class="input-date" id="search_time_end" name="search_time_end" readonly="readonly" value="{:$search_time_end | number.datetime:}" />
				
				开通时间：
				<input type="text" class="input-date" id="search_time_start" name="search_time_start2" readonly="readonly" value="{:$search_time_start2 | number.datetime:}" />
				-
				<input type="text" class="input-date" id="search_time_end" name="search_time_end2" readonly="readonly" value="{:$search_time_end2 | number.datetime:}" />
				
				<input type="submit" value="搜索" />
				
			</form>
		</td>
	</tr>
	<tr>
		<th>ID</th>
		<th>UserID</th>
		<th>昵称</th>
		<th>实名</th>
		<th>手机</th>
		<th>状态</th>
		<th>总经理到期时间</th>
		<th>操作</th>
	</tr>
	{:foreach from=$data->Data value=v:}
	<tr>
		<td class="zid">{:$v.user_id:}</td>
		<td>{:$v.user_id:}</td>
		<td><a href="/index.php?call=cheif.cheif_card&uid={:$v.user_id:}">{:$v.name | string.key_highlight $_PARAMS.n:}</a></td>
		<td>{:$v.cname:}</td>
		<td align="right">{:$v.mobile | string.key_highlight $_PARAMS.m:}</td>
		<td>{:$v.status | boolean.iconic:}</td>
		<td>{:$v.expire_time:}</td>
		<td><a href="/index.php?call=director.directorinv_pic&uid={:$v.user_id:}">查看族谱</a>
			|
			<a href="/index.php?call=cheif.share_order&uid={:$v.user_id:}">分享订单</a>
			|
			<a href="/index.php?call=cheif.share_good&uid={:$v.user_id:}">分享商品</a>
			|
			<a href="/index.php?call=user.share_money&uid={:$v.user_id:}">销售佣金</a>
		</td>
	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="20">
			{:include file="_g/pager.tpl" Result=$data:}
			<b style='float:right;line-height:30px;margin-right:30px;'>共计：{:$sum['sum']:}&nbsp;&nbsp;&nbsp;</b>
		</td>
	</tr>
</table>
<script>
	$(".input-date").datetimepicker({
		autoclose: true,
		clearBtn: true,
		todayBtn: true,
		todayHighlight: true,
		fontAwesome: true,
		zIndex: 9999,
		format: 'yyyy-mm-dd hh:ii'
	});
</script>
{:include file="_g/footer.tpl":}
