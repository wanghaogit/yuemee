<ul class="TabPages">
	<li {:if $_RUNTIME->ticket->action == 'index':}class="current"{:/if:}><a href="/index.php?call=cheif.index">总监列表</a></li>
	<li {:if $_RUNTIME->ticket->action == 'order':}class="current"{:/if:}><a href="/index.php?call=cheif.order">卡位订单</a></li> 
	<li {:if $_RUNTIME->ticket->action == 'finance':}class="current"{:/if:}><a href="/index.php?call=cheif.finance">总监账目</a></li>
</ul>