{:include file="_g/header.tpl" Title="运营/组件":}
<style>

</style>
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
        热搜管理
        <a class="button button-blue" style="float:left;" onclick="newone()">
			<i class="fas fa-plus"></i> 新增热搜
		</a>
    </caption>
	<tr>
		<th>ID</th>
		<th>热搜名</th>
		<th>显示颜色</th>
		<th>字体尺寸</th>
		<th>排序</th>
		<th>操作</th>
	</tr>
	{:foreach from=$res value=v:}
	<tr>
		<td>{:$v.id:}</td>
		<td>{:$v.title:}</td>
		<td style="color:{:$v.color:};">{:$v.color:}</td>
		<td style="font-size:{:$v.size:}px;">{:$v.size:}px</td>
		<td>{:$v.p_order:}</td>
		<td><a href="/index.php?call=runer.update_hot&id={:$v.id:}">修改</a> | <a style="color:red;" onclick="del({:$v.id:})">删除</a></td>

	</tr>
	{:/foreach:}

</table>

<script type="text/javascript">
	function newone(id, name) {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '400px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fas fa-edit',
			title: '新增热搜',
			content: '名称：<input type="text" name="title" id="title"><br>颜色：<input type="text" name="color" id="color"><br>尺寸：<input type="text" name="size" id="size"><br>排序：<input type="text" name="p_order" id="p_order">',
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '新增',
					action: function () {
						YueMi.API.Admin.invoke('runer', 'new_hot', {
							__access_token: '{:$User->token:}',
							title: $('#title').val(),
							color: $('#color').val(),
							size: $('#size').val().trim(),
							p_order: $('#p_order').val().trim()
						}, function (t, r, q) {
							location.reload();
						}, function (t, r, q) {
							alert(q.__message);
						});
					}
				},
				cancel: {text: '取消', btnClass: 'btn-blue', action: function () {}}
			}
		});
	}
	function del(id) {
		YueMi.API.Admin.invoke('runer', 'del_hot', {
			__access_token: '{:$User->token:}',
			id : id
		}, function (t, r, q) {
			location.reload();
		}, function (t, r, q) {
			alert(q.__message);
		});
	}
</script>
{:include file="_g/footer.tpl":}
