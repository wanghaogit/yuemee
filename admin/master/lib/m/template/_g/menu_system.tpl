<ul class="TabPages">
	<li {:if $_RUNTIME->ticket->action == 'index':}class="current"{:/if:}><a href="/index.php?call=system.index">运行状态</a></li>
	<li {:if $_RUNTIME->ticket->action == 'mysql':}class="current"{:/if:}><a href="/index.php?call=system.mysql">MySql运行状态</a></li>
	<li {:if $_RUNTIME->ticket->action == 'config':}class="current"{:/if:}><a href="/index.php?call=system.config">系统配置</a></li>
	<li {:if $_RUNTIME->ticket->action == 'region':}class="current"{:/if:}><a href="/index.php?call=system.region">地区数据</a></li>
	<li {:if $_RUNTIME->ticket->action == 'applet':}class="current"{:/if:}><a href="/index.php?call=system.applet">接口权限</a></li>
	<li {:if $_RUNTIME->ticket->action == 'bank':}class="current"{:/if:}><a href="/index.php?call=system.bank">银行数据</a></li>
	<li {:if $_RUNTIME->ticket->action == 'rbac':}class="current"{:/if:}><a href="/index.php?call=system.rbac">权限管理</a></li>
	{:if $User->id == 1 || $User->id == 23:}
		<li {:if $_RUNTIME->ticket->action == 'sms':}class="current"{:/if:}><a href="/index.php?call=system.sms">短信记录</a></li>
		<li {:if $_RUNTIME->ticket->action == 'deluser':}class="current"{:/if:}><a href="/index.php?call=system.deluser">用户清理</a></li>
	{:/if:}
	<li {:if $_RUNTIME->ticket->action == 'admin':}class="current"{:/if:}><a href="/index.php?call=system.admin">管理员</a></li>
	<li {:if $_RUNTIME->ticket->action == 'rule':}class="current"{:/if:}><a href="/index.php?call=system.rule">规则管理</a></li>
	<li {:if $_RUNTIME->ticket->action == 'target':}class="current"{:/if:}><a href="/index.php?call=system.target">目标管理</a></li>
	<li {:if $_RUNTIME->ticket->action == 'teach_logs':}class="current"{:/if:}><a href="/index.php?call=system.teach_logs">技术日志</a></li>
	{:if $User->id == 1 || $User->id == 23 || $User->id == 709:}
		<li {:if $_RUNTIME->ticket->action == 'smssearch':}class="current"{:/if:}><a href="/index.php?call=system.smssearch">验证码查询</a></li>
	{:/if:}
	
</ul>