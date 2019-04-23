{:include file="_g/header.tpl" Title="SKU":}
<style>
	table{width:1000px;}
	tr{width:100%;height:35px;}
	.td1{width:20%;font-weight:bold;text-align:center;font-size:15px;}
	.td2{width:30%;font-size:14px;}
	.info{height:90px;}
	#tab{background-color:rgba(105,105,105,0.2);}
	#head{font-size:20px;text-align:center;font-weight:bold;}
	.tit td{font-size:15px;}
	#show{width:1000px;background-color:#EEEEE0;}
	#infotit{font-size:20px;width:1000px;font-weight:bold;text-align:center;}
</style>
<script type="text/javascript" src="/scripts/editor.js"></script>


<table id="tab">
	<tr>
		<td colspan="4" id="head">
			商品属性
		</td>
	</tr>
	<tr></tr>
	<tr>
		<td class="td1">商品名称</td>
		<td colspan="3">{:$data.title:}</td>
	</tr>
	<tr>
		<td class="td1">所属分类</td>
		<td colspan="3">{:$data.cats:}</td>
	</tr>
	<tr>
		<td class="td1">条码</td>
		<td class="td2">{:$data.barcode:}</td>
		<td class="td1">成本价</td>
		<td class="td2">{:$data.price_base:}</td>
	</tr>
	<tr>
		<td class="td1">售价</td>
		<td class="td2">{:$data.price_sale:}</td>
		<td class="td1">零售价</td>
		<td class="td2">{:$data.price_ref:}</td>
	</tr>
	<tr>
		<td class="td1">成本价</td>
		<td class="td2">{:$data.price_base:}</td>
		<td class="td1">对标价</td>
		<td class="td2">{:$data.price_ref:}</td>
	</tr>
	<tr>
		<td class="td1">实时库存</td>
		<td class="td2">{:$data.depot:}</td>
		<td class="td1">货号</td>
		<td class="td2">{:$data.serial:}</td>
	</tr>
	<tr>
		<td class="td1">单位重量</td>
		<td class="td2">{:$data.weight:}</td>
		<td class="td1">单位</td>
		<td class="td2">{:$data.unit:}</td>
	</tr>
	<tr>
		<td class="td1">创建时间</td>
		<td class="td2">{:$data.create_time:}</td>
		<td class="td1">到期时间</td>
		<td class="td2"></td>
	</tr>
	<tr>
		<td class="td1">有邀请码会员价格</td>
		<td class="td2">{:$data.price_inv:}</td>
	</tr>
	<tr>
		<td class="td1">限购类型</td>
		{:if $data.limit_style == 0:}
		<td class="td2">不限购</td>
		{:elseif $data.limit_style == 1:}
		<td class="td2">按人头限购</td>
		{:elseif $data.limit_style == 2:}
		<td class="td2">按地址限购</td>
		{:else:}{:/if:}
		<td class="td1">限购数量</td>
		<td class="td2">{:$data.limit_size:}</td>
	</tr>
	<tr>
		<td class="td1">是否支持退换货</td>
		{:if $data.att_refund == 0:}
		<td class="td2">支持</td>
		{:elseif $data.att_refund == 1:}
		<td class="td2">不支持</td>
		{:else:}{:/if:}
		<td class="td1">商品状态</td>
		{:if $data.status == 0:}
		<td class="td2">待审</td>
		{:elseif $data.status == 1:}
		<td class="td2">打回</td>
		{:elseif $data.status == 2:}
		<td class="td2">通过</td>
		{:elseif $data.status == 3:}
		<td class="td2">下架</td>
		{:elseif $data.status == 4:}
		<td class="td2">删除</td>
		{:else:}{:/if:}
	</tr>
	<tr>
		<td class="td1">vip返佣</td>
		<td class="td2">{:$data.rebate_vip:}</td>
	</tr>
	<tr>
		<td class="td1">购买者赠送阅币</td>
		<td class="td2">{:$data.coin_buyer:}</td>
		<td class="td1">分享者赠送阅币</td>
		<td class="td2">{:$data.coin_inviter:}</td>
	</tr>
</table>
	<div id="show">
	<div id="infotit">商品详情</div>
	<div style="height:20px;"></div>
	{:$data.intro:}
</div>	
<script type="text/javascript">
</script>
{:include file="_g/footer.tpl":}