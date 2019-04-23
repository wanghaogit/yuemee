{:include file="_g/header.tpl" Title="库存/子店铺":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		店铺
	</caption>
	<tr>
		<th>ID</th>
		<th>分类</th>
		<th>名称</th>
		<th>内部分类id</th>
		<th>操作</th>
	</tr>
	{:foreach from=$shop->Data value=v:}
	<tr>
		<td>{:$v.id:}</td>
		<td>{:$v.supplier_id:}</td>
		<td>{:$v.name:}</td>
		<td>{:$v.map_id:}</td>
		
		<td>
			<a href="javascript:void(0);" onclick="javascript:drop_press({:$v.id:});">
				删除
			</a>
		</td>
	</tr>
	{:/foreach:}
	<tr class="pager">
		<td colspan="9">
			{:include file="_g/pager.tpl" Result=$shop:}
		</td>
	</tr>
</table>
<script>

</script>
{:include file="_g/footer.tpl":}