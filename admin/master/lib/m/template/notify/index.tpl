{:include file="_g/header.tpl" Title="通知":}
<div>
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

<table border="0" cellspacing="0" cellpadding="0" class="Grid" id="table">
    <caption>
        {:$status | array.enum ['草稿','待审','已审','关闭','','','','','','全部'] :}
		{:$scope | array.enum ['全体','用户','VIP','总监','经理','供应商','员工','管理员'] :}
		全局公告管理
        <a href="/index.php?call=notify.notice_create" class="button button-blue" style="float: left;">
            <i class="fas fa-plus"></i>发公告
        </a>
    </caption>
    <tr>
        <th rowspan="2">ID</th>
        <th colspan="2">范围</th>
        <th rowspan="2">标题</th>
        <th rowspan="2">状态</th>
        <th rowspan="2">时间窗口</th>
        <th rowspan="2">创作</th>
        <th rowspan="2">审核</th>
        <th rowspan="2">操作</th>
    </tr>
    <tr>
        <th>范围</th>
        <th>区域</th>

    </tr>
    {:foreach from=$Result->Data value=v:}
    <tr>
        <td class="zid">{:$v.id:}</td>
        <td align="center">{:$v.scope | array.enum ['全体','非VIP','VIP','总监','经理','供应商','员工','管理员']:}</td>
        <td align="center" width="80">{:if $v.scope_id:}
            {:$v.scope_id:}
            {:/if:}</td>
        <td width="300"><a href="/index.php?call=notify.notice_edit&id={:$v.id:}" onclick="javascript:_do_preview('{:$v.id:}');">{:$v.title:}</a></td>
        <td align="center">{:$v.status | array.enum ['草稿','待审','发布','关闭']:}</td>
        <td>
            {:$v.open_time:}
        </td>
        <td>
            {:$v.create_time:}
        </td>
        <td>
            {:$v.audit_time:}
        </td>
        <td class="operator">
            {:if $v.status == 0:}
            <a href="/index.php?call=notify.notice_edit&id={:$v.id:}">编辑</a>
            <a href="javascript:void(0);" onclick="javascript:drop_press('{:$v.id:}')">删除</a>
            {:elseif $v.status == 1:}
            <a href="/index.php?call=notify.notice_cancel&id={:$v.id:}">撤回</a>
            <a href="" onclick="javascript:examine('{:$v.id:}')">审核</a>
            {:elseif $v.status == 2:}
            <a href="/index.php?call=notify.update_status&id={:$v.id:}&t=3">关闭</a>
            {:elseif $v.status == 3:}
			<a href="javascript:void(0);" onclick="javascript:dakai('{:$v.id:}')">再发</a>
            {:/if:}
        </td>
    </tr>
    {:/foreach:}
    <tr class="paging">
        <td colspan="9">
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
			title: '删除公告',
			content: '删除吗？',
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '删除',
					action: function () {
						YueMi.API.Admin.invoke('notify', 'del', {
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

	function examine(id) {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '300px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fa fa-shield',
			title: '审核公告',
			content: '通过吗？',
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '通过',
					action: function () {
						YueMi.API.Admin.invoke('notify', 'examine', {
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

	function query_notice() {
		var scope = $('#scope').val();
		var status = $('#status').val();
		window.location.href = "/index.php?call=notify.index&status=" + status + "&scope=" + scope;
	}
	function status(id) {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '300px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fa fa-shield',
			title: '关闭公告',
			content: '关闭吗？',
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '关闭',
					action: function () {
						YueMi.API.Admin.invoke('notify', 'status', {
							status: 3,
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

	function dakai(id) {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '300px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fa fa-shield',
			title: '打开公告',
			content: '打开吗？',
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '打开',
					action: function () {
						YueMi.API.Admin.invoke('notify', 'open', {
							status: 2,
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
</script>
{:include file="_g/footer.tpl":}
