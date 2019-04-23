<ul class="TabPages">
	<li {:if $_RUNTIME->ticket->action == 'index':}class="current"{:/if:}><a href="/index.php?call=user.index">用户列表</a></li>
	<li {:if $_RUNTIME->ticket->action == 'device':}class="current"{:/if:}><a href="/index.php?call=user.device">手机设备</a></li>
	<li {:if $_RUNTIME->ticket->action == 'wechat':}class="current"{:/if:}><a href="/index.php?call=user.wechat">微信授权</a></li> 
	<li {:if $_RUNTIME->ticket->action == 'cert':}class="current"{:/if:}><a href="/index.php?call=user.cert">实名认证</a></li>
	<li {:if $_RUNTIME->ticket->action == 'address':}class="current"{:/if:}><a href="/index.php?call=user.address">收货地址</a></li>
	<li {:if $_RUNTIME->ticket->action == 'bank':}class="current"{:/if:}><a href="/index.php?call=user.bank">银行卡</a></li>
	<li {:if $_RUNTIME->ticket->action == 'finance':}class="current"{:/if:}><a href="/index.php?call=user.finance">用户账目</a></li>
	<li {:if $_RUNTIME->ticket->action == 'invite':}class="current"{:/if:}><a href="/index.php?call=user.invite">邀请关系</a></li>
	<li {:if $_RUNTIME->ticket->action == 'vip':}class="current"{:/if:}><a href="/index.php?call=user.vip">VIP</a></li>
	<li {:if $_RUNTIME->ticket->action == 'template':}class="current"{:/if:}><a href="/index.php?call=user.template">邀请模板</a></li>
</ul>