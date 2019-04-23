{:include file="_g/header.tpl" Title="物流":}
<style>
	.uu li{
		float:left;
		margin-left:10px;
	}
	.hidediv{
		width:500px;
		height:300px;
		z-index:999;
		position:absolute;
		left:500px;
		height:200px;
		background-color:#fff;
		border:1px solid #000;
		display:none;
	}
</style>
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		{:if $type == 0:}订单管理{:elseif $type == 1:}新订单管理{:elseif $type == 2:}已支付订单管理{:elseif $type == 3:}已发货订单管理{:elseif $type == 4:}已签收订单管理
		{:elseif $type == 5:}退换货订单管理
		{:elseif $type == 6:}丢件订单管理{:elseif $type == 7:}已关闭订单管理{:elseif $type == 8:}已退款订单管理{:else:}--{:/if:}
	</caption>
	<ul class="uu">
		<li><a href="/index.php?call=order.logistics&type=0">全部订单</a></li>
		<li><a href="/index.php?call=order.logistics&type=1">新订单</a></li>
		<li><a href="/index.php?call=order.logistics&type=2">已支付</a></li>
		<li><a href="/index.php?call=order.logistics&type=3">已发货</a></li>
		<li><a href="/index.php?call=order.logistics&type=4">已签收</a></li>
		<li><a href="/index.php?call=order.logistics&type=5">退换货</a></li>
		<li><a href="/index.php?call=order.logistics&type=6">丢件</a></li>
		<li><a href="/index.php?call=order.logistics&type=7">已关闭</a></li>
		<li><a href="/index.php?call=order.logistics&type=8">已退款</a></li>
	</ul>
	{:if $type == 1 :}
	<tr>
		<th>id</th>
		<th>购买人</th>
		<th>邀请人</th>
		<th>裂变种子号</th>
		<th>是否主订单</th>
		<th>主订单号</th>
		<th>供应商</th>
		<th>货品总数量</th>
		<th>支付回单号</th>
		<th>支付时间</th>
		<th>联系人</th>
		<th>联系电话</th>
		<th>创建时间</th>
		<th>是否已确认</th>
		<th>确认收货时间</th>
		<th>订单状态</th>
		<th>订单更新时间</th>
		<th>操作</th>
	</tr>
	{:foreach from=$res->Data value=v:}
	<tr>
		<td><a href='/index.php?call=order.detail&id={:$v.id:}&type={:$type:}'>{:$v.id:}</a></td>
		<td>{:$v.buy_user:}</td>
		<td></td>
		<td></td><!--$v.inviter_feed-->
		<td>{:if $v.is_primary == 0:}否{:else:}是{:/if:}</td>
		<td><a href="/index.php?call=order.index&id={:$v.depend_id:}">{:$v.depend_id:}</a></td>
		<td>{:$v.supplier:}</td>
		<td>{:$v.qty:}</td>
		<td>{:$v.pay_serial:}</td>
		<td>{:$v.pay_time:}</td>
		<td>{:$v.addr_name:}</td>
		<td>{:$v.addr_mobile:}</td>
		<td>{:$v.create_time:}</td>
		<td>{:if $v.trans_fin == 0:}否{:else:}是{:/if:}</td>
		<td>{:$v.trans_time:}</td>
		<td>{:$v.status:}</td>
		<td>{:$v.update_time:}</td>
		<td>
			<a href='/index.php?call=order.detail&id={:$v.id:}&type={:$type:}'>详情</a>
			<a href="/index.php?call=order.off&id={:$v.id:}&type={:$type:}" style="color:red;">关闭</a>
		</td>
	</tr>
	{:/foreach:}

	<tr class="pager">
		<td colspan="20">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>

	{:elseif $type == 0 || $type == 7 ||$type == 8:}
	<tr>
		<th>id</th>
		<th>购买人</th>
		<th>邀请人</th>
		<th>裂变种子号</th>
		<th>是否主订单</th>
		<th>主订单号</th>
		<th>供应商</th>
		<th>货品总数量</th>
		<th>支付回单号</th>
		<th>支付时间</th>
		<th>联系人</th>
		<th>联系电话</th>
		<th>创建时间</th>
		<th>物流单号</th>
		<th>是否已确认</th>
		<th>确认收货时间</th>
		<th>订单状态</th>
		<th>订单更新时间</th>
		<th>操作</th>
	</tr>
	{:foreach from=$res->Data value=v:}
	<tr>
		<td><a href='/index.php?call=order.detail&id={:$v.id:}&type={:$type:}'>{:$v.id:}</a></td>
		<td>{:$v.buy_user:}</td>
		<td></td>
		<td></td><!--$v.inviter_feed-->
		<td>{:if $v.is_primary == 0:}否{:else:}是{:/if:}</td>
		<td><a href="/index.php?call=order.index&id={:$v.depend_id:}">{:$v.depend_id:}</a></td>
		<td>{:$v.supplier:}</td>
		<td>{:$v.qty:}</td>
		<td>{:$v.pay_serial:}</td>
		<td>{:$v.pay_time:}</td>
		<td>{:$v.addr_name:}</td>
		<td>{:$v.addr_mobile:}</td>
		<td>{:$v.create_time:}</td>
		<td>{:$v.trans_id:}</td>
		<td>{:if $v.trans_fin == 0:}否{:else:}是{:/if:}</td>
		<td>{:$v.trans_time:}</td>
		<td>{:$v.status:}</td>
		<td>{:$v.update_time:}</td>
		<td>
			<a href='/index.php?call=order.detail&id={:$v.id:}&type={:$type:}'>详情</a>
		</td>
	</tr>
	{:/foreach:}

	<tr class="pager">
		<td colspan="20">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>

	{:elseif $type == 2:}
	<tr>
		<th>id</th>
		<th>购买人</th>
		<th>邀请人</th>
		<th>是否主订单</th>
		<th>主订单号</th>
		<th>供应商</th>
		<th>货品总数量</th>
		<th>支付回单号</th>
		<th>支付时间</th>
		<th>联系人</th>
		<th>联系电话</th>
		<th>创建时间</th>
		<th>物流单号</th>
		<th>是否已确认</th>
		<th>确认收货时间</th>
		<th>订单状态</th>
		<th>订单更新时间</th>
		<th>地址</th>
		<th>操作</th>
			{:foreach from=$res->Data value=v:}
	<tr>
		<td><a href='/index.php?call=order.detail&id={:$v.id:}&type={:$type:}'>{:$v.id:}</a></td>
		<td>{:$v.buy_user:}</td>
		<td></td>
		<td>{:if $v.is_primary == 0:}否{:else:}是{:/if:}</td>
		<td><a href="/index.php?call=order.index&id={:$v.depend_id:}">{:$v.depend_id:}</a></td>
		<td>{:$v.supplier:}</td>
		<td>{:$v.qty:}</td>
		<td>{:$v.pay_serial:}</td>
		<td>{:$v.pay_time:}</td>
		<td>{:$v.addr_name:}</td>
		<td>{:$v.addr_mobile:}</td>
		<td>{:$v.create_time:}</td>
		<td>{:$v.trans_id:}</td>
		<td>{:if $v.trans_fin == 0:}否{:else:}是{:/if:}</td>
		<td>{:$v.trans_time:}</td>
		<td>{:$v.status:}</td>
		<td>{:$v.update_time:}</td>
		<td>{:$v.addr_detail:}</td>
		<td>
			<a href='/index.php?call=order.detail&id={:$v.id:}&type={:$type:}'>详情</a>
			<a href="/index.php?call=order.off&id={:$v.id:}&type={:$type:}&time=1" style="color:red;">关闭</a>
			<a href="/index.php?call=order.deliver&id={:$v.id:}&type={:$type:}">发货</a>
		</td>
	</tr>
	{:/foreach:}
	<tr class="pager">
		<td colspan="20">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>
	{:elseif $type == 3:}
	<tr>
		<th>id</th>
		<th>购买人</th>
		<th>邀请人</th>
		<th>裂变种子号</th>
		<th>是否主订单</th>
		<th>主订单号</th>
		<th>供应商</th>
		<th>货品总数量</th>
		<th>支付回单号</th>
		<th>支付时间</th>
		<th>联系人</th>
		<th>联系电话</th>
		<th>创建时间</th>
		<th>物流单号</th>
		<th>是否已确认</th>
		<th>确认收货时间</th>
		<th>订单状态</th>
		<th>订单更新时间</th>
		<th>操作</th>
	</tr>
	{:foreach from=$res->Data value=v:}
	<tr>
		<td><a href='/index.php?call=order.detail&id={:$v.id:}&type={:$type:}'>{:$v.id:}</a></td>
		<td>{:$v.buy_user:}</td>
		<td></td>
		<td></td><!--$v.inviter_feed-->
		<td>{:if $v.is_primary == 0:}否{:else:}是{:/if:}</td>
		<td><a href="/index.php?call=order.index&id={:$v.depend_id:}">{:$v.depend_id:}</a></td>
		<td>{:$v.supplier:}</td>
		<td>{:$v.qty:}</td>
		<td>{:$v.pay_serial:}</td>
		<td>{:$v.pay_time:}</td>
		<td>{:$v.addr_name:}</td>
		<td>{:$v.addr_mobile:}</td>
		<td>{:$v.create_time:}</td>
		<td><a onclick="javascript:update_logistics('{:$v.id:}',{:$v.trans_id:});">{:$v.trans_id:}</a></td>
		<td>{:if $v.trans_fin == 0:}否{:else:}是{:/if:}</td>
		<td>{:$v.trans_time:}</td>
		<td>{:$v.status:}</td>
		<td>{:$v.update_time:}</td>
		<td>
			<a href='/index.php?call=order.detail&id={:$v.id:}&type={:$type:}'>详情</a>
			<a onclick="javascript:select_logistics('{:$v.id:}');">查物流</a>
			<a onclick="javascript:update_logistics('{:$v.id:}',{:$v.trans_id:});">修改物流</a>
		</td>
	</tr>
	{:/foreach:}

	<tr class="pager">
		<td colspan="20">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>
	{:elseif $type == 4:}
	<tr>
		<th>id</th>
		<th>购买人</th>
		<th>签收人</th>
		<th>邀请人</th>
		<th>是否主订单</th>
		<th>主订单号</th>
		<th>供应商</th>
		<th>货品总数量</th>
		<th>支付回单号</th>
		<th>支付时间</th>
		<th>联系人</th>
		<th>联系电话</th>
		<th>创建时间</th>
		<th>是否已确认</th>
		<th>确认收货时间</th>
		<th>订单状态</th>
		<th>订单更新时间</th>
		<th>操作</th>
	</tr>
	{:foreach from=$res->Data value=v:}
	<tr>
		<td><a href='/index.php?call=order.detail&id={:$v.id:}&type={:$type:}'>{:$v.id:}</a></td>
		<td>{:$v.buy_user:}</td>
		<td></td>
		<td></td>
		<td>{:if $v.is_primary == 0:}否{:else:}是{:/if:}</td>
		<td><a href="/index.php?call=order.index&id={:$v.depend_id:}">{:$v.depend_id:}</a></td>
		<td>{:$v.supplier:}</td>
		<td>{:$v.qty:}</td>
		<td>{:$v.pay_serial:}</td>
		<td>{:$v.pay_time:}</td>
		<td>{:$v.addr_name:}</td>
		<td>{:$v.addr_mobile:}</td>
		<td>{:$v.create_time:}</td>
		<td>{:if $v.trans_fin == 0:}否{:else:}是{:/if:}</td>
		<td>{:$v.trans_time:}</td>
		<td>{:$v.status:}</td>
		<td>{:$v.update_time:}</td>
		<td>
			<a href='/index.php?call=order.detail&id={:$v.id:}&type={:$type:}'>详情</a>
		</td>
	</tr>
	{:/foreach:}

	<tr class="pager">
		<td colspan="20">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>
	{:elseif $type == 5:}
	<tr>
		<th>id</th>
		<th>购买人</th>
		<th>供应商</th>
		<th>货品总数量</th>
		<th>支付回单号</th>
		<th>支付时间</th>
		<th>联系人</th>
		<th>联系电话</th>
		<th>创建时间</th>
		<th>物流单号</th>
		<th>是否已确认</th>
		<th>确认收货时间</th>
		<th>订单状态</th>
		<th>订单更新时间</th>
		<th>退货原因</th>
		<th>操作</th>
	</tr>
	{:foreach from=$res->Data value=v:}
	<tr>
		<td><a href='/index.php?call=order.detail&id={:$v.id:}&type={:$type:}'>{:$v.id:}</a></td>
		<td>{:$v.buy_user:}</td>
		<td>{:$v.supplier:}</td>
		<td>{:$v.qty:}</td>
		<td>{:$v.pay_serial:}</td>
		<td>{:$v.pay_time:}</td>
		<td>{:$v.addr_name:}</td>
		<td>{:$v.addr_mobile:}</td>
		<td>{:$v.create_time:}</td>
		<td>{:$v.trans_id:}</td>
		<td>{:if $v.trans_fin == 0:}否{:else:}是{:/if:}</td>
		<td>{:$v.trans_time:}</td>
		<td>{:$v.status:}</td>
		<td>{:$v.update_time:}</td>
		<td>退货原因todo</td>
		<td>
			<a href='/index.php?call=order.detail&id={:$v.id:}&type={:$type:}'>详情</a>
			<a href="/index.php?call=order.afsinfo&id={:$v.id:}">售后记录</a>
		</td>
	</tr>
	{:/foreach:}

	<tr class="pager">
		<td colspan="20">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>
	{:elseif $type == 6:}
	<tr>
		<th>id</th>
		<th>购买人</th>
		<th>供应商</th>
		<th>货品总数量</th>
		<th>支付回单号</th>
		<th>支付时间</th>
		<th>联系人</th>
		<th>联系电话</th>
		<th>创建时间</th>
		<th>物流单号</th>
		<th>是否已确认</th>
		<th>确认收货时间</th>
		<th>订单状态</th>
		<th>订单更新时间</th>
		<th>丢件备用</th>
		<th>操作</th>
	</tr>
	{:foreach from=$res->Data value=v:}
	<tr>
		<td><a href='/index.php?call=order.detail&id={:$v.id:}&type={:$type:}'>{:$v.id:}</a></td>
		<td>{:$v.buy_user:}</td>
		<td>{:$v.supplier:}</td>
		<td>{:$v.qty:}</td>
		<td>{:$v.pay_serial:}</td>
		<td>{:$v.pay_time:}</td>
		<td>{:$v.addr_name:}</td>
		<td>{:$v.addr_mobile:}</td>
		<td>{:$v.create_time:}</td>
		<td>{:$v.trans_id:}</a></td>
		<td>{:if $v.trans_fin == 0:}否{:else:}是{:/if:}</td>
		<td>{:$v.trans_time:}</td>
		<td>{:$v.status:}</td>
		<td>丢件备用todo</td>
		<td>{:$v.update_time:}</td>
		<td>
			<a href='/index.php?call=order.detail&id={:$v.id:}&type={:$type:}'>详情</a>
		</td>
	</tr>
	{:/foreach:}

	<tr class="pager">
		<td colspan="20">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>
	{:else:}
	{:/if:}
</table>
<div class="hidediv">
	<center>
		<input type="hidden" id="hidid" value="" />
		<input type="text" value="" id="driveid" style="width:85%;height:50px;font-size:30px;margin-top:30px;" />
		<button style="width:300px;height:70px;font-size:30px;margin-top:30px;line-height:-30px;" id="changed">修改</button>
	</center>
</div>
<script>
	function update_logistics(id,driveid) {
		$('#hidid').val(id);
		$('#driveid').val(driveid);
		$('.hidediv').fadeIn();
	}
	$('#changed').click(function(){
		var id = $('#hidid').val();
		var val = $('#driveid').val();
		YueMi.API.Admin.invoke('order', 'change', {
			id: id,
			val:val
		}, function (t, q, r) {
			$('.hidediv').fadeOut();
			location.reload() 
		}, function (t, q, r) {
			
		});
	});
	function select_logistics(id){
		alert('我要查物流！'+id);
	}
</script>
{:include file="_g/footer.tpl":}
