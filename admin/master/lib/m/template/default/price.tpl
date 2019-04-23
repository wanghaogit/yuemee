{:include file="_g/header.tpl" Title="首页":}
<script type="text/javascript" src="/scripts/editor.js"></script>
<span id="type">{:$type:}</span>
<ul class="TaskPanel">
	<li>价格计算器</li>
	<li></li>
	<li>成本价：<input type="number" class="input-money" id="price_base" value="{:$res.price_base:}" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
	<li>阅米价：<input type="number" class="input-money" id="price_sale" value="{:$res.price_sale:}" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
	<li>会员价：<input type="number" class="input-money" id="price_vip" value="{:$res.price_vip:}" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
	<li>邀请价：<input type="number" class="input-money" id="price_inv" value="{:$res.price_inv:}" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
	<li>对标价：<input type="number" class="input-money" id="price_ref" value="{:$res.price_ref:}" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
	<li>零售价：<input type="number" class="input-money" id="price_market" value="{:$res.price_market:}" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
	<li>佣金额：<input type="number" class="input-money" id="rebate_vip" value="{:$res.rebate_vip:}" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
	<li>省钱额：<input type="number" class="input-money" id="rebate_poor" value="{:$res.price_market - $res.price_sale:}" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
	<li></li>
</ul>
<button id="sub">判断</button>
<script type="text/javascript">
	var type = $('#type').html();
	$('#sub').click(function () {
		var cb = $('#price_base').val();
		var ym = $('#price_sale').val();
		var hy = $('#price_vip').val();
		var yq = $('#price_inv').val();
		var db = $('#price_ref').val();
		var sc = $('#price_market').val();
		var yj = $('#rebate_vip').val();
		var sq = $('#rebate_poor').val();
		if (type == 'JDD') {
			//京东低于20
			

		}
	});

</script>
{:include file="_g/footer.tpl":}
