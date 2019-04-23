{:include file="_g/header.tpl" Title="库存/新增品类":}
<br />
<h1>添加供应商</h1>
<br />
<form name="form1" action="/index.php?call=depot.designer_create" method="post">
	<ul class="Form">
		<li>
			<label>品牌名称：</label>
			<select  name="brand_id" id="brand_id">
				<option value="0">--请选择品牌--</option>

				{:foreach from=$res value=res:}
				<option value ="{:$res.id:}" >{:$res.name:}</option>
				{:/foreach:}
			</select>
		</li>
		<li>
			<label>设计师姓名：</label>
			<input type="text" id="name" name="name"  />
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
			alert('请输入设计师名称');
			return;
		}
		if($('#brand_id').val() == 0)
		{
			alert("请选择品牌");
			return;
		}
		document.form1.submit();
	}
</script>

