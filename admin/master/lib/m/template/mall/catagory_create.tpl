{:include file="_g/header.tpl" Title="库存/新增品类":}
<br />
{:if $pid == '0':}
<h1>添加顶级品类</h1>
{:else:}
<h1>添加“{:$pname:}”的子品类</h1>
{:/if:}
<br />
<form name="form1" action="/index.php?call=mall.catagory_create" method="post">
<ul class="Form">
	<li>
		<label>品类名称：</label>
		<input type="text" id="name" name="name"  />
	</li>
	<li>
		<label>管理员：</label>
		<select name="manager_id"/>
		<option value="0">-- 请选择 --</option>
		{:foreach from=$userlist value=v:}
		<option value="{:$v.id:}">{:$v.name:}</option>
		{:/foreach:}
		</select>
	</li>
	<li>
		<label>是否供应商专区：</label>
		是：<input type="radio" name="is_private" value="1" onclick="javascript:$('#supplier_id').removeAttr('disabled');"/>&nbsp;否：<input type="radio" name="is_private" value="0" checked="checked"  onclick="javascript:$('#supplier_id').val('0');$('#supplier_id').attr('disabled','disabled');"/>
	</li>
	<li>
		<label>供应商：</label>
		<select id="supplier_id" name="supplier_id" />
		<option value="0">-- 请选择 --</option>
		{:foreach from=$supplier_id value=v:}
		<option value="{:$v.id:}">{:$v.name:}</option>
		{:/foreach:}
		</select>
	</li>
	<li>
		<label>是否隐藏类目：</label>
		是：<input type="radio" name="is_hidden" value="1"/>&nbsp;否：<input type="radio" name="is_hidden" value="0" checked="checked"/>
	</li>
	<li>
		<label>是否内部专区（VIP可见）：</label>
		是：<input type="radio" name="is_internal" value="1"/>&nbsp;否：<input type="radio" name="is_internal" value="0" checked="checked"/>
	</li>
	<li>
		<label>排序：</label>
		<input type="text" id="p_order" name="p_order" value="{:$p_order:}" />
	</li>
	<li>
		<label>毛利死线：</label>
		<input type="text" id="gratio_dead" name="gratio_dead" placeholder="0.00" />%
	</li>
	<li>
		<label>毛利报警：</label>
		<input type="text" id="gratio_warn" name="gratio_warn" placeholder="0.00" />%
	</li>
	<li>
		<label>平台佣金比例：</label>
		<input type="text" id="rratio_system" name="rratio_system" placeholder="0.00" />%
	</li>
	<li>
		<input type="button" value="保存" onclick="javascript:check1();" />
	</li>
</ul>
	<input type="hidden" name="parent_id" value="{:$pid:}" />
</form>
{:include file="_g/footer.tpl":}
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
	
