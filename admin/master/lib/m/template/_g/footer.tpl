</div>
<div style="float:left;clear:both;display: block;height:32px;width:100%;"></div>
<div class="page_footer">
	<style type="text/css">
		ul.__inner_dialog_list { }
		ul.__inner_dialog_list > li {
			line-height: 26px;
		}
	</style>
	<script type="text/javascript">
		function _show_user_info() {
			$.confirm({boxWidth: '300px', escapeKey: 'cancel', icon: 'fas fa-user',
				title: '个人资料',
				content: '<ul class="__inner_dialog_list">' +
						'<li>修改密码</li>' +
						'<li>旧密码：<input type="text" class="input-text" id="dlg_input_oldpwd" value="" maxlength="32" size="32" /></li>' +
						'<li>新密码：<input type="text" class="input-text" id="dlg_input_newpwd" value="" maxlength="32" size="32" /></li>' +
						'</ul>',
				buttons: {
					accept: {
						btnClass: 'btn-red', text: '修改密码', action: function () {
							var op = $('#dlg_input_oldpwd').val().trim();
							var np = $('#dlg_input_newpwd').val().trim();
							if(op.length <= 4){
								$('#dlg_input_oldpwd').focus().select();return false;
							}
							if(np.length <= 4){
								$('#dlg_input_newpwd').focus().select();return false;
							}
							YueMi.API.Admin.invoke('default','passwd',{
								__access_token : '{:$User->token:}',
								id : 0,
								op : op,
								np : np
							},function(t,r,q){
								alert('修改密码成功');
							},function(t,r,q){
								alert('修改密码失败。');
							})
						}
					},
					cancel: {text: '关闭', btnClass: 'btn-blue', action: function () {}}
				}
			});
		}
	</script>
	<a href="javascript:void(0);" onclick="javascript:_show_user_info();" style="float:left;margin-top: 20px;">
		<i class="fas fa-user"></i>
		{:$User->name:}
	</a>
	北京凯熙科技有限公司
	<a href="/index.php?call=default.quit" style="float: right;margin-bottom: 20px;">
		退出后台
		<i class="fas fa-share-square"></i>
	</a>
</div>
</div>
</body>
</html>