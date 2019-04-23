{:include file="_g/header.tpl" Title="报表":}
<style>
	.tab tr td{
		font-size:15px;		
		letter-spacing:3px;
		width:140px;
		text-align:center;
	}
	.mon{
		font-weight:bold;
	}
</style>
<div id="aa">
<table border="0" cellspacing="0" cellpadding="0" class="Grid tab">
	<caption>订单财务流水统计</caption>
	<tr>
		<td colspan="3"></td>
		<td><button onclick="printdiv('aa')" style="width:100%;height:100%;">打印</button></td>
	</tr>
	<tr>
		<td colspan="16">
			<span style="color:red;">仅统计 已支付，待发货，运输中，已签收，已确认，已评价订单</span>
		</td>
	</tr>
	
	<tr>
		<th></th>
		<th>售出</th>
		<th>成本</th>
		<th>利润</th>
	</tr>
	<tr>
		<td>本月</td>
		<td>
			￥{:$mon['sell']['sum']:}
		</td>
		<td>￥{:$mon['price']:}</td>
		<td>￥{:$mon['money']:}</td>
	</tr>
	{:foreach from=$lis value=v:}
	
		{:foreach from=$v value=vv:}
		<tr>
		<td>{:$vv['time']:}</td>		
		<td>
			￥{:$vv['num']:}
		</td>
		<td>
			￥{:$vv['price']:}
		</td>
		<td>
			￥{:$vv['money']:}
		</td>
		</tr>
		{:/foreach:}
	
	{:/foreach:}
	
</table>
</div>
	<script>
		/**
	 * 打印局部div
	 * @param printpage 局部div的ID
	 */
	function printdiv(printpage) {
		var headhtml = "<html><head><title></title></head><body>";
		var foothtml = "</body>";
		// 获取div中的html内容
		var newhtml = document.all.item(printpage).innerHTML;
		// 获取div中的html内容，jquery写法如下
		// var newhtml= $("#" + printpage).html();

		// 获取原来的窗口界面body的html内容，并保存起来
		var oldhtml = document.body.innerHTML;

		// 给窗口界面重新赋值，赋自己拼接起来的html内容
		document.body.innerHTML = headhtml + newhtml + foothtml;
		// 调用window.print方法打印新窗口
		window.print();

		// 将原来窗口body的html值回填展示
		document.body.innerHTML = oldhtml;
		return false;
	}
	</script>
{:include file="_g/footer.tpl":}