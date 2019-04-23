{:include file="_g/header.tpl" Title="报表":}
<style>
	h2{
		font-weight:normal;
		font-size:20px;
	}
	#tab tr td{
		font-size:15px;
		width:140px;
		text-align:center;
	}
	th{
		width:200px;
	}
	.mon{
		font-weight:bold;
	}
</style>
<!--
<table border="0" cellspacing="0" cellpadding="0" class="Grid" id="tab">
	<caption>订单统计</caption>
{:foreach from=$lis value=v:}
<tr>
{:foreach from=$v value=vv:}
<th>{:$vv['time']:}</th>		
<td>
	待支付：{:$vv['a1']:}<br>
	代发货：{:$vv['a4']:}<br>
	运输中：{:$vv['a5']:}<br>
	已签收：{:$vv['a6']:}<br>
	已确认：{:$vv['a7']:}<br>
	已关闭：{:$vv['a13'] + $vv['a11'] + $vv['a12']:}
</td>
{:/foreach:}
</tr>
{:/foreach:}
<tr>
	<th>本月</th>
	<td style="font-weight:bold;">
		待支付：{:$mon[1]['num']:}<br>
		代发货：{:$mon[4]['num']:}<br>
		运输中：{:$mon[5]['num']:}<br>
		已签收：{:$mon[6]['num']:}<br>
		已确认：{:$mon[7]['num']:}<br>
		已关闭：{:$mon[10]['num']:}
	</td>
</tr>
</table>-->
<div id='aa'>
	<table border="0" cellspacing="0" cellpadding="0" class="Grid" id="tab">
		<caption>订单统计</caption>
		<tr>
			<td colspan="6" style="text-align:left;" >
				<span style="color:red;">仅统计 待支付，待发货，运输中，已签收，已确认，已关闭订单</span>
			</td>
			<td><button onclick="printdiv('aa')" style="width:100%;height:100%;">打印</button></td>
		</tr>
		<th style='width:200px;'></th>
		<th style='width:200px;'>待支付</th>
		<th style='width:200px;'>待发货</th>
		<th style='width:200px;'>运输中</th>
		<th style='width:200px;'>已签收</th>
		<th style='width:200px;'>已确认</th>
		<th style='width:200px;'>已关闭</th>
		<tr class="mon">
			<td>本月</td>
			<td>{:if $mon[1]['num'] > 0:}{: $mon[1]['num'] :}{:else:}{:/if:}</td>
			<td>{:if $mon[4]['num'] > 0:}{: $mon[4]['num'] :}{:else:}{:/if:}</td>
			<td>{:if $mon[5]['num'] > 0:}{: $mon[5]['num'] :}{:else:}{:/if:}</td>
			<td>{:if $mon[6]['num'] > 0:}{: $mon[6]['num'] :}{:else:}{:/if:}</td>
			<td>{:if $mon[7]['num'] > 0:}{: $mon[7]['num'] :}{:else:}{:/if:}</td>
			<td>{:if $mon[10]['num'] > 0:}{: $mon[10]['num'] :}{:else:}{:/if:}</td>
		</tr>
		{:foreach from=$lis value=v:}

		{:foreach from=$v value=vv:}
		<tr>
			<td>{:$vv['time']:}</td>
			<td>{:if $vv['a1'] > 0:}{:$vv['a1']:}{:else:}{:/if:}</td>
			<td>{:if $vv['a4'] > 0:}{:$vv['a4']:}{:else:}{:/if:}</td>
			<td>{:if $vv['a5'] > 0:}{:$vv['a5']:}{:else:}{:/if:}</td>
			<td>{:if $vv['a6'] > 0:}{:$vv['a6']:}{:else:}{:/if:}</td>
			<td>{:if $vv['a7'] > 0:}{:$vv['a7']:}{:else:}{:/if:}</td>
			<td>{:if $vv['a13'] + $vv['a11'] + $vv['a12'] > 0:}{:$vv['a13'] + $vv['a11'] + $vv['a12']:}{:else:}{:/if:}</td>
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
