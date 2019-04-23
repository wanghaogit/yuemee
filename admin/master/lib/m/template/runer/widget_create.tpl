{:include file="_g/header.tpl" Title="运营/APP":}
<script type="text/javascript" src="/scripts/editor.js"></script>
<a href="/index.php?call=runer.source">
	返回
</a>
<form name="form1" action="/index.php?call=runer.widget_create&block_id={:$block_id:}" method="post">
	<ul class="Form">
		<li>
			<label>组件名称：</label>
			<input type="text" id="name" name="name"  style="width:275px;"/>
		</li>
		<li>
			<label>组件代号：</label>
			<input type="text" id="alias" name="alias"  style="width:275px;"/>
		</li>
		<li>
			<label>组件数据格式：</label>
			<select name="source_type" style="width:100px;background-color:#fff;height:27px">
				<option value="0">自定义</option>
				<option value="1">单品</option>
				<option value="2">多品</option>
			</select>
			<label>尺寸模式：</label>
			<select name="sizer" style="width:100px;background-color:#fff;height:27px">
				<option value="0">自适应</option>
				<option value="1">指定像素</option>
				<option value="2">百分比</option>
			</select>
		</li>
		<li>
			<label>组件宽度：</label>
			<input type="text" name="width"/>
			<label>组件高度：</label>
			<input type="text" name="height"/>
			<label>数据容量：</label>
			<input type="text" name="capacity"/>
		</li>
		<li>
			<label>组件的UI代码</label>
		</li>
		<li>
			<textarea id="template" name="template" cols="120" rows="20"></textarea>
		</li>
		<li>
			<input type="button" value="保存" onclick="javascript:check1();"  style="width: 100px;margin-top: 20px;"/>
		</li>
	</ul>
</form>
<script>
	function check1() {
		if ($('#name').val() == '') {
			alert('请输入品类名称');
			return;
		}
		document.form1.submit();
	}
</script>
{:include file="_g/footer.tpl":}
