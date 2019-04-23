{:include file="_g/header.tpl" Title="库存/新增品类":}
<br />
<h1>添加供应商</h1>
<br />
<form name="form1" action="/index.php?call=depot.supplier_change" method="post">
	<ul class="Form">
		<li>
		<input type="hidden" name="id" value="{:$res.id:}">
		企业名称：<input type="text" name="name" style="width:50%;" value="{:$res.name:}">
		</li>
		<br/>
		<li>
			公司名称：<input type="text" name="corp_name" style="width:50%;" value="{:$res.corp_name:}">
		</li>
		<br/>
		<li>
		执照号码：<input type="text" name="corp_serial" style="width:50%;" value="{:$res.corp_serial:}">
		
		</li>
		<br/>
		<li>
			公司法人：<input type="text" name="corp_law" value="{:$res.corp_law:}">
		</li>
		<br/>
		<li>
		英文名：<input type="text" name="alias" value="{:$res.alias:}">
		
		</li>
		<br/>
		<li>
		泵入接口：<input type="checkbox" class="Toggle" id="pi_enable" name="pi_enable" value="" {:if $res.pi_enable == 1:}checkbox="checkbox"{:else:}{:/if:}/>
		同步接口：<input type="checkbox" class="Toggle" id="" name="po_enable" value="" {:if $res.po_enable == 1:}checkbox="checkbox"{:else:}{:/if:}/>
		</li>
		<li><input type="submit" value="修改"></li>
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

