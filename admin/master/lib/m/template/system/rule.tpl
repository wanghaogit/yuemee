{:include file="_g/header.tpl" Title="用户":}
<link rel="stylesheet" type="text/css" href="/styles/user/usershow.css" />

<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>管理规则列表</caption>
	<tr>
		<td colspan="7"><a href="/index.php?call=system.add_rule"><button>新增规则</button></a></td>
	</tr>
	<tr>
	<th>角色ID</th>
	<th>角色</th>
	<th>权限</th>
	<th>操作</th>
	</tr>
	{:foreach from=$list key = k value=v:}
	<tr>
		<td>{:$k:}</td>
		<td>{:$v.role:}</td>
		<td style="width:1200px;">
			{:foreach from=$v['arr'] value=a:}
				{:$a['name']:},
			{:/foreach:}
		</td>
		<td style="width:80px;">
			<a href="/index.php?call=system.update_ruleList&id={:$k:}">修改</a> | 
			<a onclick ="delete2({:$k:})" style="color:red;">删除</a>
		</td>
	</tr>
	{:/foreach:}
</table>
<script>
	function change(id) {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '600px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fas fa-shield',
			title: '修改管理规则',
			content: '正在加载...',
			onContentReady: function () {
				____generate_sku_list(this);
			},
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '修改',
					action: function () {
						var read = $('input[name="read"]:checked ').val();
						var edit = $('input[name="edit"]:checked ').val();
						var delete2 = $('input[name="delete"]:checked ').val();
						YueMi.API.Admin.invoke('rbac', 'edit_rule', {
							__access_token: '{:$User->token:}',
							id: id,
							read: read,
							edit: edit,
							delete: delete2
						}, function (t, q, r) {
							if (r.__code == 'OK') {
								location.reload();
							}
						}, function (t, q, r) {
							alert('修改失败');
						});
					}
				},
				cancel: {
					text: '取消',
					btnClass: 'btn-blue',
					action: function () {

					}
				}
			}
		});
	}



	function ____generate_sku_list(self) {
		var html = "<ul><li>访问： &nbsp;允许：<input type='radio' name='read' value='1'/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;拒绝：<input type='radio' name='read' value='2'/> &nbsp;&nbsp;&nbsp; 继承：<input type='radio' name='read' value='0'/></li><li>修改： &nbsp;允许：<input type='radio' name='edit' value='1'/> &nbsp;&nbsp;&nbsp; 拒绝：<input type='radio' name='edit' value='2'/> &nbsp;&nbsp;&nbsp; 继承：<input type='radio' name='edit' value='0'/></li><li>删除：&nbsp;&nbsp;允许：<input type='radio' name='delete' value='1'/> &nbsp;&nbsp;&nbsp; 拒绝：<input type='radio' name='delete' value='2'/> &nbsp;&nbsp;&nbsp; 继承：<input type='radio' name='delete' value='0'/></li></ul>";
		self.setContent(html);
	}

	function delete2(id) {
		YueMi.API.Admin.invoke('rbac', 'delete_rule', {
			__access_token: '{:$User->token:}',
			id: id
		}, function (t, q, r) {
			if (r.__code == 'OK') {
				location.reload();
			}
		}, function (t, q, r) {
			alert('删除失败');
		});
	}
</script>
{:include file="_g/footer.tpl":}
