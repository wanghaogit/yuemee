{:include file="_g/header.tpl" Title="库存/新增品类":}
<script type="text/javascript" src="/scripts/editor.js"></script>
<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/calendar.js"></script>
<style>
	.uu li{margin-top:10px;}
	.longinp{width:600px;}
	.price_give{background-color:#CCDDFF;width:120px;height:50px;font-size:14px;font-weight:bold;color:#444;}
	.price_no{background-color:#CCCCFF}
</style>
<br />
<h1>SKU编辑</h1>
<br />
<form name="form1" action="/index.php?call=sku.edit_sku" method="post">
	<ul class="uu">
		<li>
			商品分类：<!--{:$catagory:}-->
			<select onchange="get_catagory1(this.value, this)"  id="catagory1">
				{:foreach from=$res item=c:}
				<option value="{:$c.id:}" {:if $catagory_pid== $c.id:}selected="selected"{:/if:}>{:$c.name:}</option>
				{:/foreach:}
			</select>
			<select onchange="get_catagory2(this.value, this)"  id="catagory2" class="cata2">
				{:foreach from=$child_list item=c:}
				<option value="{:$c.id:}" {:if $data['catagory_id'] == $c.id:}selected="selected"{:/if:}>{:$c.name:}</option>
				{:/foreach:}
			</select>
			<input type="hidden" name="catagory_id" id = "catagory_id" value="{:$data.catagory_id:}" />
		<li>
		<li>
			商品名称：<input type="text" name="name" value="{:$data.title:}" class="longinp">
		</li>
		<li>
			副标题：<input type="text" name="subtitle" value="{:$data.subtitle:}" class="longinp">
		</li>
		{:if $name > 0 :}
		<li id="guige">
			<label style="color: red;"> * </label><label>商品规格：</label>
			{:foreach from=$name key=key  value=val:}
			<input type="checkbox" name='big' value='{:$val:}' id='{:$key:}' onchange="javascript:show({:$key:})">{:$val:}
			{:/foreach:}
			<label style="color:red"><a onclick="javascript:reset();">重选</a></label>
		</li>
		<li>
			<span id="specs" name="specs" style="color:red;"></span>
		</li>
		<li>
			<label>当前规格：</label>
			{:$data.specs:}
		</li>
		{:/if:}
		<input type="hidden" name="sku_specs" id="sku_specs" value="{:$Re.specs:}"/>
		<li>
			SKU详情：<div id="spu_intro" name="spu_intro" style="width:670px;"></div>
			<script type="text/javascript">
				var e = new window.wangEditor('#spu_intro');
				e.create();
				e.txt.html('{:$data.intro | string.js_format:}');
			</script>
			<input type="hidden" id="intro" name="intro"/>
		</li>

		<li>
			商品条码：<input type="text" name="barcode" value="{:$data.barcode:}">
			货号：<input type="text" name="serial" value="{:$data.serial:}">
		</li>
		<li>
			库存数量：<input type="text" name="quantity" value="{:$data.depot:}">
		</li>
		<li>
			成本价：<input type="text" name="price_base" readonly="true" class="price_give price_no" id="price_base" value="{:$data.price_base:}">
			对标价：<input type="text" name="price_ref" readonly="true" class="price_give price_no" id="price_ref" value="{:$data.price_ref:}">
			佣金返利：<input type="text" name="rebate_vip" readonly="true" class="price_give price_no" id="rebate_vip" value="{:if $data.catagory_id == 701:}0{:else:}{:$data.rebate_vip:}{:/if:}">


		</li>
		<li>
			有邀请码价格：<input type="text" name="price_inv" class="price_give" id="price_inv" value="{:if $data.price_inv == 0:}{:$data.price_sale:}{:else:}{:$data.price_inv:}{:/if:}">
			零售价：<input type="text" name="price_market" class="price_give" id="price_market" value="{:$data.price_market:}">
			阅米价（无邀请码价格）：<input type="text" name="price_sale" class="price_give" id="price_sale" value="{:$data.price_sale:}">
		</li>
		<li>
			单位重量：<input type="text" id="weight" name="weight" value="{:$data.weight:}" />

			单位：<select id="unit" name="unit" style="width:100px;background-color:#fff;text-align: center">
				<option value="件" {:if $data.unit == '件':}selected="selected"{:/if:}>件</option>
				<option value="克" {:if $data.unit == '克':}selected="selected"{:/if:}>克</option>
				<option value="只" {:if $data.unit == '只':}selected="selected"{:/if:}>只</option>
				<option value="套" {:if $data.unit == '套':}selected="selected"{:/if:}>套</option>
				<option value="个" {:if $data.unit == '个':}selected="selected"{:/if:}>个</option>
				<option value="L" {:if $data.unit == 'L':}selected="selected"{:/if:}>L</option>
				<option value="ML" {:if $data.unit == 'ML':}selected="selected"{:/if:}>ML</option>
			</select>
		</li>
		<li>
			是否新人专享：<input type="checkbox" {:if $data.att_newbie == 1:}checked="checked"{:else:}{:/if:} class="Toggle" id="" name="att_newbie" value="" />
			是否包邮：<input type="checkbox" {:if $data.att_shipping == 0:}checked="checked"{:else:}{:/if:} class="Toggle" id="" name="att_shipping" value="" /><br/><br/>
			七天无理由退货：<input type="checkbox" {:if $data.att_refund == 1:}checked="checked"{:else:}{:/if:} class="Toggle" id="" name="att_refund" value="" />
			是否限购：<input type="checkbox" {:if $data.limit_style == 1:}checked="checked"{:else:}{:/if:} class="Toggle" id="limit_style" name="limit_style" value="" /><br/>
			是否大礼包：<input type="checkbox" {:if $big_libao == 1:}checked="checked"{:else:}{:/if:} class="Toggle" id="big_libao" name="big_libao" value="1" />

			<span {:if $data.limit_style == 1:}{:else:}style="display:none;"{:/if:} id="xgnum">限购数量：<input type="text" name="limit_size" value="{:$data.limit_size:}" id="limit_size" /></span>

		</li>
		<input type="hidden" value="{:$data.spu_id:}" id="spu_id" />
		<li>
			<input type="button" value="保存"  onclick="javascript:check1();">
		</li>
	</ul>
	<b id="hidsid" style="display:none;">{:$data.supplier_id:}</b>
	<input type="hidden" name="id" value="{:$data.id:}">
	<input type="hidden" name="page" value="{:$page:}"/>
	<input type='hidden' name='ty' value="{:$ty:}" />
</form>
<br />
<div style="width: 400px;height: 500px;border: 0px solid #000;z-index: 999;position: absolute;left: 900px;top: 150px;">
	<ul class="TaskPanel">
		<li>价格参考</li>
		<li></li>
		<li>成本价：<input type="number" class="input-money" id="price_base2" value="{:$price.cb:}" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
		<li>阅米价：<input type="number" class="input-money" id="price_sale2" value="{:$price.ym:}" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
		<li>邀请价：<input type="number" class="input-money" id="price_inv2" value="{:if $price.yq == 0:}{:$price.ym:}{:else:}{:$price.yq:}{:/if:}" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
		<li>对标价：<input type="number" class="input-money" id="price_ref2" value="{:$price.db:}" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
		<li>零售价：<input type="number" class="input-money" id="price_market2" value="{:$price.sc:}" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
		<li>佣金额：<input type="number" class="input-money" id="rebate_vip2" value="{:$price.yj:}" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
		<li>省钱额：<input type="number" class="input-money" id="rebate_poor2" value="{:$price.sd:}" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
		<li></li>
	</ul>
</div>

<span id="type">{:$type:}</span>
<span id="a1"></span>
{:include file="_g/footer.tpl":}
<script>
	$('#limit_style').click(function () {
		if ($(this).is(":checked")) {
			$('#xgnum').show();
		} else {
			$('#xgnum').hide();
		}
	});

	//显示规格
	function show(id) {
		//alert(id);
		if ($('#' + id).prop("checked")) {
			var spu_id = $('#spu_id').val();
			YueMi.API.Admin.invoke('spu', 'big', {
				__access_token: '{:$User->token:}',
				id: id,
				spu_id: spu_id
			}, function (t, q, r) {
				//追加li
				var str = '';
				str += "<li class='small' id=" + r.name + ">";
				str += "<label style=red;>*</label><label id='color'>" + r.name + "</label>";
				$(r.res).each(function (i, n) {
					//console.log(n);
					//下面两种方式实现的效果是一样的
					if (i == 0)
					{
						str += "<input type='radio' checked id='" + n + "' name = '" + r.name + "' value = '" + n + "' onchange='javascript:shows(this.value);'>" + n;
					} else {
						str += "<input type='radio' id='" + n + "' name = '" + r.name + "' value = '" + n + "' onchange='javascript:shows(this.value);'>" + n;
					}
				});
				str += "</li>";
				//$("#specs").append(r.name + ':');
				$("#guige").append(str);
			}, function (t, q, r) {
				//失败
			});
		} else {
			//取消选中操作
			alert("必须选择，不能取消");
			var state = $('#' + id).prop('checked');
			$('#' + id).prop('checked', !state);
		}
	}

	//追加显示
	function shows(name)
	{
		//$("#specs").append(name + "\r\n");
	}
	//重选择规格
	function reset()
	{
		$('#specs').html(" ");
		$('.small').html(" ");
		//select清空并且追加值清空
		$("input[name='big']").prop("checked", false);
	}

	function check1()
	{
		var html = e.txt.html();
		var infoobj = document.getElementById('intro');
		infoobj.value = html;

		//价格操作开始
		var cb = $('#price_base').val().trim();
		var ym = $('#price_sale').val().trim();
		var yq = $('#price_inv').val().trim();
		var db = $('#price_ref').val().trim();
		var sc = $('#price_market').val().trim();
		var yj = $('#rebate_vip').val().trim();
	
		if (cb < 0.1 || cb == '') {
			alert('请确认成本价');
			exit;
		}
		if (ym < 0.1 || cb == '') {
			alert('请确认阅米价');
			exit;
		}
		if (yq < 0.1 || cb == '') {
			alert('请确认邀请价');
			exit;
		}
		if (db < 0.1 || cb == '') {
			alert('请确认对标价');
			exit;
		}
		if (sc < 0.1 || cb == '') {
			alert('请确认零售价');
			exit;
		}

		if (sc - cb < 0) {
			alert('零售价过低');
			exit;
		}
		if ((ym - cb) / ym < 0.05) {
			alert('毛利过低');

		}

		//判断是否都选择上规格		
		var str = document.getElementsByName("big");
		var objarray = str.length;
		var chestr = "";
		for (i = 0; i < objarray; i++)
		{
			if (str[i].checked == false)
			{
				alert("请先选择复选框～！");
				exit;
			}
		}
		//获取规格和规格值
		$("#guige input[type='checkbox']").each(function () {
			var name = $(this).val();//复选框的值
			var aa = $("#" + $(this).val() + " input[type='radio']:checked").val();
			var guige = name + ":" + aa + "\n";
			//获取规格
			//alert(guige);
			//追加到隐藏域
			$("#specs").append(guige);
			//写入sku_specs隐藏域
			$("#sku_specs").append(guige);
		})
		var specs = $("#specs").text();		//获得所选值
		$("#sku_specs").val(specs);			//写入隐藏域
		
		if(specs.indexOf("undefined") > 0)//索引位置，666  
		{  
			alert("商品规格错误，请重新选择");  
			exit;
		}

		//价格操作结束
		document.form1.submit();
	}

	$(document).on('input', '.price_give', function () {

		var catagory = $('#catagory_id').val();

		var cb = $('#price_base').val().trim();
		var ym = $('#price_sale').val().trim();
		var yq = $('#price_inv').val().trim();
		var db = $('#price_ref').val().trim();
		var sc = $('#price_market').val().trim();
		var yj = $('#rebate_vip').val().trim();

		var type = $('#type').html();

		if (type == 'JDD') {
			var cb2 = cb;
			var ym2 = db - (db - cb) * 0.056;
			var yq2 = yq;
			var db2 = db;
			var sc2 = db2 * 1.1;
			var yj2 = (db - cb) * 0.504;
			if (catagory == '701') {
				yj2 = 0;
			}
			var sd2 = sc2 - ym2;
			$('#price_base2').val(cb2);
			$('#price_sale2').val(ym2);
			$('#price_inv2').val(yq2);
			$('#price_ref2').val(db2);
			$('#rebate_vip').val(yj2);
			$('#price_market2').val(sc2);
			$('#rebate_vip2').val(yj2);
			$('#rebate_poor2').val(sd2);
		} else if (type == 'JDG') {
			var cb2 = cb * 1.01;
			var ym2 = db - (db - cb * 1.01) * 0.056;
			var yq2 = yq;
			var db2 = db;
			var sc2 = db * 1.1;
			var yj2 = (db - cb * 1.01) * 0.504;
			if (catagory == '701') {
				yj2 = 0;
			}
			var sd2 = sc2 - ym2;

			$('#price_base2').val(cb2);
			$('#price_sale2').val(ym2);
			$('#price_inv2').val(yq2);
			$('#price_ref2').val(db2);
			$('#rebate_vip').val(yj2);
			$('#price_market2').val(sc2);
			$('#rebate_vip2').val(yj2);
			$('#rebate_poor2').val(sd2);
		} else if (type == 'YX') {
			var cb2 = cb;
			var ym2 = db - (db - cb) * 0.056;
			var yq2 = yq;
			var db2 = db;
			var sc2 = db * 1.1;
			var yj2 = (db2 - cb2) * 0.54;
			if (catagory == 701) {
				yj2 = 0;
			}
			var sd2 = sc2 - ym2;
			$('#price_base2').val(cb2);
			$('#price_sale2').val(ym2);
			$('#price_inv2').val(yq2);
			$('#price_ref2').val(db2);
			$('#rebate_vip').val(yj2);
			$('#price_market2').val(sc2);
			$('#rebate_vip2').val(yj2);
			$('#rebate_poor2').val(sd2);
		} else {
			var cb2 = cb;
			var ym2 = ym;
			var yq2 = yq;
			var db2 = db;
			var sc2 = sc;
			var yj2 = yj;

			var sd2 = sc - ym;
			var yj2 = (yq - cb) * 0.56;
			if (catagory == '701') {
				yj2 = 0;
			}
			$('#price_base2').val(cb2);
			$('#price_sale2').val(ym2);
			$('#rebate_vip').val(yj2);
			$('#price_inv2').val(yq2);
			$('#price_ref2').val(db2);
			$('#price_market2').val(sc2);
			$('#rebate_vip2').val(yj2);
			$('#rebate_poor2').val(sd2);
		}
	});

	$(".input-date").datetimepicker({
		autoclose: true,
		clearBtn: true,
		todayBtn: true,
		todayHighlight: true,
		fontAwesome: true,
		zIndex: 9999,
		format: 'yyyy-mm-dd hh:ii'
	});


	function get_catagory1(id, obj) {
		$('.cata2').remove();
		YueMi.API.Admin.invoke('depot', 'get_catagory', {
			__access_token: '{:$User->token:}',
			id: id
		}, function (t, q, r) {
			var str = '<select onchange="get_catagory2(this.value, this)"  id="catagory2" class="cata2">';
			$.each(r.Re, function (k, v) {
				str += '<option value="' + v.id + '">' + v.name + '</option>';
			});
			str += '</select>';
			$(obj).after(str);
			var val = r.Re[0]['id'];
			$('#catagory_id').val(val);
		}, function (t, q, r) {
			//失败
		});
	}

	function get_catagory2(id, obj) {
		var val = $(obj).val();
		$('#catagory_id').val(val);
	}

</script>

