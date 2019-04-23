{:include file="_g/header.tpl" Title="商城/货架":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		货架管理
	</caption>
	<tr>
		<th>ID</th>
		<th>分类</th>
		<th colspan="2">供应商</th>
		<th colspan="2">名称</th>
		<th colspan="2">条码</th>
		<th colspan="2">单位重量</th>
		<th colspan="2">库存</th>
		<th colspan="2">价格</th>
		<th>佣金</th>
		<th colspan="2">维护人</th>
		<th>操作</th>
	</tr>
	{:foreach from=$data->Data value=SH:}
		<tr>
			<td rowspan="2">{:$SH.id:}</td>
			<td rowspan="2">{:$SH.name_1:}</td>
			<td>供货</td>
			<td>{:$SH.supplier_id:}</td>
			<td>SPU</td>
			<td><a href="/index.php?call=depot.shelf_detail&id={:$SH.id:}">{:$SH.ptitle:}</a></td>
			<td rowspan="2">条码</td>
			<td rowspan="2">{:$SH.barcode:}</td>
			<td>单位</td>
			<td>{:$SH.unit:}</td>
			<td>上架</td>
			<td>{:$SH.quantity:}</td>
			<td>成本</td>
			<td>{:$SH.price_base | number.currency:}</td>
			<td rowspan="2">{:$SH.rebate_vip | number.currency:}</td>
			<td>录入</td>
			<td>{:$SH.create_user:}</td>
			<td rowspan="2" class="operator">
				{:if $SH.status == 0:}
					<a>下架</a>
				{:elseif $SH.status == 1:}
					<a href="/index.php?call=depot.online&skuid={:$SH.id:}">上架</a>
				{:elseif $SH.status == 3:}
					<a>已上架</a>
				{:/if:}
				<a href="/index.php?call=mall.schedule&shelf_id={:$SH.id:}">排期</a>
			</td>
		</tr>
		<tr>
			<td>品牌</td>
			<td></td>
			<td>标题</td>
			<td>{:$SH.title:}</td>
			<td>重量</td>
			<td>{:$SH.weight | string.sprintf : '%d':} 克</td>
			<td>实时</td>
			<td>{:$SH.qty_left:}</td>
			<td>平台</td>
			<td>{:$SH.price_sale | number.currency:}</td>
			<td>审核</td>
			<td>{:$SH.audit_user:}</td>
		</tr>
	{:/foreach:}
</table>
{:include file="_g/footer.tpl":}
