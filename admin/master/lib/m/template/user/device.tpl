{:include file="_g/header.tpl" Title="VIP":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		手机设备
	</caption>
	<tr>
		<td>搜索</td>
		<td colspan="10">
			<form action="/index.php" method="GET">
				<input type="hidden" name="call" value="{:#Z_HANDLER:}.{:#Z_ACTION:}" />
				<input type="hidden" name="p" value="{:$_PARAMS.p:}" />
				类型：
				<select name="t">
					<option value="-1">--请选择--</option>
					<option value="1" {:if $_PARAMS.t == 1:}selected="selected"{:else:}{:/if:}>安卓</option>
					<option value="2" {:if $_PARAMS.t == 2:}selected="selected"{:else:}{:/if:}>苹果</option>
				</select>
				品牌：
				<select name="b">
					<option value="0">请选择</option>
					{:foreach from=$brand value=v:}
					<option value="{:$v.id:}" {:if $_PARAMS.b == $v.id:}selected="selected"{:else:}{:/if:}>{:$v.name:}</option>
					{:/foreach:}
				</select>
				<input type="submit" value="搜索" />
			</form>
		</td>
	</tr>
	<tr>
		<th>编号</th>
		<th>类型</th>
		<th>品牌</th>
		<th>型号</th>
		<th>识别码</th>
		<th>系统版本</th>
		<th>APP版本</th>
		<th>OA版本</th>
		<th>所在位置</th>
		<th>注册时间</th>
		<th>更新时间</th>
	</tr>
	{:foreach from=$Result->Data value=D:}
		<tr>
			<td>{:$D.id:}</td>
			<td>{: if $D.type == 0:}未知{: elseif $D.type == 1:}安卓{: elseif $D.type == 2:}苹果{: /if :}</td>
			<td>{:$D.dname:}</td>
			<td>{:$D.sname:}</td>
			<td>{:$D.udid:}</td>
			<td>{:$D.version_sys:}</td>
			<td>{:$D.version_app:}</td>
			<td>{:$D.version_oa:}</td>
			<td>{:$D.province:}-{:$D.city:}-{:$D.country:}</td>
			<td>{:$D.create_time | number.datetime:}</td>
			<td>{:$D.update_time | number.datetime:}</td>
		</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="13">
			{:include file="_g/pager.tpl" Result=$Result:}
		</td>
	</tr>
</table>

{:include file="_g/footer.tpl":}