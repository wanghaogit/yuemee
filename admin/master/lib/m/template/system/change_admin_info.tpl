{:include file="_g/header.tpl" Title="银行卡":}
<form action="/index.php?call=system.change_admin_info" method="post" name="form1">
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>管理员修改</caption>
	<tr>
		<td>
			管理员名称：
		</td>
		<td>
			<input type="text" value="{:$list.name:}" readonly="true" />
		</td>
	</tr>
	<tr>
		<td>
			所属分类：
		</td>
		<td>
			<select name="role1" class="role">
				
				{:foreach from=$role value=v:}
				<option value="{:$v.id:}" {:if $list['role_id'] == $v.id:}selected="selected"{:/if:}>{:$v.name:}</option>
				{:/foreach:}
			</select>
			<input type="hidden" name="role" id="rolea" value="1" />
		</td>
	</tr>
	<tr>
		<td>
			密码：
		</td>
		<td>
			<button style="width:80%;height:100%;" id="usenew">使用新密码</button>
			<input type="hidden" value="" name="pass" id="hidpass"/>
		</td>
	</tr>
	
	<tr>
		<td colspan="2">
			<input type="button" onclick="javascript:subit();" value="修改"/>
		</td>
	</tr>
</table>
			<input type="hidden" name="uid" value="{:$list['user_id']:}" />
</form>
<script>
	function subit(){
		document.form1.submit();
	}
	$(document).on('change','.role',function(){
		var id = $(this).val();
		$(this).nextAll('.role').remove();
		$('#rolea').val(id);
		YueMi.API.Admin.invoke('user', 'get_role', {
			__access_token: '{:$User->token:}',
			id: id
		},
				function (t, r, q) {
					if (q.__code === 'OK')
					{
						if (q.__arr.length > 0) {
							var str = '<select name="role" class="role"><option value="0">--请选择--</option>';
							for(var i = 0; i < q.__arr.length; i++){
								str += '<option value="'+q.__arr[i]['id']+'">'+q.__arr[i]['name']+'</option>';
							}
							str += '</select>';
							$('.role:last').after(str);
							$('#rolea').val($(this).val());
						}

					} else {

					}
				}, function (t, r, q) {

		});
	});
	
	
	$('#usenew').click(function(){
		$(this).remove();
		$('#hidpass').before('<input type="password" name="pass"/>');
		$('#hidpass').remove();
	});
	
</script>
{:include file="_g/footer.tpl":}