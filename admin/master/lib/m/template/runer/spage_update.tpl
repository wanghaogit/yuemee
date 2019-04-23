{:include file="_g/header.tpl" Title="运营/APP":}
<a href="/index.php?call=runer.spage&parent_id={:$Result.parent_id:}">
	返回上级
</a>
<form name="form1" action="/index.php?call=runer.spage_update&id={:$id:}" method="post">
<ul class="Form">
	<li>
		<label>配置名称：</label>
		<input type="text" id="name" name="name"  style="width:200px;" value="{:$Result.name:}"/>
	</li>
	<li>
		<label>页面代号：</label>
		<input type="text" id="alias" name="alias"  style="width:200px;" value="{:$Result.alias:}"/>
	</li>
	<li>
		<input type="button" value="保存" onclick="javascript:check1();"  style="width: 100px;margin-top: 20px;"/>
	</li>
</ul>
	<input type="hidden" name="parent_id" value="{:$pid:}" />
</form>
<script>
function check1()
{
	if($('#name').val() == '')
	{
		alert('配置名称不可为空');
		return;
	}
	if($('#name').val() == '')
	{
		alert('页面代号不可为空');
		return;
	}
	document.form1.submit();
}
</script>
{:include file="_g/footer.tpl":}
