{:include file="_g/header.tpl" Title="库存/SPU":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<tr>
		<td>查询</td>
		<td colspan="8">
			<form action="/index.php" method="get">
				<input type="hidden" name="call" value="{:$_RUNTIME->ticket->handler:}.{:$_RUNTIME->ticket->action:}" />
				供应商<select id="supplier_serch" name="sid">
					<option value="0">请选择</option>
					{:foreach from=$supplier value=v:}
						<option value="{:$v.id:}" {:if $_PARAMS.sid == $v.id:}selected="selected"{:/if:}>{:$v.name:}</option>
					{:/foreach:}
				</select>
				品牌：<select id="brand_serch" name="bid">
					<option value="0">请选择</option>
					{:foreach from=$brand value=v:}
						<option value="{:$v.id:}" {:if $_PARAMS.bid == $v.id:}selected="selected"{:/if:}>{:$v.name:}</option>
					{:/foreach:}
				</select>
				状态：<select id="status_serch" name="status_serch">
					<option value="0">--请选择--</option>
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
		<th>商品标题</th>
		<th>供应商</th>
		<th>关联分类</th>
		<th>成本价</th>
		<th>品牌</th>
		<th>状态</th>
		<th>操作</th>
	</tr>
	{:foreach from=$data->Data value=v:}
		<tr>
			<td>{:$v.id:}</td>
			<td>
				<a href="/index.php?call=extspu.extspu_info&spuid={:$v.id:}">{:$v.title | string.key_highlight $_PARAMS.key_serch:}</a>
				<!--<a href="/index.php?call=extsku.extsku&spu_id={:$v.id:}" title="查看外部SKU列表" style="float:right;"><i class="fas fa-arrow-alt-circle-right"></i></a>-->
			</td>
			<td align="center">{:$v.supplier:}</td>
			<td>{:$v.ext_cat:}</td>
			<td align="right">{:$v.price_base | number.currency:}</td>
			<td>{:$v.brand:}</td>
			<td align="center">{:$v.status | boolean.iconic :}</td>
			<td>
				<a href="/index.php?call=extspu.extspu_picture&ext_spu_id={:$v.id:}&type=0">商品素材</a>
				<a href="/index.php?call=extspu.extspu_picture&ext_spu_id={:$v.id:}&type=1">内容素材</a>
			</td>
		</tr>
	{:/foreach:}
	<tr class="pager">
		<td colspan="9">
			{:include file="_g/pager.tpl" Result=$data:}
		</td>
	</tr>
</table>
<script type="text/javascript">

</script>
{:include file="_g/footer.tpl":}
