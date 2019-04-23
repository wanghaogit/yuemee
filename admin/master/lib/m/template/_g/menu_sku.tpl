<ul class="TabPages">
	<li{:if $_RUNTIME->ticket->action == 'index':} class="current"{:/if:}><a href="/index.php?call=sku.index">首页</a></li>
	<li{:if $_RUNTIME->ticket->action == 'sku_a':} class="current"{:/if:}><a href="/index.php?call=sku.sku_a">在线sku</a></li>
	<li{:if $_RUNTIME->ticket->action == 'sku_b':} class="current"{:/if:}><a href="/index.php?call=sku.sku_b">待审sku</a></li>
	<li{:if $_RUNTIME->ticket->action == 'sku_c':} class="current"{:/if:}><a href="/index.php?call=sku.sku_c">下架sku</a></li>
	<li{:if $_RUNTIME->ticket->action == 'sku_d':} class="current"{:/if:}><a href="/index.php?call=sku.sku_d">无效sku</a></li>
	<li{:if $_RUNTIME->ticket->action == 'material':} class="current"{:/if:}><a href="/index.php?call=sku.material">素材</a></li>
</ul>