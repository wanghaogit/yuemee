<ul class="TabPages">
	<li{:if $_RUNTIME->ticket->action == 'index':} class="current"{:/if:}><a href="/index.php?call=extspu.index">首页</a></li>
	<li{:if $_RUNTIME->ticket->action == 'extspu_picture':} class="current"{:/if:}><a href="/index.php?call=extspu.extspu_picture">素材</a></li>
</ul>