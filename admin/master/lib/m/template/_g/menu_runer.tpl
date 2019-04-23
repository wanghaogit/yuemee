<ul class="TabPages">
	<li {:if $_RUNTIME->ticket->action == 'index':}class="current"{:/if:}><a href="/index.php?call=runer.index">排期</a></li>
	<li {:if $_RUNTIME->ticket->action == 'spage':}class="current"{:/if:}><a href="/index.php?call=runer.spage">应用内</a></li>
	<li {:if $_RUNTIME->ticket->action == 'dpage':}class="current"{:/if:}><a href="/index.php?call=runer.dpage">专题</a></li>
	<li {:if $_RUNTIME->ticket->action == 'widget':}class="current"{:/if:}><a href="/index.php?call=runer.widget">组件</a></li>
	<li {:if $_RUNTIME->ticket->action == 'source':}class="current"{:/if:}><a href="/index.php?call=runer.source">数据源</a></li>
	<li {:if $_RUNTIME->ticket->action == 'release':}class="current"{:/if:}><a href="/index.php?call=runer.release">发布页</a></li>
	<li {:if $_RUNTIME->ticket->action == 'hot':}class="current"{:/if:}><a href="/index.php?call=runer.hotsearch">热搜</a></li>
	<li {:if $_RUNTIME->ticket->action == 'spread':}class="current"{:/if:}><a href="/index.php?call=runer.spread">广告用户</a></li>
</ul>