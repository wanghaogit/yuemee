{:include file="_g/header.tpl" Title="报表":}
<style>
	h2{
		font-weight:normal;
		font-size:20px;
	}
	#tab tr td{
		font-size:15px;
		line-height:30px;
		width:140px;
		text-align:center;
	}
	
</style>
<div id="aa">
<table border="0" cellspacing="0" cellpadding="0" class="Grid" id="tab">
	<caption>商品统计</caption>
	<tr>
		<td colspan="5"></td>
		<td><button onclick="printdiv('aa')" style="width:100%;height:100%;">打印</button></td>
	</tr>
	<tr>
		<th></th>
		<th>待审商品</th>
		<th>打回商品</th>
		<th>通过商品</th>
		<th>下架商品</th>
		<th>删除商品</th>
	</tr>
	<tr class="mon">
		<td>本月</td>
		<td>{:$mon[0]:}</td>
		<td>{:$mon[1]:}</td>
		<td>{:$mon[2]:}</td>
		<td>{:$mon[3]:}</td>
		<td>{:$mon[4]:}</td>
	</tr>
	{:foreach from=$lis value=v:}
	
		{:foreach from=$v value=vv:}
		<tr>
		<td>{:$vv['time']:}</td>		
		<td>{: if $vv[0] > 0:}{:$vv[0]:}{:else:}{:/if:}</td>
		<td>{: if $vv[1] > 0:}{:$vv[1]:}{:else:}{:/if:}</td>
		<td>{: if $vv[2] > 0:}{:$vv[2]:}{:else:}{:/if:}</td>
		<td>{: if $vv[3] > 0:}{:$vv[3]:}{:else:}{:/if:}</td>
		<td>{: if $vv[4] > 0:}{:$vv[4]:}{:else:}{:/if:}</td>
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
