{:include file="_g/header.tpl" Title="用户":}
<style>
	.ull li{
		margin-top:15px;
	}
</style>
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>管理目标列表</caption>
	<tr>
		<td colspan="9"><button onclick="new_target()">新增目标</button></td>
	</tr>
	<tr>
		<th>ID</th>
		<th>名称</th>
		<th>归属</th>
		<th>module</th>
		<th>handler</th>
		<th>action</th>
		<th>MVC参数</th>
		<th>MVC参数值</th>
		<th>操作</th>
	</tr>
	{:foreach from=$res->Data value=v:}
	<tr>
		<td>{:$v.id:}</td>
		<td>{:$v.name:}</td>
		<td>{:$v.pname:}</td>
		<td>{:$v.mvc_module:}</td>
		<td>{:$v.mvc_handler:}</td>
		<td>{:$v.mvc_action:}</td>
		<td>{:$v.mvc_param:}</td>
		<td>{:$v.mvc_value:}</td>
		<td><a onclick="update2({:$v.id:})">修改</a>|<a onclick="delete2({:$v.id:})" style="color:red;">删除</a></td>
	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="10">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>
</table>
<input type="hidden" id="hidpid" value="0"/>
<script>
	function new_target() {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '600px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fas fa-shield',
			title: '新增管理目标',
			content: '正在加载...',
			onContentReady: function () {
				____generate_sku_list(this);
			},
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '添加',
					action: function () {
						var parent = $('#hidpid').val();
						var name = $('#target_name').val();
						var modle = $('#module_name').val();
						var handler = $('#handler_name').val();
						var action = $('#action_name').val();
						var param = $('#param_name').val();
						var value = $('#value_name').val();
						YueMi.API.Admin.invoke('rbac', 'insert_target', {
							__access_token: '{:$User->token:}',
							parent: parent,
							name: name,
							modle: modle,
							handler: handler,
							action: action,
							param: param,
							value: value
						}, function (t, q, r) {
							if (r.__msg == 'OK') {
								location.reload();
							}
						}, function (t, q, r) {
							alert('添加失败');
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
		YueMi.API.Admin.invoke('rbac', 'target_parent', {
			__access_token: '{:$User->token:}'
		}, function (t, q, r) {
			var str = '<select name = "parent" id = "parent_sel" class= "parent_sel"><option value="0">自身父级</option>';
			$.each(r.list, function (k, v) {
				str += '<option value="' + v.id + '">' + v.name + '</option>';
			});
			str += '</select>';
			var html = '<ul class="ull"><li>父级目标：' + str + '目标名称：<input type="text" name="name" id="target_name"/></li>' +
					'<li>MVC模块：<input type="text" name="module" id="module_name"/>	MVC方法：' +
					'<input type="text" name="handler" id="handler_name"/>' +
					'</li><li>MVC动作：<input type="text" name="action" id="action_name"/>' +
					'MVC参数：<input type="text" name="param" id="param_name"/></li>' +
					'<li>MVC参数：<input type="text" name="value" id="value_name"/></li></ul>';
			self.setContent(html);
		}, function (t, q, r) {
			self.setContent("加载失败，请关闭对话框重试一次");
		});


	}

	$(document).on('change', '.parent_sel', function () {
		var val = $(this).val();
		$(this).nextAll('.parent_sel').remove();
		$('#hidpid').val(val);
		if(val == 0){
			return;
		}
		YueMi.API.Admin.invoke('rbac', 'get_target', {
			__access_token: '{:$User->token:}',
			id: val
		}, function (t, q, r) {
			if (r.__arr.length > 0) {
				var str = '<select class="parent_sel" ><option value="0">--请选择--</option>';
				$.each(r.__arr, function (k, v) {
					str += '<option value="' + v['id'] + '">' + v['name'] + '</option>';
				});
				str += '</select>';
				$('.parent_sel:last').after(str);
			}
		}, function (t, q, r) {

		});

	});
	
	function delete2(id){
		YueMi.API.Admin.invoke('rbac', 'delete_target', {
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
	
	function update2(id) {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '600px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fas fa-shield',
			title: '修改管理目标',
			content: '正在加载...',
			onContentReady: function () {
				____generate_sku_list2(this,id);
			},
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '修改',
					action: function () {
						var id = $('#id2').val();
						var parent = $('#parent2').val();
						var name = $('#name2').val();
						var module = $('#module2').val();
						var handler = $('#handler2').val();
						var action = $('#action2').val();
						var param = $('#param2').val();
						var value = $('#value2').val();
						YueMi.API.Admin.invoke('rbac', 'update_target', {
							__access_token: '{:$User->token:}',
							id : id,
							parent: parent,
							name: name,
							module: module,
							handler: handler,
							action: action,
							param: param,
							value: value
						}, function (t, q, r) {
							if (r.__msg == 'OK') {
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
	
	function ____generate_sku_list2(self,id) {
		YueMi.API.Admin.invoke('rbac', 'get_target_info', {
			__access_token: '{:$User->token:}',
			id : id
		}, function (t, q, r) {
			var str = '<input type="hidden" id="id2" value="'+r.__arr['id']+'"/>父ID：<input type="text" id="parent2" value="'+r.__arr['parent_id']+'"/>名称：<input type="text" id="name2" value="'+r.__arr['name']+'"/><br/>module：<input type="text" id="module2" value="'+r.__arr['mvc_module']+'"/>handler：<input type="text" id="handler2" value="'+r.__arr['mvc_handler']+'"/><br/>action：<input type="text" id="action2" value="'+r.__arr['mvc_action']+'"/>param：<input type="text" id="param2" value="'+r.__arr['mvc_param']+'"/><br/>value：<input type="text" id="value2" value="'+r.__arr['mvc_value']+'"/>';
			self.setContent(str);
		}, function (t, q, r) {
			self.setContent("加载失败，请关闭对话框重试一次");
		});


	}

</script>
{:include file="_g/footer.tpl":}
