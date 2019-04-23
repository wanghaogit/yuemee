{:include file="_g/header.tpl" Title="阅米微站":}
邀请入驻<br />
邀请码 = {:$_PARAMS.v:}<br />
微信授权码 = {:$_PARAMS.code:}<br />
微信状态码 = {:$_PARAMS.state:}<br />
{:if $Wechat:}
	微信：{:$Wechat->union_id:}<br />
{:/if:}
{:if $User:}
	昵称：{:$User->name:}<br />
	手机：{:$User->mobile:}<br />
{:else:}
	尚未绑定手机号。
{:/if:}

{:include file="_g/footer.tpl":}
