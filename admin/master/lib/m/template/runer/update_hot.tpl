{:include file="_g/header.tpl" Title="运营/组件":}
<style>

</style>
<form action="/index.php?call=runer.update_hot" method="post">
	<table border="0" cellspacing="0" cellpadding="0" class="Grid">
		<caption>
        热搜修改
    </caption>
	<input type="hidden" name="id" value="{:$res.id:}" />
	<tr>
		<td>
	名称：
		</td>
		<td>
	<input type="text" name="title" value="{:$res.title:}" >
		</td>
	</tr>
	<tr>
		<td>颜色：</td><td><input type="text" name="color" value="{:$res.color:}" ></td>
	</tr>
	<tr>
		<td>尺寸：</td><td><input type="text" name="size" value="{:$res.size:}" ></td>
	</tr>
	<tr>
		<td>排序：</td><td><input type="text" name="p_order" value="{:$res.p_order:}" ></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" value="修改" /></td>
	</tr>
	</table>
</form>
<script type="text/javascript">

</script>
{:include file="_g/footer.tpl":}
