{:include file="_g/header.tpl" Title="库存/供应商":}
<table cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		供应商管理
		<a class="button button-blue" style="float:left;" href="/index.php?call=depot.supplier_create"> <i class="fas fa-plus"></i> 新增供应商 </a>
	</caption>
	<tr>
		<td>查询</td>
		<td colspan="23">
			<form method="GET" action="/index.php">
				<input type="hidden" name="call" value="{:#Z_HANDLER:}.{:#Z_ACTION:}" />
				<input type="hidden" name="p" value="{:$_PARAMS.p:}" />
				手机号码：<input type="text" class="input-mobile" name="m" value="{:$_PARAMS.m:}" />
				微信名称：<input type="text"  name="n" value="{:$_PARAMS.n:}" />
				<input type="submit" value="查询" />
			</form>
		</td>
	</tr>
	<tr>
		<th rowspan="2">编号</th>
		<th rowspan="2">名称</th>
		<th colspan="4">会员</th>
		<th rowspan="2">状态</th>
		<th colspan="2">入站接口</th>
		<th colspan="2">出站接口</th>
		<th rowspan="2">入驻日期</th>
		<th rowspan="2">操作</th>
	</tr>
	<tr>
		<th>ID</th>
		<th>手机</th>
		<th>微信</th>
		<th>昵称</th>
		<th>英文名</th>
		<th>管理</th>
		<th>代码</th>
		<th>管理</th>
	</tr>
	{:foreach from=$Result->Data item=S:}
	<tr>
		<td>{:$S.id:}</td>
		<td>{:$S.name:}</td>
		{:if $S.user_id > 0:}
		<td>{:$S.user_id:}</td>
		<td>{:$S.user_mobile | string.key_highlight $_PARAMS.m:}&nbsp;<i class="fas fa-edit" onclick="do_revip({:$S.id:},{:$S.user_mobile:})"></i></td>
		<td>{:$S.wname | string.key_highlight $_PARAMS.n:}</td>
		<td>{:$S.user_name:}</td>
		{:else:}
		<td colspan="4" align="center">未绑定</td>
		{:/if:}
		<td align="center">
			{:$S.status | boolean.iconic:}
		</td>
		<td align="center">{:$S.alias:}</td>
		<td>{:if $S.pi_enable:}
			<a href="/index.php?call=depot.ext_child_shop&clid={:$S.id:}">子店铺</a> |
			<a href="/index.php?call=depot.extspu&sid={:$S.id:}">外部SPU</a> |
			<a href="/index.php?call=depot.extsku&sid={:$S.id:}">外部SKU</a>
			{:/if:}</td>
		<td>

		</td>
		<td>
			<a href="javascript:void(0);"></a>
		</td>
		<td>{:$S.create_time | number.datetime:}</td>
		<td>
			<a href="/index.php?call=depot.supplier_change&id={:$S.id:}">修改信息</a> |

			<a href="/index.php?call=mall.spu&sid={:$S.id:}">SPU</a> |
			<a href="/index.php?call=mall.sku&sid={:$S.id:}">SKU</a> |


		</td>
	</tr>
	{:/foreach:}
	<tr>
		<td colspan="14">{:include file="_g/pager.tpl" Result=$Result:}</td>
	</tr>
</table>
<script type="text/javascript">
	function do_revip(id, mobile) {
		$.confirm({
			boxWidth: '400px',
			escapeKey: 'cancel',
			icon: 'fas fa-edit',
			title: '修改VIP',
			content: '手机号：<input type="text" class="input-text" id="dlg_input_mobile" value="' + mobile + '" maxlength="32" size="40" />',
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '修改',
					action: function () {
						YueMi.API.Admin.invoke('depot', 'user_revip', {
							__access_token: '{:$User->token:}',
							id: id,
							mobile: $('#dlg_input_mobile').val().trim()
						}, function (t, r, q) {
							alert(q.__message);
							if (q.__message == '修改成功') {
								location.reload();
							}
						}, function (t, r, q) {
							alert(q.__message);
						});
					}
				},
				cancel: {text: '取消', btnClass: 'btn-blue', action: function () {}}
			}
		});
	}
	function _do_disable(id) {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '300px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fa fa-shield',
			title: '暂停合作',
			content: '确定暂停吗？',
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '确定',
					action: function () {
						YueMi.API.Admin.invoke('depot', 'break_off', {
							status: 0,
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
	function _do_enable(id) {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '300px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fa fa-shield',
			title: '继续合作',
			content: '继续合作吗？',
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '继续',
					action: function () {
						YueMi.API.Admin.invoke('depot', 'turn_on', {
							status: 1,
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
