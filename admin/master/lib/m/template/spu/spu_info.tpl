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
		<td class="td1">商品名称</td>
		<td colspan="3">{:$res.title:}</td>
	</tr>
	<tr>
		<td class="td1">分类名称</td>
		<td class="td2">{:$res.c_name:}</td>
		<td class="td1">供应商</td>
		<td class="td2">{:$res.s_name:}</td>
	</tr>
	<tr>
		<td class="td1">品牌名称</td>
		<td class="td2">{:$res.b_name:}</td>
		<td class="td1">条码</td>
		<td class="td2">{:$res.barcode:}</td>
	</tr>
	<tr>
		<td class="td1">货号</td>
		<td class="td2">{:$res.serial:}</td>

	</tr>
	<tr>
		<td class="td1">单位重量</td>
		<td class="td2">{:$res.weight:}</td>
		<td class="td1">单位</td>
		<td class="td2">{:$res.unit:}</td>
	</tr>
	<tr>
		<td class="td1">是否虚拟产品</td>
		<td class="td2">{:if $v.is_virtual == 0:}
			否
			{:else:}
			是
			{:/if:}</td>
		<td class="td1">是否赠品</td>
		<td class="td2">{:if $v.is_gift == 0:}否{:else:}是{:/if:}</td>
	</tr>
	<tr>
		<td class="td1">是否捆绑</td>
		<td class="td2">{:if $v.is_bind == 0:}否{:else:}是{:/if:}</td>
		<td class="td1">是否自提</td>
		<td class="td2">{:if $v.is_zhiti == 0:}否{:else:}是{:/if:}</td>
	</tr>
	<tr>
		<td class="td1">状态</td>
		<td class="td2">{:if $v.status == 0:}下架{:else:}上架{:/if:}</td>
		<td class="td1"></td>
		<td class="td2"></td>
	</tr>

	<tr>
		<td class="td1">预定上架时间</td>
		<td class="td2">{:$res.online_time:}</td>
		<td class="td1">预定下架时间</td>
		<td class="td2">{:$res.offline_time:}</td>
	</tr>

	<tr class="info">
		<td class="td1">主图</td>
		<td class="td1" colspan="3">
			<ul>
				{:foreach from=$ImgMain value=item:}
				<li><img src="https://r.yuemee.com/upload{:$item.Picture:}" width="80"/></li>
					{:/foreach:}
			</ul>
		</td>
	</tr>
	<tr class="info">
		<td class="td1">内容</td>
		<td class="td1" colspan="3">
			<ul>
				{:foreach from=$ImgCont value=item:}
				<li><img src="https://r.yuemee.com/upload{:$item.Picture:}" width="80"/></li>
					{:/foreach:}
			</ul>
		</td>
	</tr>
	<tr class="info">
		<td class="td1">活动</td>
		<td class="td1" colspan="3">
			<ul>
				{:foreach from=$ImgLoop value=item:}
				<li><img src="https://r.yuemee.com/upload{:$item.Picture:}" width="80"/></li>
					{:/foreach:}
			</ul>
		</td>
	</tr>
	<tr></tr>
</table>
<div id="show">
	<div id="infotit">商品详情</div>
	<div style="height:20px;"></div>
	{:$res.intro:}
</div>	
<style>
	.info .td1 ul li{
		float:left;
		margin-right: 10px;
	}
</style>

{:include file="_g/footer.tpl":}
<script>
</script>
