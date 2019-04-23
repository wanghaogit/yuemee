{:include file="_g/header.tpl" Title="资讯":}
<!--<div>
	<select style="width:100px;background-color: #fff" id="status">
		<option value="9" {:if $status == 9:}selected="selected"{:/if:}>全部</option>
		<option value="0" {:if $status == 0:}selected="selected"{:/if:}>草稿</option>
		<option value="1" {:if $status == 1:}selected="selected"{:/if:}>待审</option>
		<option value="2" {:if $status == 2:}selected="selected"{:/if:}>审核</option>
		<option value="3" {:if $status == 3:}selected="selected"{:/if:}>关闭</option>
	</select>
	<select style="width:100px;background-color: #fff" id="scope">
		<option value="0" {:if $scope == 0:}selected="selected"{:/if:}>全体</option>
		<option value="1" {:if $scope == 1:}selected="selected"{:/if:}>用户</option>
		<option value="2" {:if $scope == 2:}selected="selected"{:/if:}>VIP</option>
		<option value="3" {:if $scope == 3:}selected="selected"{:/if:}>总监</option>
		<option value="0" {:if $scope == 4:}selected="selected"{:/if:}>经理</option>
		<option value="0" {:if $scope == 5:}selected="selected"{:/if:}>供应商</option>
		<option value="0" {:if $scope == 6:}selected="selected"{:/if:}>员工</option>
		<option value="0" {:if $scope == 7:}selected="selected"{:/if:}>管理员</option>
	</select>
	<input type="button" value="查询" onclick="query_notice()"/>
</div>
-->
<table border="0" cellspacing="0" cellpadding="0" class="Grid" id="table">
    <caption>

		资讯管理
        <a href="/index.php?call=cms.create_cms" class="button button-blue" style="float: left;">
            <i class="fas fa-plus"></i>发资讯
        </a>
    </caption>
    <tr>
        <th>ID</th>
		<th>分类</th>
        <th>标题</th>
        <th>状态</th>
        <th>时间</th>
        <th>操作</th>
    </tr>
	{:foreach from=$content value=v:}
	<tr>
		<td>{:$v.id:}</td>
		<td>{:$v.cname:}</td>
		<td>{:$v.title:}</td>
		<td>{:$v.status | array.enum ['待审','拒绝','删除','批准','排队','正常','下架']:}</td>
		<td>{:$v.create_time:}</td>
		<td>
			<a href="/index.php?call=cms.update_cms&id={:$v.id:}">编辑</a>
            <a href="javascript:void(0);" onclick="javascript:drop_press('{:$v.id:}')">删除</a>
			<a href="/index.php?call=cms.examine&id={:$v.id:}">审核</a>
		</td>
	</tr>
    {:/foreach:}
    <tr class="paging">
        <td colspan="6">
            {:include file="_g/pager.tpl" Result=$Result:}
        </td>
    </tr>

</table>
<script type="text/javascript">
	
	function drop_press(id) {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '300px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fa fa-shield',
			title: '删除资讯',
			content: '删除吗？',
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '删除',
					action: function () {
						YueMi.API.Admin.invoke('cms', 'del', {
							id: id,
							__access_token : '{:$User->token:}'
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

	
	
</script>
{:include file="_g/footer.tpl":}
