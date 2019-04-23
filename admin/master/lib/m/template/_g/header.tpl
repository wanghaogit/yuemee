<!DOCTYPE html>
<html>
<head>
		<title>阅米后台 - {:$Title:}</title>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport' />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="/favicon.ico" rel="icon" type="image/x-icon" />
		<link rel="stylesheet" type="text/css" href="{:#URL_RES:}/v1/styles/jquery-ui.css" />
		<link rel="stylesheet" type="text/css" href="{:#URL_RES:}/v1/styles/ziima.a.css" />
		<link rel="stylesheet" type="text/css" href="{:#URL_RES:}/v1/styles/awesome.css" />
		<link rel="stylesheet" type="text/css" href="{:#URL_RES:}/v1/styles/dialog.css" />
		<link rel="stylesheet" type="text/css" href="/styles/admin.css" />
		<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/jquery.js"></script>
		<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/jquery-ui.js"></script>
		<script type="text/javascript" src="/scripts/ziima.js"></script>
		<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/dialog.js"></script>
		<script type="text/javascript" src="/scripts/api.js"></script>
		<style type="text/css">
			
		</style>
		<script type="text/javascript">
			
		</script>
	</head>
	<body>
		<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/calendar.js"></script>
		<div class="page_container">
			<div class="page_menu">
				<div class="page_logo">
					<img src="/images/logo.png" />
					<span>阅米总后台</span>
				</div>
				<ul class="MainMenu">
					<li {:if $_RUNTIME->ticket->handler == 'default':}class="current"{:/if:}><a href="/index.php?call=default.index">首页</a></li>
					<li {:if $_RUNTIME->ticket->handler == 'user':}class="current"{:/if:}><a href="/index.php?call=user.index">用户</a></li>
					<li {:if $_RUNTIME->ticket->handler == 'cheif':}class="current"{:/if:}><a href="/index.php?call=cheif.index">总监</a></li>
					<li {:if $_RUNTIME->ticket->handler == 'director':}class="current"{:/if:}><a href="/index.php?call=director.index">经理</a></li>
					<li {:if $_RUNTIME->ticket->handler == 'depot':}class="current"{:/if:}><a href="/index.php?call=depot.index">库存</a></li>
					<li {:if $_RUNTIME->ticket->handler == 'extspu':}class="current"{:/if:}><a href="/index.php?call=extspu.index">SPU(外)</a></li>
					<li {:if $_RUNTIME->ticket->handler == 'extsku':}class="current"{:/if:}><a href="/index.php?call=extsku.index">SKU(外)</a></li>
					<li {:if $_RUNTIME->ticket->handler == 'spu':}class="current"{:/if:}><a href="/index.php?call=spu.index">SPU(内)</a></li>
					<li {:if $_RUNTIME->ticket->handler == 'sku':}class="current"{:/if:}><a href="/index.php?call=sku.index">SKU(内)</a></li>
					<li {:if $_RUNTIME->ticket->handler == 'mall':}class="current"{:/if:}><a href="/index.php?call=mall.index">商城</a></li>
					<li {:if $_RUNTIME->ticket->handler == 'runer':}class="current"{:/if:}><a href="/index.php?call=runer.index">运营</a></li>
					<li {:if $_RUNTIME->ticket->handler == 'share':}class="current"{:/if:}><a href="/index.php?call=share.index">分享</a></li>
					<li {:if $_RUNTIME->ticket->handler == 'order':}class="current"{:/if:}><a href="/index.php?call=order.index">订单</a></li>
					<li {:if $_RUNTIME->ticket->handler == 'finance':}class="current"{:/if:}><a href="/index.php?call=finance.index">财务</a></li>
					<li {:if $_RUNTIME->ticket->handler == 'report':}class="current"{:/if:}><a href="/index.php?call=report.index">报表</a></li>
					<li {:if $_RUNTIME->ticket->handler == 'notify':}class="current"{:/if:}><a href="/index.php?call=notify.index">通知</a></li>
					<li {:if $_RUNTIME->ticket->handler == 'cms':}class="current"{:/if:}><a href="/index.php?call=cms.index">资讯</a></li>
					<li {:if $_RUNTIME->ticket->handler == 'system':}class="current"{:/if:}><a href="/index.php?call=system.index">系统</a></li>
				</ul>
			</div>
			<div class="page_body">
				{:include dynamic='_g/menu_' + $_RUNTIME->ticket->handler + '.tpl':}
