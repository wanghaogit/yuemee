{:include file="_g/header.tpl" Title="分享":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		分享列表
	</caption>
	<tr>
		<td>
			搜索
		</td>
		<td colspan="13">
			<form action="/index.php" method="GET">
				<input type="hidden" name="call" value="{:#Z_HANDLER:}.{:#Z_ACTION:}" />
				<input type="hidden" name="p" value="{:$_PARAMS.p:}" />
				用户：<input type="text"  name="n" value="{:$_PARAMS.n:}" />
				商品名：<input type="text"  name="g" value="{:$_PARAMS.g:}" />
				<input type="submit" value="搜索" />
			</form>
		</td>
	</tr>
	<tr>
		<th>ID</th>
		<th>用户</th>
		<th>分享归属总监理	</th>
		<th>分享所属团队</th>
		<th>使用模板ID</th>
		<th>分享商品名称</th>
		<th>分享文案</th>
		<th>图片</th>
		<th>时间</th>
	</tr>
	{:foreach from=$data->Data value=v:}
	<tr>
		<td>{:$v.id:}</td>
		<td>{:$v.Name | string.key_highlight $_PARAMS.n:}</td>
		<td>{:$v.Dname:}</td>
		<td>{:$v.Tname:}</td>
		<td>{:$v.template_id:}</td>
		<td>{:$v.Title | string.key_highlight $_PARAMS.g:}</td>
		<td>{:$v.title:}</td>
		<td><img src="{:$v.image_url:}" style="width:55px;height: 55px;" class="imgs"/></td>
		<td>{:$v.create_time | number.datetime:}</td>
	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="20">
			{:include file="_g/pager.tpl" Result=$data:}
		</td>
	</tr>
</table>
<img id="imgb" src="" style="width:300px;height:500px;z-index:999;position:absolute;display:none;left:0px;top:0px;" />
<script>
	$('.imgs').mouseover(function () {
		var X = $(this).offset().left;
		var Y = $(this).offset().top;
		$('#imgb').css('left', X - 500 + 'px').css('top', Y + 'px');
		$('#imgb').attr('src', $(this).attr('src')).show();
	}).mouseout(function () {
		$('#imgb').hide();
	});
</script>
{:include file="_g/footer.tpl":}
