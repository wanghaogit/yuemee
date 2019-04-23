<ul class="TabPages">
	<li {:if $_RUNTIME->ticket->action == 'index':}class="current"{:/if:}><a href="/index.php?call=director.index">经理列表</a></li>
	<li {:if $_RUNTIME->ticket->action == 'order':}class="current"{:/if:}><a href="/index.php?call=director.order">卡位订单</a></li> 
	<li {:if $_RUNTIME->ticket->action == 'finance':}class="current"{:/if:}><a href="/index.php?call=director.finance">经理账目</a></li>
	<li {:if $_RUNTIME->ticket->action == 'team':}class="current"{:/if:}><a href="/index.php?call=director.team">直营团队</a></li>
	<li {:if $_RUNTIME->ticket->action == 'staff':}class="current"{:/if:}><a href="/index.php?call=director.staff">直营员工</a></li>
	<li {:if $_RUNTIME->ticket->action == 'perform':}class="current"{:/if:}><a href="/index.php?call=director.perform">直营绩效</a></li>
	<li {:if $_RUNTIME->ticket->action == 'salary':}class="current"{:/if:}><a href="/index.php?call=director.salary">直营工资</a></li>
</ul>