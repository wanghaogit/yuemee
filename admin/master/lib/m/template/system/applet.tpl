{:include file="_g/header.tpl" Title="系统/银行":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		接口权限
	</caption>
	<tr>
		<th>ID</th>
		<th>用户</th>
		<th>类型</th>
		<th>名称</th>
		<th>token</th>
		<th>调用密钥</th>
		<th>回调地址</th>
		<th>状态</th>
		<th>创建时间</th>
		<th>操作</th>
	</tr>
	{:foreach from=$res->Data item=v:}
	<tr>
		<td align="center">{:$v.id:}</td>
		<td align="center">{:$v.uname:}</td>
		<td align="center">{:if $v.type == 0:}系统{:elseif $v.type == 1 :}VIP{:elseif $v.type == 2 :}总监{:elseif $v.type == 3 :}经理{:elseif $v.type == 4 :}供应商{:else:}{:/if:}</td>
		<td align="center">{:$v.name:}</td>
		<td align="center">{:$v.token:}</td>
		<td align="center">{:$v.secret:}</td>
		<td align="center">{:$v.callback:}</td>
		<td align="center">{:if $v.status == 0:}未提交{:elseif $v.status == 1:}待审核{:elseif $v.status == 2:}已审核{:elseif $v.status == 3:}已关闭{:else:}{:/if:}</td>
		<td align="center">{:$v.create_time:}</td>
		<td align="center">
			{:if $v.id == 1:}{:else:}
			{:if $v.status == 3:}<a onclick="changes({:$v.id:}, 0)">开启</a>{:/if:}
			{:if $v.status == 1:}<a onclick="changes({:$v.id:}, 2)">审核</a>{:/if:}
			{:if $v.status < 3:}<a onclick="changes({:$v.id:}, 3)">关闭</a>{:/if:}
			<a onclick="delete2({:$v.id:})" style="color:red;">删除</a>
			{:/if:}
		</td>
	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="10">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>
</table>
<script type="text/javascript">
	function delete2(id) {
		YueMi.API.Admin.invoke('rbac', 'delete_applet', {
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
	function changes(id, val) {
		YueMi.API.Admin.invoke('rbac', 'change_applets', {
			__access_token: '{:$User->token:}',
			id: id,
			val: val
		}, function (t, q, r) {
			if (r.__code == 'OK') {
				location.reload();
			}
		}, function (t, q, r) {
			alert('更改失败');
		});
	}

</script>			
{:include file="_g/footer.tpl":}
