{:include file="_g/header.tpl" Title="系统/权限":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
    <caption>
        角色管理
        <a class="button button-blue" href="/index.php?call=system.release_new" style="float: left;">
            <i class="fas fa-plus"></i>新建角色
        </a>
    </caption>
    <tr>
        <th>角色ID</th>
        <th>上级角色ID</th>
        <th>角色名称</th>
        <th>操作</th>
    </tr>
    {:foreach from=$Result->Data value=R:}
    <tr>
        <td>{:$R.id:}</td>
        <td>{:$R.parent_id:}</td>
        <td>{:$R.name:}</td>
        <td>
            <a href="javascript:void(0);" onclick="javascript:drop_press('{:$R.id:}')">删除</a>
        </td>
    </tr>
    {:/foreach:}
    <tr class="paging">
        <td colspan="9">
            {:include file="_g/pager.tpl" Result=$Result:}
        </td>
    </tr>
</table>
<script>
	function drop_press(id){
		YueMi.API.Admin.invoke('rbac', 'delete_rbac', {
							__access_token: '{:$User->token:}',
							id : id
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
