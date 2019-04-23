<script src="https://cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>
<style>
	li {
		float:left;
		margin:10px;
	}
</style>
<table brrder="1">
	<th>id</th><th>商品名</th><th>操作</th>
		{:foreach from=$res->Data item=c:}
	<tr style='height:30px;'><td>{:$c.id:}</td><td>{:$c.title:}</td><td><input type='checkbox' name="aa" value='{:$c.id:}' /></td></tr>
			{:/foreach:}
	<tr>
		<td colspan="5">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>
</table>
<script>
	
	var stri = '';
	$('input:checkbox[name=aa]').click(function () {
		if (this.checked) {
			parent.hello += $(this).val() + '|';
		}
	});
</script>
