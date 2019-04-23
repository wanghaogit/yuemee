{:include file="_g/header.tpl" Title="银行卡":}
<style>
	
</style>
<form action="/index.php?call=system.add_rule" method="post" name="form1">
	<table border="0" cellspacing="0" cellpadding="0" class="Grid">
		<caption>新增规则</caption>
		<tr>
			<td>角色</td>
			<td>
				<select name="role1" class="role">
					{:foreach from=$role value=v:}
					<option value="{:$v.id:}">{:$v.name:}</option>
					{:/foreach:}
				</select>
				<input type="hidden" name="role" id="rolea" value="1" />
			</td>
		</tr>
		<tr>
			<td colspan="2">权限配置</td>
		</tr>
		<tr>
			<td colspan="2" style="font-size:15px;line-height:30px;">
				{:foreach from=$TargetList value=v:}
					{:if $v.parent_id == 0:}<br/>{:/if:}
					{:$v.name:}
					<input type="checkbox" name="a{:$v.id:}" value="{:$v.id:}" />
				
				{:/foreach:}
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
				<input type="submit" value="添加" id="sub"/>
			</td>
		</tr>
	</table>
</form>
<script>
	$('#sub').mouseover(function(){
		var role = $('#rolea').val();
		if(role == 0){
			alert('请选择详细角色');
			exit;
		}
	});
	$(document).on('change', '.role', function () {
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
							for (var i = 0; i < q.__arr.length; i++) {
								str += '<option value="' + q.__arr[i]['id'] + '">' + q.__arr[i]['name'] + '</option>';
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

	$(document).change(function () {
		$('#rolea').val($('.role:last').val());
	});

	$(document).on('change', '.target', function () {
		var id = $(this).val();
		$('#targeta').val(id);
		$(this).nextAll('.target').remove();
		if (id == 0) {
			return;
		}
		YueMi.API.Admin.invoke('rbac', 'get_target', {
			__access_token: '{:$User->token:}',
			id: id
		},
				function (t, r, q) {
					if (q.__code === 'OK')
					{
						$(this).next('.target').remove();
						if (q.__arr.length > 0) {
							var str = '<select name="target" class="target"><option value="0">--请选择--</option>';
							for (var i = 0; i < q.__arr.length; i++) {
								str += '<option value="' + q.__arr[i]['id'] + '">' + q.__arr[i]['name'] + '</option>';
							}
							str += '</select>';
							$('.target:last').after(str);
							$('#target').val($('.target:last').val());
						}
					} else {

					}
				}, function (t, r, q) {

		});
	});
</script>
{:include file="_g/footer.tpl":}