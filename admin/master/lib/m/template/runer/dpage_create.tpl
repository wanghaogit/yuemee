{:include file="_g/header.tpl" Title="运营/APP":}
<script type="text/javascript" src="/scripts/editor.js"></script>
<a href="/index.php?call=runer.dpage&parent_id={:$parent_id:}">
	返回上级
</a>
<form name="form1" action="/index.php?call=runer.dpage_create&parent_id={:$parent_id:}" method="post">
<ul class="Form">
	<li>
		<label>配置名称：</label>
		<input type="text" id="name" name="name"  style="width:200px;"/>
	</li>
	<li>
		<label>页面代号：</label>
		<input type="text" id="alias" name="alias"  style="width:200px;"/>
	</li>
	<li>
		<label>模块代码：</label>
	</li>
	<li>
		<textarea id="template" name="template" cols="120" rows="20">{:$Result.template:}</textarea>
	</li>
	<li>
		<input type="button" value="保存" onclick="javascript:check1();"  style="width: 100px;margin-top: 20px;"/>
	</li>
</ul>
</form>
<script>
function check1()
{
	if($('#name').val() == '')
	{
		alert('请输入品类名称');
		return;
	}
	document.form1.submit();
}
</script>
{:include file="_g/footer.tpl":}
