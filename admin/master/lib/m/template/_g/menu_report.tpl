<ul class="TabPages">
	<li {:if $_RUNTIME->ticket->action == 'index':}class="current"{:/if:}><a href="/index.php?call=report.index">报表中心</a></li>
	<li {:if $_RUNTIME->ticket->action == 'user':}class="current"{:/if:}><a href="/index.php?call=report.user">用户</a></li>
	<li {:if $_RUNTIME->ticket->action == 'controller':}class="current"{:/if:}><a href="/index.php?call=report.controller">管理</a></li>
	<li {:if $_RUNTIME->ticket->action == 'goods':}class="current"{:/if:}><a href="/index.php?call=report.goods">商品</a></li>
	<li {:if $_RUNTIME->ticket->action == 'order':}class="current"{:/if:}><a href="/index.php?call=report.order">订单状态</a></li>
	<li {:if $_RUNTIME->ticket->action == 'finance':}class="current"{:/if:}><a href="/index.php?call=report.finance">订单流水</a></li>
</ul>