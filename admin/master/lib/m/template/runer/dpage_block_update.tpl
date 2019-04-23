{:include file="_g/header.tpl" Title="运营/APP":}
<script type="text/javascript" src="/scripts/editor.js"></script>
<form name="form1" action="/index.php?call=runer.dpage_block_update&id={:$id:}&page_id={:$page_id:}" method="post">
<ul class="Form">
	<li>
		<label>模块名称：</label>
		<input type="text" id="name" name="name"  style="width:600px;" value="{:$Result.name:}"/>
	</li>
	<li>
		<label>模块代号：</label>
		<input type="text" id="alias" name="alias"  style="width:600px;" value="{:$Result.alias:}"/>
	</li>
	<li>
		<label>组件数据格式：</label>
		<select name="source_type" style="width:100px;background-color: #fff;height:26px">
			<option value="0" {:if $Result.source_type == 0:}selected="selected"{:/if:}>自定义</option>
			<option value="1" {:if $Result.source_type == 1:}selected="selected"{:/if:}>单品</option>
			<option value="2" {:if $Result.source_type == 2:}selected="selected"{:/if:}>多品</option>
		</select>
		<label>尺寸模式：</label>
		<select name="sizer" style="width:100px;background-color: #fff;height:26px">
			<option value="0" {:if $Result.sizer == 0:}selected="selected"{:/if:}>自适应</option>
			<option value="1" {:if $Result.sizer == 1:}selected="selected"{:/if:}>指定像素</option>
			<option value="2" {:if $Result.sizer == 2:}selected="selected"{:/if:}>百分比</option>
		</select>
	</li>
	<li>
		<label>模块宽度：</label>
		<input type="text" name="width" style="width:153px" value="{:$Result.width:}"/>
		<label>模块高度：</label>
		<input type="text" name="height" style="width:153px" value="{:$Result.height:}"/>
		<label>数据容量：</label>
		<input type="text" name="capacity" style="width:153px" value="{:$Result.capacity:}"/>
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
		alert('请输入品类名称');
		return;
	}
	document.form1.submit();
}
</script>
{:include file="_g/footer.tpl":}
