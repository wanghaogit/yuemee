{:include file="_g/header.tpl" Title="库存/新增品类":}
<br />
<h1>添加供应商</h1>
<br />
<form name="form1" action="/index.php?call=depot.supplier_chapass" method="post">
	<ul class="Form">
		<li>
			供应商ID：{:$res.id:}
		</li>
		<li>
			供应商：{:$res.name:}
		</li>
		<li>
			密码：<input type="text" name="pass">
			<input type="hidden" name="id" value="{:$res.id:}">
		</li>
		<li>
			<input type="submit" value="确认修改" />
		</li>
	</ul>
</form>
{:include file="_g/footer.tpl":}
<script>

</script>

