<ul class="TabPages">
	<li {:if $_RUNTIME->ticket->action == 'index':}class="current"{:/if:}><a href="/index.php?call=cms.index">内容管理</a></li>
	<li {:if $_RUNTIME->ticket->action == 'column':}class="current"{:/if:}><a href="/index.php?call=cms.column">栏目管理</a></li>
</ul>