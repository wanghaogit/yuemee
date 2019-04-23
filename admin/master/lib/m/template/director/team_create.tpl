{:include file="_g/header.tpl" Title="团队/创建团队":}
<br />
<h1>添加团队</h1>
<br />
<form name="form1" action="/index.php?call=director.team_create" method="post">
<ul class="Form">
	<li>
		<label>总经理：</label>
		<select id="director_id" name="director_id">
			<option value="0">-- 请选择 --</option>
			{:foreach from=$res value=v:}
			<option value="{:$v.id:}">{:$v.name:}</option>
			{:/foreach:}
		</select>
	</li>
	<li>
		<label>团队名称：</label>
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
	if($('#director_id').val() == '0')
	{
		alert('请选择总经理');
		return;
	}
	if($('#name').val() == '')
	{
		alert('请输入团队名称');
		return;
	}
	document.form1.submit();
}
</script>
	
