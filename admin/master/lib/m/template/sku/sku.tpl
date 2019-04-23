{:include file="_g/header.tpl" Title="库存/SKU":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption style="text-align: left;">

	</caption>
	<tr>
		<td>
			查询
		</td>
		<td colspan="21">
			<form action="/index.php?call=mall.sku" method="post">
				供应商<select id="supplier_serch" name="supplier_serch">
					<option value="0">请选择</option>
					{:foreach from=$supplier value=v:}
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
		<th>分类</th>
		<th style="width:220px;">标题</th>
		<th style="width:220px;">spu标题</th>
		<th>品牌 </th>
		<th>供应商</th>
		<th>条码</th>
		<th>重量</th>
		<th>单位</th>
		<th>库存</th>
		<th>价格</th>
		<th>赠送阅币</th>
		<th>状态</th>
		<th>操作</th>
	</tr>
	{:foreach from=$data->Data value=SKU:}
	<tr>
		<td>

			{:$SKU.id:}
		</td>
		<td>{:$SKU.c_name:}</td>
		<td>
			<a href="/index.php?call=mall.spu&spu_id={:$SKU.spu_id:}" title="查看SPU列表" style="float:left;"><i class="fas fa-arrow-alt-circle-left"></i></a>
				{:$SKU.title:}
		</td>
		<td><a href="/index.php?call=mall.sku_detail&id={:$SKU.id:}">{:$SKU.sp_name:}</a></td>
		<td>{:$SKU.b_name:}</td>
		<td>{:$SKU.su_name:}</td>
		<td>{:$SKU.barcode:}</td>
		<td>{:$SKU.weight | string.sprintf : '%d':}</td>
		<td>{:$SKU.unit:}</td>
		<td>{:$SKU.depot:}</td>
		<td>
			<ul>
				<li>成本价：<span style="float:right;">{:$SKU.price_base | number.currency:}</span></li>
				<li>平台价：<span style="float:right;">{:$SKU.price_sale | number.currency:}</span></li>
				<li>零售价：<span style="float:right;">{:$SKU.price_ref | number.currency:}</span></li>
				<li>有邀请码价格：<span style="float:right;">{:$SKU.price_inv | number.currency:}</span></li>
				<li>无邀请码价格：<span style="float:right;">{:$SKU.price_vip | number.currency:}</span></li>
				<li>VIP返佣：<span style="float:right;">{:$SKU.rebate_vip | number.currency:}</span></li>
			</ul>
		</td>
		<!--<td rowspan="3" align="center"></td>-->
		<td>
			<ul>
				<li>购买者赠送阅币：<span style="float:right">{:$SKU.coin_buyer:}</span></li>
				<li>邀请者赠送阅币：<span style="float:right">{:$SKU.coin_inviter:}</span></li>
			</ul>
		</td>
		<td >{: if $SKU.status == 0:}待审{: elseif $SKU.status == 1:}<span style="color:red;">驳回</span>
			{:elseif  $SKU.status == 2:}上架{:elseif  $SKU.status == 3:}下架{:elseif  $SKU.status == 4:}删除{:else:}{:/if:}</td>
		<td class="operator">
			<a href="/index.php?call=mall.material&sku_id={:$SKU.id:}&t=2">商品素材</a>
			<a href="/index.php?call=mall.material&sku_id={:$SKU.id:}&t=3">内容素材</a>
			<a href="/index.php?call=mall.update_sku&id={:$SKU.id:}">编辑详情</a>
			{: if $SKU.status == 0:}<a style="color:green;" href="/index.php?call=mall.statuschange&id={:$SKU.id:}&t=2">通过</a>
			<a style="color:red;" href="/index.php?call=mall.statuschange&id={:$SKU.id:}&t=1">驳回</a>{: elseif $SKU.status == 1:}{:else:}{:/if:}
			<!--<a href="/index.php?call=depot.shelves_sku&sku_id={:$SKU.id:}">上架</a>-->
			<!--<a href="/index.php?call=mall.statuschange&id={:$SKU.id:}&t=4">删除</a>-->

		</td>
	</tr>

	{:/foreach:}
	<tr class="paging">
		<td colspan="21">
			{:include file="_g/pager.tpl" Result=$data:}
		</td>
	</tr>
</table>
{:include file="_g/footer.tpl":}