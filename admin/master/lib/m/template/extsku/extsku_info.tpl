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
	<tr class="tit">
		<td class="td1">商品名称</td>
		<td colspan="3">{:$name:}</td>
	</tr>
	<tr class="tit">
		<td class="td1">SPU名称</td>
		<td colspan="3">{:$title:}</td>
	</tr>
	<tr>
		<td class="td1">成本价</td>
		<td class="td2">{:$price_base:}</td>
		<td class="td1">原价</td>
		<td class="td2">{:$price:}</td>
	</tr>
	<tr>
		<td class="td1">零售价</td>
		<td class="td2">{:$price_market:}</td>
		<td class="td1">商品bn</td>
		<td class="td2">{:$bn:}</td>
	</tr>
	<tr>
		<td class="td1">品牌</td>
		<td class="td2">{:$ppname:}</td>
		<td class="td1">供应商</td>
		<td class="td2">{:$gysname:}</td>
	</tr>
	
	<tr>
		<td class="td1">创建时间</td>
		<td class="td2">{:$create_time | number.datetime:}</td>
		<td class="td1"></td>
		<td class="td2"></td>
	</tr>
	<tr></tr>
</table>
<div id="show">
	<div id="infotit">商品详情</div>
	<div style="height:20px;"></div>
	{:$intro:}
</div>	
<script type="text/javascript">

</script>
{:include file="_g/footer.tpl":}
