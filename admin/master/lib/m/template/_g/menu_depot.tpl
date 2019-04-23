<ul class="TabPages">
	<li{:if $_RUNTIME->ticket->action == 'index':} class="current"{:/if:}><a href="/index.php?call=depot.index">库存首页</a></li>
	<li{:if $_RUNTIME->ticket->action == 'supplier':} class="current"{:/if:}><a href="/index.php?call=depot.supplier">供应商</a></li>
	<li{:if $_RUNTIME->ticket->action == 'brand':} class="current"{:/if:}><a href="/index.php?call=depot.brand">品牌</a></li>
	<!--<li{:if $_RUNTIME->ticket->action == 'extspu':} class="current"{:/if:}><a href="/index.php?call=depot.extspu">外部SPU</a></li>
	<li{:if $_RUNTIME->ticket->action == 'extspu_picture':} class="current"{:/if:}><a href="/index.php?call=depot.extspu_picture">外部SPU素材</a></li>
	<li{:if $_RUNTIME->ticket->action == 'extsku':} class="current"{:/if:}><a href="/index.php?call=depot.extsku">外部SKU</a></li>
	<li{:if $_RUNTIME->ticket->action == 'extsku_picture':} class="current"{:/if:}><a href="/index.php?call=depot.extsku_picture">外部SKU素材</a></li>-->
</ul>