{:include file="_g/header.tpl" Title="库存/新增品类":}
<br />
<h1>添加供应商</h1>
<br />
<form name="form1" action="/index.php?call=depot.supplier_create" method="post">
	<ul class="Form">
		<li>
			<label>手机号码：</label>
			<input type="text" class="input-mobile" id="user_mobile" name="user_mobile" value="" />

			<label>初始密码：</label>
			<input type="text" class="input-password" id="user_password" name="user_password" value="" />
		</li>
		<li>
			<label>企业名称：</label>
			<input type="text" class="input-text" id="corp_name" name="corp_name" value="" size="60" />
		</li>
		<li>
			<label>企业代号：</label>
			<input type="text" class="input-account" id="corp_alias" name="corp_alias" value="" />
		</li>
		<li>
			<label>公司名称：</label>
			<input type="text" class="input-text"  name="corp_name" value="" size="60" />
			
		</li>
		<li>
			<label>执照号码：</label>
			<input type="text" class="input-account"  name="corp_serial" value="" />
			<label>公司法人：</label>
			<input type="text" class="input-account"  name="corp_law" value="" />
		</li>
		<li>
			<label>泵入接口：</label>
			<input type="checkbox" class="Toggle" id="pi_enable" name="pi_enable" value="" />
		</li>
		<li>
			<label>同步接口：</label>
			<input type="checkbox" class="Toggle" id="pi_enable" name="po_enable" value="" />
		</li>
		<li>
			<input type="submit" value="保存" />
		</li>
	</ul>
	<script>

	</script>
</form>
{:include file="_g/footer.tpl":}

