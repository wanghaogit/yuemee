{:include file="_g/header.tpl" Title="银行卡":}
<form action="/index.php?call=system.release_new" method="post" name="form1">
	<table border="0" cellspacing="0" cellpadding="0" class="Grid">
		<caption>新增角色</caption>
		<tr>
			<td>所属父级：</td>
			<td>
				<select class="sel">
					<option value="0">--自身父级--</option>
					{:foreach from=$res value=v:}
					<option value="{:$v.id:}">{:$v.name:}</option>
					{:/foreach:}
				</select>
				<input type="hidden" name="parent" value="0" id="hidpid"/>
			</td>
		</tr>
		<tr>
			<td>
				名称：
			</td>
			<td>
				<input type="text" name="name" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" value="提交" />
			</td>
		</tr>
	</table>
</form>
<script>
	$(document).on('change','.sel',function(){
		var id = $(this).val();
		$(this).nextAll('.sel').remove();
		$('#hidpid').val(id);
		if(id == 0){
			return;
		}
		
		YueMi.API.Admin.invoke('rbac', 'get_role', {
			__access_token: '{:$User->token:}',
			id: id
		}, function (t, q, r) {
			if (r.__arr.length > 0) {
				var str = '<select class="sel" ><option value="0">--请选择--</option>';
				$.each(r.__arr, function (k, v) {
					str += '<option value="' + v['id'] + '">' + v['name'] + '</option>';
				});
				str += '</select>';
				$('.sel:last').after(str);
			}
		}, function (t, q, r) {

		});
	});
</script>
{:include file="_g/footer.tpl":}