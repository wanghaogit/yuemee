{:include file="_g/header.tpl" Title="用户":}
<link rel="stylesheet" type="text/css" href="/styles/user/usershow.css" />
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>自发礼包列表</caption>
	<tr>
		<td>搜索</td>
		<td colspan="20">
			<form method="GET" action="/index.php">
				<input type="hidden" name="call" value="{:#Z_HANDLER:}.{:#Z_ACTION:}" />
				<input type="hidden" name="p" value="{:$_PARAMS.p:}" />
			总经理电话：
			<input type="text" name="m" value="{:$_PARAMS.m:}" />
			<input type="submit" value="搜索"/>
			</form>
		</td>
	</tr>
	<tr>
		<th>订单号</th>
		<th>商品名</th>
		<th>图</th>
		<th>购买数量</th>
		<th>购买者</th>
		<th>购买者身份</th>
		<th>快递公司</th>
		<th>快递单号</th>
		<th>收货人</th>
		<th>收货人电话</th>
		<th>收货地址</th>
		<th>创建时间</th>
		<th>备注</th>
		<th>归属团队(总监)</th>
		<th>操作</th>
		
	</tr>
	
	{:foreach from=$res->Data value=v:}
		<tr>
			<td>{:$v.order_id:}</td>
			<td style="width:200px;">{:$v.title:}</td>
			<td><img src="https://r.yuemee.com/upload{:$v.pic:}" style="width:90px;height:90px;"/></td>
			<td>{:$v.qty:}</td>
			<td>{:$v.buyname:}</td>
			<td>{:if $v.body == 'v':}VIP{:elseif $v.body == 'c':}总监{:elseif $v.body == 'd':}总经理{:elseif $v.body == 'u':}普通用户{:else:}未知{:/if:}</td>
			<td>{:$v.kuaidi_name:}</td>
			<td>{:$v.trans_id:}</td>
			<td>{:$v.addr_name:}</td>
			<td>{:$v.addr_mobile:}</td>
			<td>{:$v.province:}-{:$v.city:}-{:$v.country:}<br>{:$v.addr_detail:}</td>
			<td>{:$v.create_time | number.datetime:}</td>
			<td>{:$v.comment_user:}</td>
			<td>VIP:{:$v.vname:}<br>总监：{:$v.cname:}<br>总经理：{:$v.dname:}</td>
			<td></td>
		</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="16">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>
</table>
<script>

</script>
{:include file="_g/footer.tpl":}
