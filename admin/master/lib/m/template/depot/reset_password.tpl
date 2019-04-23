{:include file="_g/header.tpl" Title="重置密码":}
<ul>
	<form action="/index.php?call=depot.reset_password" method="post">
		<li>
			<label>原始密码</label>
			<input type="hidden" name="id" value="{:$res.id:}"/>
			<input type="text" name="original_password" style="margin-left: 10px;"/>
		</li><br>
		<li>
			<label>新密码</label>
			<input type="text" name="new_password" style="margin-left: 23px;"/>
		</li><br>
		<li>
			<label>确认新密码</label>
			<input type="text" name="qr_password"/>
		</li>
		<li>
			<input type="submit" value="保存"/>
		</li>
	</form>
</ul>
{:include file="_g/footer.tpl":}