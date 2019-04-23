{:include file="_g/header.tpl" Title="资讯栏目管理":}

<form name="form1" id="form1" action="/index.php?call=cms.column" method="post">
	<ul class="Form">
		<li>
			<label>栏目名称：</label>
			<input type="text" id="name" name="name"/>
		</li>
		<li>
			<label>上级栏目：</label>
			<select name="cms_id" id="cms_id"/>
				<option value="0">-- 请选择 --</option>
				{:foreach from=$res value=v:}
					<option value="{:$v.id:}">{:$v.name:}</option>
				{:/foreach:}
			</select>
		</li>

		<li>
			<input type="button" value="保存" onclick="javascript:check1();" />
		</li>
	</ul>
</form>
<table border="0" cellspacing="0" cellpadding="0" class="Grid" id="table">
    <tr>
        <th>ID</th>
		<th>父类ID</th>
		<th>分类名称</th>
        <th>操作</th>
    </tr>
	{:foreach from=$res value=v:}
		<tr>
			<td>{:$v.id:}</td>
			<td>{:$v.parent_id:}</td>
			<td>{:$v.name:}</td>

			<td>
				<a href="javascript:void(0);" onclick="javascript:drop_press('{:$v.id:}')">删除</a>
			</td>
		</tr>
    {:/foreach:}
</table>
<script>
	function check1()
	{
		if ($('#name').val() == '')
		{
			alert('请输入分类名称');
			return;
		}
		YueMi.API.Admin.invoke('cms', 'column_add', {
			__access_token: '{:$User->token:}',
			cms_id : $('#cms_id').val().trim(),
			name : $('#name').val().trim()
		}, function (t, r, q) {
			location.href = '/index.php?call=cms.column';
		}, function (t, r, q) {
			alert(q.__message);
		});
	}

	function drop_press(id) {
		if (id < 10) {
			alert("不允许操作");
		} else {
			$.confirm({
				useBootstrap: false,
				type: 'blue',
				boxWidth: '300px',
				escapeKey: 'cancel',
				backgroundDismiss: false,
				backgroundDismissAnimation: 'glow',
				icon: 'fa fa-shield',
				title: '删除栏目',
				content: '删除吗？',
				buttons: {
					accept: {
						btnClass: 'btn-red',
						text: '删除',
						action: function () {
							YueMi.API.Admin.invoke('cms', 'catagory_del', {
								id: id
							}, function (t, r, q) {
								if (q.__code === 'OK')
								{
									location.reload();
								} else {
									alert(q.__message);
								}
							}, function (t, r, q) {

							});

						}
					},
					cancel: {text: '取消', btnClass: 'btn-blue', action: function () {}}
				}
			});
		}

	}
</script>

{:include file="_g/footer.tpl":}