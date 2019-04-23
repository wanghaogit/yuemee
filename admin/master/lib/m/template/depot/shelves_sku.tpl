{:include file="_g/header.tpl" Title="库存/上架工具":}
<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/calendar.js"></script>
<script type="text/javascript" src="/scripts/editor.js"></script>
<style>
	.uu li{margin-top:10px;}
	.longinp{width:600px;}
</style>
<form name="form1" action="/index.php?call=depot.shelves_sku" method="post">
	<ul class="uu">
		<li>
			标题：
			<input type="text" class="longinp" name="title" id="title" maxlength="128" value="{:$res.name:}" />

		</li>
		<li>
			分类：
			<select onchange="get_catagory(this.value, this)" name="catagory_id" id="catagory_id">
					<option value="0">--请选择分类--</option>
					{:foreach from=$ress item=c:}
					<option value="{:$c.id:}">{:$c.name:}</option>
					{:/foreach:}
			</select>


		</li>
		<li>
			上架数量（库存）：
			<input type="number" class="input-number" id="rebate_user" name="qty_total" value="10" min="0" max="500" />
		</li>
		<li>
			成本价：
			<input id="price_base" type="text" name="price_base" value="{:$res.price_base:}">
		</li>
		<li>
			售卖价（默认售价）：<input type="text" name="price_sale" id="price_sale" value="{:$res.price_base:}">
			<span style="color:red; display:none;" id="message">售价过低</span>
			售卖价（对非VIP售价）：<input type="text" name="price_user" value="">
			售卖价（对孤单VIP售价）：<input type="text" name="price_vips" value="">
		</li>
		<li>
			售卖价(对受邀VIP售价)：<input type="text" name="price_vipi" value="">
			对标价(京东价/电商价)：<input type="text" name="price_ref" value="">
		</li>
		<li>
			用户返利金额：<input type="text" name="rebate_user" value="">
			VIP返利金额：<input type="text" name="rebate_vip" value="">
			平台返利金额：<input type="text" name="rebate_system" value="">
			总监返利金额：<input type="text" name="rebate_chief" value="">
			经理返利金额：<input type="text" name="rebate_director" value="">
		</li>
		<li>
			用户阅币：<input type="text" name="coin_user" value="">
			孤单VIP阅币：<input type="text" name="coin_vips" value="">
			受邀VIP阅币：<input type="text" name="coin_vipi" value="">
			邀请人阅币：<input type="text" name="coin_vipu" value="">
			总监阅币：<input type="text" name="coin_vipc" value="">
			经理阅币：<input type="text" name="coin_vipd" value="">
		</li>
		<li>
			是否要求VIP身份（是VIP才可以购买）：<input type="checkbox" class="Toggle" name="check_vip" value="" />
			是否检查邀请人（没有邀请人不可购买）：<input type="checkbox" class="Toggle" name="check_vipi" value="" />
			是否检查总监（必须有总监身份才可购买）：<input type="checkbox" class="Toggle" name="check_cheif" value="" />
			是否检查总经理（必须有总经理身份才可购买）：<input type="checkbox" class="Toggle" name="check_director" value="" />
		</li>
		<li>
			限购类型：<input type="radio" value="0" name="limit_style"/>不限购
			<input type="radio" value="1" name="limit_style"/>按人头限购
			<input type="radio" value="2" name="limit_style"/>按地址限购
			<input type="radio" value="3" name="limit_style"/>上架时间限购
			<input type="radio" value="4" name="limit_style"/>指定天数限购
		</li>
		<li>
			限购数量：<input type="text" name="limit_size" value="">
			限购天数：<input type="text" name="limit_days" value="">
			分类内部排序：<input type="text" name="p_order" value="">
		</li>
		<li>
			视频URL：<input type="text" name="video" value="" class="longinp">
		</li>
		<li>
			描述内容：<div id="spu_intro" name="spu_intro" style="width:670px;"></div>
			<script type="text/javascript">
				var e = new window.wangEditor('#spu_intro');
				e.create();
				e.txt.html('{:$data.intro:}');
			</script>
			<input type="hidden" id="intro" name="intro"/>
		</li>
		<li>
			预定上架时间：<input type="text" id="online_time" name="online_time"  class="input-date" readonly="readonly" value=""/>
			预定下架时间：<input type="text" id="offline_time" name="offline_time"  class="input-date" readonly="readonly" value=""/>
		</li>
		是否单独下单，有它必须单独下单：<input type="checkbox" class="Toggle" name="is_alone" value="" />
		<li>
			创建人：{:$User->name:}
		</li>
		<input type="hidden" name="create_user" value="{:$User->id:}">

		<input type="hidden" name="sku_id" value="{:$res.id:}">

		<li>
			<input type="button" value="上架" onclick="javascript:check1();" />
		</li>
	</ul>

</form>
{:include file="_g/footer.tpl":}
<script>
	function check1()
	{
		var html = e.txt.html();
		var infoobj = document.getElementById('intro');
		infoobj.value = html;
		document.form1.submit();
	}
	$(".input-date").datetimepicker({
		autoclose: true,
		clearBtn: true,
		todayBtn: true,
		todayHighlight: true,
		fontAwesome: true,
		zIndex: 9999,
		format: 'yyyy-mm-dd hh:ii'
	});
	$('#price_sale').blur(function () {
		var price_sale = $(this).val();
		var price_base = $('#price_base').val();
		if ((price_sale - price_base) <= 0) {
			$('#message').show();
		}
	}).focus(function () {
		$('#message').hide();
	});
</script>
<script>

</script>
