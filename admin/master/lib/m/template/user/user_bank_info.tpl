{:include file="_g/header.tpl" Title="用户":}
<form action="/index.php?call=user.doeidt_userbank" method="post">
	<table border="0" cellspacing="0" cellpadding="0" class="Grid">
		<input type="hidden" name="id" value="{:$res.id:}" />
		<input type="hidden" name="user_id" value="{:$res.user_id:}" />
		<tr><td>
				开户地区:
			</td><td><input type="text" class="input-region" id="region" name="region_id" value="{:$res.region_id:}" /></td>
		<script>
			$('#region').createRegionSelector({
				level: 'country'
			});
		</script>
		</tr>
		<tr>
			<td>
				开户行：
			</td>
			<td>
				<select name="bank_id" id="banksel">
					{:foreach from=$bank value=v:}
					<option value="{:$v.id:}" {: if $res.bank_id == $v.id :}selected="selected"{: /if :}>{:$v.name:}</option>
					{:/foreach:}
				</select>
			</td></tr>
		<tr>
			<td>
		卡号：
			</td>
			<td>
		<input type="text" name="card_no" value="{:$res.card_no:}" style="width:200px;"/>
			</td>
		</tr>
		<tr>
			<td>
		用户状态：
			</td>
			<td>
		<select name="status">
			<option value="0" {: if $res.status == 0 :}selected="selected"{: /if :}>删除</option>
			<option value="1" {: if $res.status == 1 :}selected="selected"{: /if :}>可用</option>
			<option value="2" {: if $res.status == 2 :}selected="selected"{: /if :}>正确</option>
			<option value="3" {: if $res.status == 3 :}selected="selected"{: /if :}>错误</option>
		</select>
			</td>
</tr>
<tr>
	<td colspan="2">
		<input type="submit" value="修改" />
	</td>
</tr>
	</table>
</form>
<script type="text/javascript">

</script>
{:include file="_g/footer.tpl":}
