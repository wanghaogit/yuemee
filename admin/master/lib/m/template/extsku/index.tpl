{:include file="_g/header.tpl" Title="库存/SKU":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<tr>
		<td>
			查询：
		</td>
		<td colspan="12">
			<form action="/index.php" method="get">
				<input type="hidden" name="call" value="{:$_RUNTIME->ticket->handler:}.{:$_RUNTIME->ticket->action:}" />
				供应商<select id="supplier_serch" name="supplier_serch">
					<option value="0">请选择</option>
					{:foreach from=$supplier value=v:}
						<option value="{:$v.id:}" {:if $_PARAMS.supplier_serch == $v.id:}selected="selected"{:/if:}>{:$v.name:}</option>
					{:/foreach:}
				</select>
				<select id="brand_serch" name="brand_serch" style="display:none;">
					<option value="0">请选择</option>
					{:foreach from=$brand value=v:}
						<option value="{:$v.id:}" {:if $_PARAMS.brand_serch == $v.id:}selected="selected"{:/if:}>{:$v.name:}</option>
					{:/foreach:}
				</select>
				状态：<select id="status_serch" name="status_serch">
					<option value="0">请选择</option>
					<option value="2" {:if $_PARAMS.status_serch == 2:}selected="selected"{:/if:}>下架</option>
					<option value="1" {:if $_PARAMS.status_serch == 1:}selected="selected"{:/if:}>上架</option>
				</select>
				编码：<input type="text" value="{:$_PARAMS.bn_serch:}" name="bn_serch">
				关键字：<input type="text" value="{:$_PARAMS.key_serch:}" name="key_serch">
				<input type="submit" value="搜索">
			</form>
		</td>
	</tr>
	<tr>
		<th>ID</th>
		<th>货品BN</th>
		<th>商品标题</th>
		<th>供应商</th>
		<th>关联分类</th>
		<th>成本价</th>
		<th>对标价</th>
		<th>实时库存</th>
		<th>是否失效</th>
		<th>操作</th>
	</tr>
	{:foreach from=$data->Data value=v:}
		<tr>
			<td align="center">{:$v.id:}</td>
			<td align="center">{:$v.bn:}</td>
			<td>
				<!--<a href="/index.php?call=extspu.extspu&sku={:$v.ext_spu_id:}" title="查看外部SPU列表" style="float:left;"><i class="fas fa-arrow-alt-circle-left"></i></a>-->
				<a href="/index.php?call=extsku.extsku_info&ext_sku_id={: $v.id :}">{:$v.name | string.key_highlight $_PARAMS.key_serch:}</a>
				{:if $v.extspu_spu_id == 0:}
				{:else if:}
					<!--<a href="/index.php?call=spu.spu&spu_id={:$v.extspu_spu_id:}" title="查看SPU列表" style="float:right;"><i class="fas fa-arrow-alt-circle-right"></i></a>-->
					{:/if:}
			</td>
			<td align="center">{:$v.supplier:}</td>
			<td>{:$v.cats:}</td>
			<td align="right">{:$v.price_base | number.currency:}</td>
			<td align="right">{:$v.price_ref | number.currency:}</td>
			<td align="right">{:$v.stock:}</td>
			<td align="center">{:$v.status | boolean.iconic:}</td>
			<td>
				<a href="/index.php?call=extsku.extsku_picture&ext_sku_id={:$v.id:}&type=0">商品素材</a>
				<a href="/index.php?call=extsku.extsku_picture&ext_sku_id={:$v.id:}&type=1">内容素材</a>
			</td>
		</tr>
	{:/foreach:}
	<tr class="pager">
		<td colspan="13">
			{:include file="_g/pager.tpl" Result=$data:}
		</td>
	</tr>
</table>
<script type="text/javascript">

</script>
{:include file="_g/footer.tpl":}