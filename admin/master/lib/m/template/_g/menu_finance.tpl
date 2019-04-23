<ul class="TabPages">
	<li{:if $_RUNTIME->ticket->action == 'index':} class="current"{:/if:}><a href="/index.php?call=finance.index">财务一览</a></li>
	<li{:if $_RUNTIME->ticket->action == 'charge':} class="current"{:/if:}><a href="/index.php?call=finance.charge">充值对账</a></li>
	<li{:if $_RUNTIME->ticket->action == 'offline':} class="current"{:/if:}><a href="/index.php?call=finance.offline">线下支付</a></li>
	<li{:if $_RUNTIME->ticket->action == 'card':} class="current"{:/if:}><a href="/index.php?call=finance.card">激活卡</a></li>
	<li{:if $_RUNTIME->ticket->action == 'withdraw':} class="current"{:/if:}><a href="/index.php?call=finance.withdraw">提现申请</a></li>
	<li{:if $_RUNTIME->ticket->action == 'bonus':} class="current"{:/if:}><a href="/index.php?call=finance.bonus">奖金发放</a></li>
	<li{:if $_RUNTIME->ticket->action == 'salary':} class="current"{:/if:}><a href="/index.php?call=finance.salary">工资结算</a></li>
	<li{:if $_RUNTIME->ticket->action == 'team':} class="current"{:/if:}><a href="/index.php?call=finance.team">团队绩效</a></li>
	<li{:if $_RUNTIME->ticket->action == 'supplier':} class="current"{:/if:}><a href="/index.php?call=finance.supplier">货款结算</a></li>
	<li{:if $_RUNTIME->ticket->action == 'tally_money':} class="current"{:/if:}><a href="/index.php?call=finance.tally_money">余额流水</a></li>
	<li{:if $_RUNTIME->ticket->action == 'tally_coin':} class="current"{:/if:}><a href="/index.php?call=finance.tally_coin">阅币流水</a></li>
	<li{:if $_RUNTIME->ticket->action == 'tally_profit':} class="current"{:/if:}><a href="/index.php?call=finance.tally_profit">佣金流水</a></li>
	<li{:if $_RUNTIME->ticket->action == 'tally_recruit':} class="current"{:/if:}><a href="/index.php?call=finance.tally_recruit">招聘流水</a></li>
</ul>