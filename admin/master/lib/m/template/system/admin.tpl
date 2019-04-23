{:include file="_g/header.tpl" Title="用户":}
<link rel="stylesheet" type="text/css" href="/styles/user/usershow.css" />
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>管理员列表</caption>
	<tr>
		<td colspan="7"><a href="/index.php?call=system.add_admin&msg=0"><button>新增管理员</button></a></td>
	</tr>
	<tr>
		<th>ID</th>
		<th>前台用户</th>
		<th>真实姓名</th>
		<th>姓名</th>
		<th>管理角色</th>
		<th>状态</th>
		<th>操作</th>
	</tr>
	{:foreach from=$res2->Data value=v:}
		<tr>
			<td>{:$v.id:}</td>
			<td>{:$v.mobile:}</td>
			<td>{:$v.card_name:}</td>
			<td>{:$v.name:}</td>
			<td>
				{:$v.role_id | array.find $Role,'id','name','':}
			</td>
			<td>{:$v.status| boolean.iconic:}</td>
			<td>
				{:if $v.id == 1:}

				{:else:}
					{:if $v.status == 1:}
						<a href="" onclick="javascript:_do_disable({:$v.id:});">禁用</a> |
					{:else:}
						<a href="" onclick="javascript:_do_enable({:$v.id:});">启用</a> |
					{:/if:}
					<a href="/index.php?call=system.change_admin_info&uid={:$v.user_id:}">资料</a>
				{:/if:}
			</td>
		</tr>
	{:/foreach:}
</table>
<script>
	function _do_disable(id) {
		YueMi.API.Admin.invoke('rbac', 'admin_disable', {
			__access_token: '{:$User->token:}',
			id: id,
		}, function (t, q, r) {
			location.reload();
		}, function (t, q, r) {

		});
	}

	function _do_enable(id) {
		YueMi.API.Admin.invoke('rbac', 'admin_enable', {
			__access_token: '{:$User->token:}',
			id: id,
		}, function (t, q, r) {
			location.reload();
		}, function (t, q, r) {

		});
	}
</script>
{:include file="_g/footer.tpl":}
