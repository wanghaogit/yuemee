{:include file="_g/header.tpl" Title="阅米微站":}
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
