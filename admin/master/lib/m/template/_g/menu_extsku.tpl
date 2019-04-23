<ul class="TabPages">
	<li{:if $_RUNTIME->ticket->action == 'index':} class="current"{:/if:}><a href="/index.php?call=extsku.index">首页</a></li>
	<li{:if $_RUNTIME->ticket->action == 'extsku_picture':} class="current"{:/if:}><a href="/index.php?call=extsku.extsku_picture">素材</a></li>
</ul>