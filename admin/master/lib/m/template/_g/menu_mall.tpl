<ul class="TabPages">
	<li {:if $_RUNTIME->ticket->action == 'index':}class="current"{:/if:}><a href="/index.php?call=mall.index">售卖概要</a></li>
	<li {:if $_RUNTIME->ticket->action == 'catagory':}class="current"{:/if:}><a href="/index.php?call=mall.catagory">分类</a></li>
	<!--<li {:if $_RUNTIME->ticket->action == 'spu_a':}class="current"{:/if:}><a href="/index.php?call=mall.spu_a">在线SPU</a></li>
	<li {:if $_RUNTIME->ticket->action == 'spu_b':}class="current"{:/if:}><a href="/index.php?call=mall.spu_b">无效SPU</a></li>
	<li {:if $_RUNTIME->ticket->action == 'sku_a':}class="current"{:/if:}><a href="/index.php?call=mall.sku_a">在线SKU</a></li>
	<li {:if $_RUNTIME->ticket->action == 'sku_b':}class="current"{:/if:}><a href="/index.php?call=mall.sku_b">待审SKU</a></li>
	<li {:if $_RUNTIME->ticket->action == 'sku_c':}class="current"{:/if:}><a href="/index.php?call=mall.sku_c">下架SKU</a></li>
	<li {:if $_RUNTIME->ticket->action == 'sku_d':}class="current"{:/if:}><a href="/index.php?call=mall.sku_d">无效SKU</a></li>
	<li {:if $_RUNTIME->ticket->action == 'material':}class="current"{:/if:}><a href="/index.php?call=mall.material">素材</a></li>-->
	<li {:if $_RUNTIME->ticket->action == 'cart':}class="current"{:/if:}><a href="/index.php?call=mall.cart">购物车</a></li>
	<li {:if $_RUNTIME->ticket->action == 'rebate':}class="current"{:/if:}><a href="/index.php?call=mall.rebate">返利</a></li>
	<li {:if $_RUNTIME->ticket->action == 'verify':}class="current"{:/if:}><a href="/index.php?call=mall.verify">SKU审核</a></li>
	<li {:if $_RUNTIME->ticket->action == 'ext_sku_verify':}class="current"{:/if:}><a href="/index.php?call=mall.ext_sku_verify">内购通知</a></li>
	<li {:if $_RUNTIME->ticket->action == 'sku_task':}class="current"{:/if:}><a href="/index.php?call=mall.sku_task">排期</a></li>
	<li {:if $_RUNTIME->ticket->action == 'discount':}class="current"{:/if:}><a href="/index.php?call=mall.discount">优惠券</a></li>
</ul>