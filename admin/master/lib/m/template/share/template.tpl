{:include file="_g/header.tpl" Title="分享":}
<table class="Grid" cellspacing="0" cellpadding="0">
	<caption>分享模板管理</caption>
	<tr>
		<td colspan="8">
			<button onclick="javascript:_do_create();"><i class="fas fa-plus"></i> 新建模板</button>
		</td>
	</tr>
	<tr>
		<th>ID</th>
		<th>名称</th>
		<th>尺寸</th>
		<th>路径</th>
		<th>多品</th>
		<th>状态</th>
		<th>创建</th>
		<th>操作</th>
	</tr>
	{:foreach from=$Templates value=v:}

		<tr>
			<td>{:$v.id:}</td>
			<td>{:$v.name:}</td>
			<td>{:$v.body_width:}*{:$v.body_height:}</td>
			<td>{:$v.body_path:}</td>
			<td>{:if $v.is_multiple == 0:}  不支持   {:else:} 支持  {:/if:}</td>
			<td>{:if $v.status == 0:}  没启用   {:else:} 已启用  {:/if:}</td>
			<td>{:$v.create_user:}</td>
			<td><a href="/index.php?call=share.update_template&id={:$v.id:}">修改</a></td>
		</tr>
	{:/foreach:}
</table>
<script type="text/javascript">
	function _do_create() {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '300px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fas fa-plus',
			title: '新建分享模板',
			content: '模板名称：<input type="text" class="input-text" maxlength="32" id="dlg_input_name" size="24" /><br />' +
					'支持多品：<input type="checkbox" class="Toggle" id="dlg_input_multi" value="1" /><br />',
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '下一步',
					action: function () {
						var title = document.getElementById("dlg_input_name").value;
						var multi = document.getElementById("dlg_input_multi").value;
						YueMi.API.Admin.invoke('share', 'template_create', {
							title: title,
							multi: multi
						}, function (t, r, q) {
							if (q.__code === 'OK') {
								_do_upload(q.Id, q.Title);
							} else {
								alert(q.__message);
							}
						}, function (t, r, q) {

						});
					}
				}, cancel: {text: '取消', btnClass: 'btn-blue', action: function () {}}
			}
		});
	}

	function _do_upload(id, title) {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '300px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fas fa-plus',
			title: '上传模板图片',
			content:
					'模板名称：<input type="text" class="input-text" maxlength="32" id="dlg_input_name" size="24" value="' + title + '" /><br />' +
					' 模板Id：<input type="text" class="input-number" maxlength="32" id="dlg_input_name" size="24" value="' + id + '" /><br />' +
					' 模板图片：<input type="text" class="input-text" maxlength="32" id="dlg_input_body" size="24" value="" readonly="true" /><br />' +
					'模板图片：<div id="dlg_upload"></div>',

			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '下一步',
					action: function () {
						if($('#dlg_input_body').val().length < 1){
							$('#dlg_input_body').focus().select();
							return false;
						}
						location.reload();
					}}, cancel: {text: '取消', btnClass: 'btn-blue', action: function () {}}
			},
			onContentReady: function(){
				YueMi.Upload.Admin.create('dlg_upload',{
					__width : 100,
					__height : 62,
					schema : 'share',
					template_id : id
				},function(t,r,q){
					$('#dlg_input_body').val(q.Url);
				},function(t,r,q){
					alert(q.__code);
				});
			}
		});
	}
		

</script>


{:include file="_g/footer.tpl":}
