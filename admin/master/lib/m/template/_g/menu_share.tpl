<ul class="TabPages">
	<li {:if $_RUNTIME->ticket->action == 'index':}class="current"{:/if:}><a href="/index.php?call=share.index">一览</a></li>
	<li {:if $_RUNTIME->ticket->action == 'template':}class="current"{:/if:}><a href="/index.php?call=share.template">模板</a></li>
	<li {:if $_RUNTIME->ticket->action == 'picture':}class="current"{:/if:}><a href="/index.php?call=share.picture">素材</a></li>
</ul>