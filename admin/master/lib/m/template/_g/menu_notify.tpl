<ul class="TabPages">
	<li {:if $_RUNTIME->ticket->action == 'index':}class="current"{:/if:}><a href="/index.php?call=notify.index">公告</a></li>
	<li {:if $_RUNTIME->ticket->action == 'private':}class="current"{:/if:}><a href="/index.php?call=notify.private">私信</a></li>
	<li {:if $_RUNTIME->ticket->action == 'message':}class="current"{:/if:}><a href="/index.php?call=notify.message">通知</a></li>
	<li {:if $_RUNTIME->ticket->action == 'assist':}class="current"{:/if:}><a href="/index.php?call=notify.assist">客服</a></li>
	<li {:if $_RUNTIME->ticket->action == 'train':}class="current"{:/if:}><a href="/index.php?call=notify.train">培训</a></li>
	<li {:if $_RUNTIME->ticket->action == 'sms':}class="current"{:/if:}><a href="/index.php?call=notify.sms">验证码</a></li>
	<li {:if $_RUNTIME->ticket->action == 'im':}class="current"{:/if:}><a href="/index.php?call=notify.im">IM</a></li>
</ul>