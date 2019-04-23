{:include file="_g/header.tpl" Title="用户":}
<form action="/index.php?call=user.address_doedit" method="post">

	<input type="hidden" name = "uid" id="id" value="{:$res.user_id:}" readonly="readonly"/>
	<input type="hidden" name = "id" id="id" value="{:$res.id:}" readonly="readonly"/>
	<table border="0" cellspacing="0" cellpadding="0" class="Grid">
		<tr>
			<td>联系人：</td>
			<td><input type="text" name = "person" id="iperson" value="{:$res.contacts:}"/></td>
			<td>联系电话：</td>
			<td><input type="text" name = "mobile" id="imobile" value="{:$res.mobile:}"/></td>
		</tr>
		
		<tr>
			<td>状态：</td>
			<td colspan="3">
				<select name="status" id="istatus">
					<option value="1" {:if $res.status == 1 :}selected="selected"{:else:}{:/if:}>可用</option>
					<option value="0" {:if $res.status == 0 :}selected="selected"{:else:}{:/if:}>删除</option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td>地区：</td>
			<td colspan="3"><input type="text" class="input-region" id="region" name="region_id" value="{:$res.region_id:}"/>
				<script>
					$('#region').createRegionSelector({
						level: 'country'
					});
				</script></td>
		</tr>
		<tr>
			<td>详细地址</td>
			<td colspan="3"><textarea name="addressinfo" id="addressinfo" style="width:400px;height:150px;">{:$res.address:}</textarea></td>
		</tr>
		<tr>
			<td colspan="4">
				<input type="submit" value="修改">
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">
	var API_ADMIN = new Invoker({
		udid: '000000000000000000000000',
		url: 'http://z.ym.cn/api.php',
		applet_token: 'b31ed652c66e11b41b6f7378',
		access_token: function () {
			var m = /\buser\_token\=([a-z0-9]+)\b/i.exec(document.cookie);
			if (m && m.length > 0) {
				return m[1];
			}
			return '';
		}
	});
	$(document).on('change', '.cityS', function () {
		var t = $(this).val();
		API_ADMIN.invoke('user', 'user_cityInfo', {
			tt: t
		}, function (t, r, q) {
			if (q.__code === 'OK')
			{
				console.log(q.__arr);
				var str = '<select name="" class="cityC" name="region_id">';
				$.each(q.__arr, function (index, obj) {
					str += '<option value=' + obj.id + '>' + obj.city + '</option>';
				});
				str += '</select>';
				$('.cityS').nextAll('select').remove();
				$('.cityS').after(str);
			} else {
				alert(2);
			}
		}, function (t, r, q) {
			alert(3);
		});
	});
	$(document).on('change', '.cityC', function () {
		var t = $(this).val();
		API_ADMIN.invoke('user', 'user_cityInfo2', {
			tt: t
		}, function (t, r, q) {
			if (q.__code === 'OK')
			{
				console.log(q.__arr);
				var str = '<select name="region_id" class="cityT">';
				$.each(q.__arr, function (index, obj) {
					str += '<option value=' + obj.id + '>' + obj.country + '</option>';
				});
				str += '</select>';
				$('.cityC').nextAll('select').remove();
				$('.cityC').after(str);
			} else {
				alert(2);
			}
		}, function (t, r, q) {
			alert(3);
		});
	});
</script>
{:include file="_g/footer.tpl":}
