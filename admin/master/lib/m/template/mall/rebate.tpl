{:include file="_g/header.tpl" Title="库存/品类":}
<table cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		返利管理
	</caption>
	<tr>
		<th colspan="3">ID</th>
		<th rowspan="2">商品</th>
		<th colspan="2">用户</th>
		<th colspan="3">金额</th>
		<th colspan="4">支付</th>
		<th colspan="6">佣金</th>
		<th colspan="2">系统参数</th>
	</tr>
	<tr>
	
		<th>订单</th>
		<th>明细</th>
		<th>购买者</th>
		
		
		<th>分享ID</th>
		<th>分享者ID</th>
		
		<th>购买数量</th>
		<th>支付总额</th>
		<th>支付余额</th>
		
		<th>在线支付</th>
		<th>支付余额</th>
		<th>佣金</th>
		<th>券</th>
		
		<th>总可支配佣金</th>
		<th>平台返利金额</th>
		<th>自己返利金额</th>
		<th>分享返利金额</th>
		<th>总监返利金额</th>
		<th>经理返利金额</th>
		
		<th>创建时间</th>
		<th>状态</th>
	</tr>
	{:foreach from=$data->Data item=S:}
		<tr>
		
			<td>{:$S.order_id:}</td>
			<td>{:$S.item_id:}</td>
			<td>{:$S.buyer_id:}</td>
			
			<td>{:$S.name_1:} </td>
			
			<td>{:$S.share_id:}</td>
			<td>{:$S.share_user_id:}</td>
			
			<td>{:$S.pay_count:}</td>
			<td>{:$S.pay_total:}</td>
			<td>{:$S.pay_money:}</td>
			
	
			<td>{:$S.pay_online:}</td>
			<td>{:$S.pay_money:}</td>
			<td>{:$S.pay_profit:}</td>
			<td>{:$S.pay_ticket:}</td>
			
			<td>{:$S.total_profit:}</td>
			<td>{:$S.system_profit:}</td>
			
			<td>{:$S.self_profit:}</td>
			<td>{:$S.share_profit:}</td>
			
			<td>{:$S.cheif_profit:}</td>
			<td>{:$S.director_profit:}</td>

			<td>{:$S.create_time | number.datetime:}</td>
			<td>{:$S.status | array.enum ['待确认','已取消','已确认','已结算']:}</td>
		</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="29">{:include file="_g/pager.tpl" Result=$data:}</td>
	</tr>
</table>
<script type="text/javasctipt">

</script>{:include file="_g/footer.tpl":}
