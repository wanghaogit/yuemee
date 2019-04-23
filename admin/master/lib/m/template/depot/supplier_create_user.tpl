{:include file="_g/header.tpl" Title="库存/新增品类":}
<br />
<h1>添加用户</h1>
<br />
<form name="form1" action="/index.php?call=depot.supplier_create_user" method="post">
	<ul class="Form">
		<li>
			<label>用户名：</label>
			<input type="text" name="name">

			<label>密码：</label>
			<input type="password" name="pass">
		</li>
		<li>
			<label>手机号：</label>
			<input type="text" name="mobile">

			<input type="submit" value="提交">
		</li>	
</form>
{:include file="_g/footer.tpl":}
<script>
	function check1()
	{
		if ($('#name').val() == '')
		{
			alert('请输入品牌名称');
			return;
		}
		document.form1.submit();
	}
</script>

