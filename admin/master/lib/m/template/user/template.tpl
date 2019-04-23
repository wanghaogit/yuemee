{:include file="_g/header.tpl" Title="系统/邀请模板":}
<table cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		邀请素材模板
		<a href="javascript:void(0);" onclick="javascript:img();"></a>
		<a class="button button-blue" style="float:left;" onclick="javascript:_do_create();"> <i class="fas fa-plus"></i> 新建模板 </a>
	</caption>
	<tr>
		<th>编号</th>
		<th>模板名称</th>
		<th>文件路径</th>
		<th>底图尺寸</th>
		<th>姓名</th>
		<th>代码</th>
		<th>头像</th>
		<th>二维码</th>
		<th >状态</th>
		<th >操作</th>
	</tr>
	{:foreach from=$Grid item=Tpl:}
		<tr>
			<td>{:$Tpl.id:}</td>
			<td>{:$Tpl.name:}</td>
			<td>{:$Tpl.body_path:}</td>
			<td>{:$Tpl.body_width:} × {:$Tpl.body_height:}</td>
			<td align="center">{:$Tpl.name_enable | boolean.iconic:}</td>
			<td align="center">{:$Tpl.code_enable | boolean.iconic:}</td>
			<td align="center">{:$Tpl.avatar_enable | boolean.iconic:}</td>
			<td align="center"><i class="fas fa-check"></i></td>
			<td align="center">{:$Tpl.status | array.enum ['停用','草稿','启用']:}</td>
			<td class="operator">
				<a href="/index.php?call=user.template_edit&id={:$Tpl.id:}">修改</a>
				{:if $Tpl.status == 0 || $Tpl.status == 1:}
					<a href="javascript:void(0);" onclick="javascript:_do_enable({:$Tpl.id:});">启用</a>
				{:else $Tpl.status == 2:}
					<a href="javascript:void(0);" onclick="javascript:_do_disable({:$Tpl.id:});">停用</a>
				{:/if:}
				<a href="javascript:void(0);" onclick="javascript:_do_preview({:$Tpl.id:});">预览</a>
				<a href="javascript:void(0);" onclick="javascript:_do_examine({:$Tpl.id:});">修改背景图</a>
			</td>
		</tr>
	{:/foreach:}
</table>
<script type="text/javascript">
	function img() {
		YueMi.API.Admin.invoke('user', 'img', {
			__access_token: '{:$User->token:}'
			
		}, function (t, q, r) {
			//成功
		}, function (t, q, r) {
			//失败
		});
	}
	function _do_examine(id)
	{
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '300px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fas fa-plus',
			title: '修改模板图片',
			content:
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
						var url = $("#dlg_input_body").val();
						var id = $("#dlg_input_name").val();
						YueMi.API.Open.invoke('user', 'copy', {
							url : url,
							id  : id
						}, function (t, q, r) {
							//成功
						}, function (t, q, r) {
							//失败
						});
						
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

	function _do_disable(id){
		
	}
	function _do_enable(id){
		
	}
	function _do_preview(id){

	}
	
</script>
{:include file="_g/footer.tpl":}
