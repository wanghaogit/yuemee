{:include file="_g/header.tpl" Title="总监":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		总监卡位订单
	</caption>
	<tr>
		<td>
			搜索
		</td>
		<td colspan="9">
			<form action="/index.php" method="GET">
				<input type="hidden" name="call" value="{:#Z_HANDLER:}.{:#Z_ACTION:}" />
				<input type="hidden" name="p" value="{:$_PARAMS.p:}" />
				昵称：<input type="text"  name="n" value="{:$_PARAMS.n:}" />
				手机：<input type="text"  name="m" value="{:$_PARAMS.m:}" />
				支付状态：<select name="status" style="background-color: #fff;width: 150px;height:25px;">
						<option value="0" {:if $_PARAMS.status == 0:}selected="selected"{:/if:}>全部</option>
						<option value="1" {:if $_PARAMS.status == 1:}selected="selected"{:/if:}>已关闭</option>
						<option value="2" {:if $_PARAMS.status == 2:}selected="selected"{:/if:}>待支付</option>
						<option value="3" {:if $_PARAMS.status == 3:}selected="selected"{:/if:}>已支付</option>
					</select>
				<input type="submit" value="搜索" />
			</form>
		</td>
	</tr>
	<tr>
		<th>id</th>
		<th>姓名</th>
		<th>手机</th>
		<th>支付渠道</th>
		<th>支付状态</th>
		<th>支付时间</th>
		<th>订单号</th>
		<th>支付金额</th>
		<th>创建时间</th>
		<th>操作</th>
	</tr>
	{:foreach from=$data->Data value=v:}
	<tr>
		<td>{:$v.id:}</td>
		<td>{:$v.name | string.key_highlight $_PARAMS.n:}</td>
		<td>{:$v.mobile | string.key_highlight $_PARAMS.m:}</td>
		<td>{:if $v.pay_channel == 0:}免费{:elseif $v.pay_channel == 1:}卡片{:elseif $v.pay_channel == 2:}线下{:elseif $v.pay_channel == 3:}微信{:elseif $v.pay_channel == 4:}支付宝{:else:}未知{:/if:}</td>
		<td>{:if $v.pay_status == 0:}已关闭{:elseif $v.pay_status == 1:}待支付{:else:}已支付{:/if:}</td>
		<td>{:$v.pay_time:}</td>
		<td>{:$v.order_id:}</td>
		<td>{:$v.money:}</td>
		<td>{:$v.create_time:}</td>
		<td>{:if $v.pay_status == 0:}{:elseif $v.pay_status == 1:}<a href="" onclick="javascript:_do_del({:$v.id:});">删除</a>{:else:}{:/if:}</td>
	</tr>
	{:/foreach:}
	<!--tr class="summary">
		<td>小计</td>
		<td>￥123.45</td>
		<td colspan="3"></td>
	</tr-->
	<tr class="pager">
		<td colspan="12">
			{:include file="_g/pager.tpl" Result=$data:}
		</td>
	</tr>
</table>
<script>
	function _do_del(id) {
		YueMi.API.Admin.invoke('order', 'delorderc', {
			__access_token: '{:$User->token:}',
			order_id : id
		}, function (t, r, q) {
			location.href = '/index.php?call=cheif.order';
		}, function (t, r, q) {
			alert(q.__message);
		});
	}
</script>
{:include file="_g/footer.tpl":}
