{:include file="_g/header.tpl" Title="总监":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		创建总监
	</caption>
	<tr>
		<td align="right">订单号：</td>
		<td align="left"><input type="text" class="input-text" id="order_id" value="{:$OrderId:}" name="order_id" readonly="true" /></td>
		<td align="right">手机号：</td>
		<td align="left"><input type="text" class="input-mobile" name="mobile" id="mobile" /></td>
	</tr>
	<tr>
		<td align="right">姓名：</td>
		<td align="left"><input type="text" class="input-text" name="cert_name" id="cert_name" /></td>
		<td align="right">身份证：</td>
		<td align="left"><input type="text" class="input-text" name="cert_pin" maxlength="18" id="cert_pin" style="text-transform:uppercase;ime-mode:disabled;" /></td>
	</tr>
	<tr>
		<td align="right">送卡：</td>
		<td align="left" colspan="3">
			<input type="checkbox" class="Toggle" id="give_card" name="give_card" checked="checked" value="1" />
			<label style="color:red;" for="give_card">重要：选择此开关，将赠送10张VIP激活卡。</label>
		</td>
	</tr>
	<tr>
		<td align="right">地区：</td>
		<td align="left">
			<input type="text" class="input-region" id="region" name="region"/>
			<script>
				$('#region').createRegionSelector({
					level: 'country'
				});
			</script>
		</td>
		<!--<td align="right">从属经理：</td>
		<td align="left">
			<select id="subord_id" name="subord_id">
				<option value="0">请选择</option>
				<option value="1">魔礼青</option>
				<option value="2">魔礼海</option>
				<option value="3">魔礼红</option>
				<option value="4">魔礼寿</option>
			</select>
		</td>-->
	</tr>
	<tr>
		<td align="right">地址：</td>
		<td align="left" colspan="3">
			<input type="text" class="input-text" id="address" size="60" maxlength="60" name="address"/>
		</td>
	</tr>
	<tr>
		<td align="right">银行：</td>
		<td align="left">
			<select id="bank" name="bank">
				{:foreach from=$BankList key=id item=name:}
					<option value="{:$id:}">{:$name:}</option>
				{:/foreach:}
			</select>
		</td>
		<td align="right">卡号：</td>
		<td align="left"><input type="text" class="input-text" name="card" maxlength="32" size="32" id="card" style="text-transform:uppercase;ime-mode:disabled;"  /></td>
	</tr>
	<tr>
		<td colspan="4" align="center">
			<input type="button" value="提交" onclick="javascript:_do_exec();" />
		</td>
	</tr>
</table>
<script>
	function _do_exec() {
		YueMi.API.Admin.invoke('cheif', 'helicopter', {
			__access_token: '{:$User->token:}',
			order_id : $('#order_id').val().trim(),
			mobile : $('#mobile').val().trim(),
			name : $('#cert_name').val().trim(),
			pin : $('#cert_pin').val().trim(),
			region: $('#region').val().trim(),
			address: $('#address').val().trim(),
			bank_id: $('#bank').val().trim(),
			card: $('#card').val().trim(),
			/*subord_id: $('#subord_id').val().trim(),*/
			give_card: $('#give_card').is(':checked') ? 1 : 0
		}, function (t, r, q) {
			location.href = '/index.php?call=cheif.index';
		}, function (t, r, q) {
			alert(q.__message);
		});
	}
</script>
{:include file="_g/footer.tpl":}
