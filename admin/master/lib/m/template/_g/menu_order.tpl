
<ul class="TabPages">
	<li {:if $_RUNTIME->ticket->action == 'index':}class="current"{:/if:}><a href="/index.php?call=order.index">订单</a></li>
	<li {:if $_RUNTIME->ticket->action == 'logistics':}class="current"{:/if:}><a href="/index.php?call=order.logistics">物流</a></li>
	<li {:if $_RUNTIME->ticket->action == 'cancel':}class="current"{:/if:}><a href="/index.php?call=order.cancel">退货</a></li>
	<li {:if $_RUNTIME->ticket->action == 'comment':}class="current"{:/if:}><a href="/index.php?call=order.comment">评价</a></li>
	<li {:if $_RUNTIME->ticket->action == 'gift':}class="current"{:/if:}><a href="/index.php?call=order.gift">自发礼包</a></li>
</ul>
