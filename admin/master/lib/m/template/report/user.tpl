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
<!--<table border="0" cellspacing="0" cellpadding="0" class="Grid tab">
	<caption>新增用户统计</caption>

	{:foreach from=$lis value=v:}
	<tr>
		{:foreach from=$v value=vv:}
		<th>{:$vv['time']:}</th>		
		<td>
			<span class='mon'>普通用户：</span>{:$vv['user']['num']:}<br>
			<span class='mon'>微信用户：</span>{:$vv['wechat']['num']:}
		</td>
		{:/foreach:}
	</tr>
	{:/foreach:}
	<th>本月</th>
	<td>
		<span class='mon'>普通用户：</span>{:$mon['um']:}<br>
		<span class='mon'>微信用户：</span>{:$mon['wm']:}
	</td>
</table>-->
	<div id="aa">
<table border="0" cellspacing="0" cellpadding="0" class="Grid tab">
	<caption>新增用户统计</caption>
	<tr>
		<td colspan="2"></td>
		<td><button onclick="printdiv('aa')" style="width:100%;height:100%;">打印</button></td>
	</tr>
	<tr class="mon">
		<td></td>
		<td>新增普通用户</td>
		<td>新增微信用户</td>
	</tr>
	<tr class="mon">
		<td>本月</td>
		<td>{:$mon['um']:}</td>
		<td>{:$mon['wm']:}</td>
	</tr>
	{:foreach from=$lis value=v:}

	{:foreach from=$v value=vv:}
	<tr>
		<td>
			{:$vv['time']:}
		</td>
		<td>
			{:$vv['user']['num']:}
		</td>		
		<td>
			{:$vv['wechat']['num']:}
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