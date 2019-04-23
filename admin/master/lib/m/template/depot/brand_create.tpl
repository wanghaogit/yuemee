{:include file="_g/header.tpl" Title="库存/新增品类":}
<br />
<h1>添加供应商</h1>
<br />
<form name="form1" action="/index.php?call=depot.brand_create" method="post">
	<ul class="Form">
		<li>
			<label>供应商ID：</label>
			<select  name="supplier_id">
			{:foreach from=$Result value=su:}
			<option value ="{:$su.id:}" id="supplier_id">{:$su.name:}</option>
			{:/foreach:}
			</select>
		</li>
		<li>
			<label>品牌名称：</label>
			<input type="text" id="name" name="name"  />
		</li>
		<li>
			<label>英文名：</label>
			<input type="text" name="alias" />
		</li>
		<li>
			<input type="button" value="保存" onclick="javascript:check1();" />
		</li>
	</ul>
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

