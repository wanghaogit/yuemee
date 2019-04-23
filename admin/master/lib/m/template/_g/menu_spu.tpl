<ul class="TabPages">
	<li{:if $_RUNTIME->ticket->action == 'index':} class="current"{:/if:}><a href="/index.php?call=spu.index">首页</a></li>
	<li{:if $_RUNTIME->ticket->action == 'spu_a':} class="current"{:/if:}><a href="/index.php?call=spu.spu_a">在线spu</a></li>
	<li{:if $_RUNTIME->ticket->action == 'spu_b':} class="current"{:/if:}><a href="/index.php?call=spu.spu_b">无效spu</a></li>
	<li{:if $_RUNTIME->ticket->action == 'material':} class="current"{:/if:}><a href="/index.php?call=spu.material">素材</a></li>
</ul>