{:include file="_g/header.tpl" Title="银行卡":}
<style>

</style>
{:foreach from=$ChoArr value=v:}
<span class="chose" style="display:none;">{:$v:}</span>
{:/foreach:}
<form action="/index.php?call=system.update_ruleList" method="post" name="form1">
	<table border="0" cellspacing="0" cellpadding="0" class="Grid">
		<caption>新增规则</caption>
		<tr style="height:45px;">
			<td style="font-size:17px;font-weight:bold">角色</td>
			<td style="font-size:17px;font-weight:bold">
				{:$role_name['name']:}
				<input type="hidden" name="hiderole" value="{:$role_name['id']:}" />
			</td>
		</tr>
		<tr>
			<td colspan="2">权限配置</td>
		</tr>
		<tr>
			<td colspan="2" style="font-size:15px;line-height:30px;">
				{:foreach from=$TargetList value=v:}
				{:if $v.parent_id == 0:}<hr>{:/if:}
				{:$v.name:}
				<input type="checkbox" name="a{:$v.id:}" value="{:$v.id:}" class="checkt"/>

				{:/foreach:}
			</td>
		</tr>

		<tr>
			<td colspan="2">
				<input type="submit" value="添加" />
			</td>
		</tr>
	</table>
</form>
<script>
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
							$('#rolea').val($('.role:last').val());
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
	$(function () {
		var arr = new Array();
		$('.chose').each(function () {
			arr.push($(this).html());
		});

		$('.checkt').each(function () {
			if ($.inArray('' + $(this).val(), arr) > -1) {
				$(this).prop("checked", true);
			}
		});
	});
	
</script>
{:include file="_g/footer.tpl":}