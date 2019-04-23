{:include file="_g/header.tpl" Title="订单":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		售后记录
	</caption>
	<tr>
		<th>id</th>
		<th>用户</th>
		<th>商品</th>
		<th>素材</th>
		<th>订单号</th>
		<th>订单详情号</th>
		<th>供应商</th>
		<th>退货数量</th>
		<th>退货价格</th>
		<th>退货总价</th>
		<th>申请方式</th>
		<th>申请理由</th>
		<th>申请退款金额</th>
		<th>申请消息</th>
		<th>退货物流</th>
		<th>实际退款</th>
		<th>订单状态</th>
		<th>创建时间</th>
		<th>更新时间</th>
		<th>操作</th>
	</tr>
	{:foreach from=$res->Data value=v:}
	<tr>
		<td>{:$v.id:}</td>
		<td>{:$v.username:}</td>
		<td>{:$v.title:}</td>
		<td><img src="{:#URL_RES:}/upload{:$v.picture:}" style="width:50px;height:50px;" /></td>
		<td>{:$v.order_id:}</td>
		<td>{:$v.item_id:}</td>
		<td>{:$v.supplier:}</td>
		<td>{:$v.qty:}</td>
		<td>{:$v.price:}</td>
		<td>{:$v.total:}</td>
		<td>{:if $v.req_type == 1:}退货退款{:elseif $v.req_type == 2:}补发{:elseif $v.req_type == 3:}部分退款{:elseif $v.req_type == 4:}全额退款{:else:}其他{:/if:}</td>
		<td>{:$v.req_reason:}</td>
		<td>{:$v.req_money:}</td>
		<td>{:$v.req_message:}</td>
		<td>{:$v.req_trans:}</td>
		<td>{:$v.bak_money:}</td>
		<td>{:if $v.status == 3:}完成{:elseif $v.status == 0:}申请{:elseif $v.status == 1:}拒绝{:elseif $v.status == 2:}通过{:else:}未知{:/if:}</td>
		<td>{:$v.create_time:}</td>
		<td>{:$v.update_time:}</td>
		<td>
			{:if $v.req_type == 1:}<a>退款</a>{:elseif $v.req_type == 2:}<a href="/index.php?call=order.regive&id={:$v.id:}">补发</a>{:elseif $v.req_type == 3:}<a>退款</a>{:elseif $v.req_type == 4:}<a>全额退款</a>{:else:}{:/if:}
		</td>
	</tr>
	{:/foreach:}
	<tr class="pager">
		<td colspan="20">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>

</table>

{:include file="_g/footer.tpl":}
