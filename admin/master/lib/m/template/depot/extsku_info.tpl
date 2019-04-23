{:include file="_g/header.tpl" Title="SKU":}
<style>
	table{width:1000px;}
	tr{width:100%;height:35px;}
	.td1{width:20%;font-weight:bold;text-align:center;font-size:15px;}
	.td2{width:30%;font-size:14px;}
	.info{height:90px;}
</style>
<script type="text/javascript" src="/scripts/editor.js"></script>
<label style="float: left;">EXT_SKU详情：</label><br><br>
<table>
	<tr>
		<td class="td1">商品名称</td>
		<td colspan="3">{:$name:}</td>
	</tr>
	<tr>
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
		<td class="td1">视频地址</td>
		<td class="td2" colspan="3"><a href="{:$video:}">{:$video:}</a></td>
	</tr>
	<tr>
		<td class="td1">创建时间</td>
		<td class="td2">{:$create_time | number.datetime:}</td>
		<td class="td1"></td>
		<td class="td2"></td>
	</tr>
	<tr class="info">
		<td class="td1">详情</td>
		<td class="td2" colspan="3">{:$intro:}</td>
	</tr>
</table>
<script type="text/javascript">

</script>
{:include file="_g/footer.tpl":}
