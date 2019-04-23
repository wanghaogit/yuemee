{:include file="_g/header.tpl" Title="订单":}

{:if isset($data.NeiGouInfo.order_id):}
	<div style="clear:both">&nbsp;</div>
	<table border="0" cellspacing="0" cellpadding="0" class="Grid">
		<caption>内购订单信息：</caption>
		<tr>
			<td>
				<!-- 内购订单总价：{:$data.NeiGouInfo.final_amount:} <br /> -->
				内购订单号：{:$data.NeiGouInfo.order_id:} <br />
				收货人信息：{:$data.NeiGouInfo.shipping.ship_addr:} {:$data.NeiGouInfo.shipping.ship_name:} {:$data.NeiGouInfo.shipping.ship_mobile:} <br />
				订单状态：{:if $data.NeiGouInfo.status == 1:}正常订单{:/if:}
					{:if $data.NeiGouInfo.status == 2:}取消订单{:/if:}
					{:if $data.NeiGouInfo.status == 3:}已完成{:/if:}
					<br  />
				确认收货：{:if $data.NeiGouInfo.confirm_status == 1:}未确认{:/if:}
					{:if $data.NeiGouInfo.confirm_status == 2:}已确认{:/if:}
					<br  />
				发货状态：{:if $data.NeiGouInfo.ship_status == 1:}未发货{:/if:}
					{:if $data.NeiGouInfo.ship_status == 2:}已发货{:/if:}
					{:if $data.NeiGouInfo.ship_status == 3:}已收货{:/if:}
					{:if $data.NeiGouInfo.ship_status == 4:}已退货{:/if:}
					<br  />
			</td>
		</tr>
	</table>
{:/if:}

<div style="clear:both">&nbsp;</div>
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>物流详情</caption>
	<tr>
		<td>
			物流公司：{:$data.trans_com:} <br />
			物流单号：{:$data.trans_id:} <br />
			物流详情：<br />
			{:$data.KuaiDi:}
		</td>
	</tr>
</table>

{:include file="_g/footer.tpl":}
