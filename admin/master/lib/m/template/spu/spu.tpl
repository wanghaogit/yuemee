{:include file="_g/header.tpl" Title="库存/SPU":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>

		SPU管理
		<a class="button button-blue" style="float:left;" href="/index.php?call=mall.spu_create" >
			<i class="fas fa-plus"></i>
			录库
		</a>
	</caption>
	<tr>
		<td>查询</td>
		<td colspan="9">
			<form action="/index.php?call=mall.spu" method="post">
				供应商<select id="supplier_serch" name="supplier_serch">
					<option value="0">请选择</option>
					{:foreach from=$supplier value=v:}
					<option value="{:$v.id:}">{:$v.name:}</option>
					{:/foreach:}
				</select>
				品牌：<select id="brand_serch" name="brand_serch">
					<option value="0">请选择</option>
					{:foreach from=$brand value=v:}
					<option value="{:$v.id:}">{:$v.name:}</option>
					{:/foreach:}
				</select>
				状态：<select id="status_serch" name="status_serch">
					<option value="-1">请选择</option>
					<option value="0">下架</option>
					<option value="1">上架</option>
				</select>
				关键字：<input type="text" value="" name="key_serch">
				<input type="submit" value="搜索">
			</form>
		</td>
	</tr>
	<tr>
		<th>ID</th>
		<th>商品标题</th>
		<th>品类</th>
		<th>货号</th>
		<th>预定上架时间</th>
		<th>预定下架时间</th>
		<th>状态</th>
		<th>操作</th>
	</tr>
	{:foreach from=$data->Data value=v:}
	<tr>
		<td>{:$v.id:}</td>
		<td>
			<a href="/index.php?call=depot.extsku&spu_spu={:$v.id:}" title="查看外部SKU列表" style="float:left;"><i class="fas fa-arrow-alt-circle-left"></i></a>
			<a href="/index.php?call=mall.spu_info&spu_id={:$v.id:}">{:$v.title:}</a>
			<a href="/index.php?call=mall.sku&spuid={:$v.id:}" title="查看SKU列表" style="float:right;"><i class="fas fa-arrow-alt-circle-right"></i></a>
		</td>
		<td>{:$v.name_1:}</td>
		<td>{:$v.serial:}</td>
		<td>{:if $v.online_time == 0:}无{:else:} {:$v.online_time:}{:/if:}</td>
		<td>{:if $v.offline_time == 0:}无{:else:}{:$v.offline_time:}{:/if:}</td>
		<td>
			{:if $v.status == 0:}
			下架
			{:elseif $v.status == 1:}
			上架
			{:elseif $v.status == 3:}
			已上架
			{:/if:}
		</td>
		<td>
			<a href="/index.php?call=mall.material&spu_id={:$v.id:}&t=0">商品素材</a>
			<a href="/index.php?call=mall.material&spu_id={:$v.id:}&t=1">内容素材</a>
			<a href="/index.php?call=mall.spu_info&spu_id={:$v.id:}">SPU详情</a>
			<a href="/index.php?call=mall.sku_create&spu_id={:$v.id:}">添加SKU</a>
			<a href="/index.php?call=mall.edit_spu_info&id={:$v.id:}">编辑</a>
		</td>
	</tr>
	{:/foreach:}
	<tr class="pager" style="width: 100%;">
		<td colspan="11">
			{:include file="_g/pager.tpl" Result=$data:}
		</td>
	</tr>
</table>
<script type="text/javascript">

</script>
{:include file="_g/footer.tpl":}
