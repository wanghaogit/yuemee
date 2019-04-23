{:include file="_g/header.tpl" Title="库存/新增品类":}
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

<table id="tab">
	<tr>
		<td colspan="4" id="head">
			商品属性
		</td>
	</tr>
	<tr></tr>
	<tr>
		<td class="td1">SPU名称</td>
		<td colspan="3">{:$res.title:}</td>
	</tr>
	<tr>
		<td class="td1">供应商</td>
		<td class="td2">{:$res.supplier:}</td>
		<td class="td1">分类名称</td>
		<td class="td2">{:$res.c_name:}</td>
	</tr>
	<tr>
		<td class="td1">内部SPU名称</td>
		<td colspan="3">{:$res.spu_name:}</td>
	</tr>
	<tr>
		<td class="td1">内部分类</td>
		<td class="td2">{:$res.category_name:}</td>
		<td class="td1">品牌名称</td>
		<td class="td2">{:$res.brand_name:}</td>
	</tr>
	<tr>
		<td class="td1">成本价</td>
		<td class="td2">{:$res.price_base:}</td>
		<td class="td1">商品bn</td>
		<td class="td2">{:$res.bn:}</td>
	</tr>
	<tr>
		<td class="td1">图片素材</td>
		<td colspan="3"><img src="{:#URL_RES:}/upload{:$res.img_url:}" style="width:300px;" /></td>
	</tr>
	
	<tr></tr>
</table>

<div id="show">
	<div id="infotit">商品详情</div>
	<div style="height:20px;"></div>
	{:$intro:}
</div>	
{:include file="_g/footer.tpl":}
<script>
</script>
