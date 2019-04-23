{:include file="_g/header.tpl" Title="银行卡":}
<form action="/index.php?call=system.subadmin" method="post" name="form1">
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>添加管理员</caption>
	<tr>
		<td colspan="2">
			{: if $_PARAMS.msg == 1 :}<span style="color:red;">请检查手机号</span>{:elseif $_PARAMS.msg == 2:}<span style="color:red;">重复添加</span>{:/if:}
		</td>
	</tr>
	<tr>
		<td>
			电话：
		</td>
		<td>
			<input type="text" value="" name="mobile" id="mobile" />
		</td>
	</tr>
	
	<tr>
		<td>
			管理员类型：
		</td>
		<td>
			<select name="role1" class="role">
				
				{:foreach from=$role value=v:}
				<option value="{:$v.id:}">{:$v.name:}</option>
				{:/foreach:}
			</select>
			<input type="hidden" name="role" id="rolea" />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			权限分配：
		</td>
	</tr>
	<tr>
		<td colspan="2">

		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="button" onclick="javascript:subit();" value="确认添加" />
		</td>
	</tr>
</table>
</form>
<script>
	function subit(){
		var name = $('#name').val();
		var mobile = $('#mobile').val();
		var pass = $('#pass').val();
		if(name == '' || mobile == '' || pass == ''){
			alert('输入不能为空！');
			exit;
		}
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
							$('#rolea').val($('.role:last').val());
						}

					} else {

					}
				}, function (t, r, q) {

		});
	});
	
	$(document).change(function(){
		$('#rolea').val($('.role:last').val());
	});
	
	
</script>
{:include file="_g/footer.tpl":}