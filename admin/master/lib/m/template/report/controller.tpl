{:include file="_g/header.tpl" Title="报表":}
<style>

	.tab tr td{
		font-size:15px;		
		letter-spacing:2px;
		width:150px;
		text-align:center;
	}
	.mon{
		font-weight:bold;
	}
	#cheif,#director{
		display:none;
	}
</style>
<button onclick="hideshow('vip')">VIP统计</button><button onclick="hideshow('cheif')">总监统计</button><button onclick="hideshow('director')">经理统计</button>
<br/><br/>
<div id="aa">
	<table border="0" cellspacing="0" cellpadding="0" class="Grid tab" id="vip">
		<tr>
			<td colspan="7"></td>
			<td><button onclick="printdiv('aa')" style="width:100%;height:100%;">打印</button></td>
		</tr>
		<tr>
			<th></th>
			<th>总计</th>
			<th>非VIP</th>
			<th>测试VIP</th>
			<th>免费VIP</th>
			<th>卡充VIP</th>
			<th>兑换VIP</th>
			<th>购买VIP</th>
		</tr>
		<tr  class="mon">
			<td>本月</td>
			<td>{:$mon['mv']:}</td>
			<td>{:$mon['mv0']:}</td>
			<td>{:$mon['mv1']:}</td>
			<td>{:$mon['mv2']:}</td>
			<td>{:$mon['mv3']:}</td>
			<td>{:$mon['mv4']:}</td>
			<td>{:$mon['mv5']:}</td>
		</tr>
		{:foreach from=$lis value=v:}
		{:foreach from=$v value=vv:}
		<tr>
			<td>{:$vv['time']:}</td>		
			<td>{: if $vv['vip']['num'] > 0:}{:$vv['vip']['num']:}{:else:}{:/if:}</td>
			<td>{: if $vv['vip0']['num'] > 0:}{:$vv['vip0']['num']:}{:else:}{:/if:}</td>
			<td>{: if $vv['vip1']['num'] > 0:}{:$vv['vip1']['num']:}{:else:}{:/if:}</td>
			<td>{: if $vv['vip2']['num'] > 0:}{:$vv['vip2']['num']:}{:else:}{:/if:}</td>
			<td>{: if $vv['vip3']['num'] > 0:}{:$vv['vip3']['num']:}{:else:}{:/if:}</td>
			<td>{: if $vv['vip4']['num'] > 0:}{:$vv['vip4']['num']:}{:else:}{:/if:}</td>
			<td>{: if $vv['vip5']['num'] > 0:}{:$vv['vip5']['num']:}{:else:}{:/if:}</td>
		</tr>
		{:/foreach:}

		{:/foreach:}

	</table>
</div>
<div id="bb">
	<table border="0" cellspacing="0" cellpadding="0" class="Grid tab" id="cheif">
		<tr>
			<td colspan="5"></td>
			<td><button onclick="printdiv('bb')" style="width:100%;height:100%;">打印</button></td>
		</tr>
		<tr>
			<th></th>
			<th>总计</th>
			<th>非总监</th>
			<th>激活总监</th>
			<th>自然晋升总监</th>
			<th>卡位总监</th>
		</tr>
		<tr class="mon">
			<td>本月</td>
			<td>{:$mon['mc']:}</td>
			<td>{:$mon['mc0']:}</td>
			<td>{:$mon['mc1']:}</td>
			<td>{:$mon['mc2']:}</td>
			<td>{:$mon['mc3']:}</td>
		</tr>
		{:foreach from=$lis value=v:}

		{:foreach from=$v value=vv:}
		<tr>
			<td>{:$vv['time']:}</td>		
			<td>{: if $vv['cheif']['num'] > 0:}{:$vv['cheif']['num']:}{:else:}{:/if:}</td>
			<td>{: if $vv['cheif0']['num'] > 0:}{:$vv['cheif0']['num']:}{:else:}{:/if:}</td>
			<td>{: if $vv['cheif1']['num'] > 0:}{:$vv['cheif1']['num']:}{:else:}{:/if:}</td>
			<td>{: if $vv['cheif2']['num'] > 0:}{:$vv['cheif2']['num']:}{:else:}{:/if:}</td>
			<td>{: if $vv['cheif3']['num'] > 0:}{:$vv['cheif3']['num']:}{:else:}{:/if:}</td>
		</tr>
		{:/foreach:}

		{:/foreach:}

	</table>
</div>
<div id="cc">
	<table border="0" cellspacing="0" cellpadding="0" class="Grid tab" id="director">
		<tr>
			<td colspan="4"></td>
			<td><button onclick="printdiv('cc')" style="width:100%;height:100%;">打印</button></td>
		</tr>
		<tr>
			<th></th>
			<th>总计</th>
			<th>非总经理</th>
			<th>自然晋升总经理</th>
			<th>卡位总经理</th>
		</tr>
		<tr class="mon">
			<td>本月</td>
			<td>{:$mon['md']:}</td>
			<td>{:$mon['md0']:}</td>
			<td>{:$mon['md1']:}</td>
			<td>{:$mon['md2']:}</td>
		</tr>
		{:foreach from=$lis value=v:}

		{:foreach from=$v value=vv:}
		<tr>
			<td>{:$vv['time']:}</td>		
			<td>{: if $vv['director']['num'] > 0:}{:$vv['director']['num']:}{:else:}{:/if:}</td>
			<td>{: if $vv['director0']['num'] > 0:}{:$vv['director0']['num']:}{:else:}{:/if:}</td>
			<td>{: if $vv['director1']['num'] > 0:}{:$vv['director1']['num']:}{:else:}{:/if:}</td>
			<td>{: if $vv['director2']['num'] > 0:}{:$vv['director2']['num']:}{:else:}{:/if:}</td>
		</tr>
		{:/foreach:}

		{:/foreach:}
	</table>
</div>
<script>
	function hideshow(t) {
		$('.tab').hide();
		var str = '#' + t;
		$(str).show();
	}
</script>
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
