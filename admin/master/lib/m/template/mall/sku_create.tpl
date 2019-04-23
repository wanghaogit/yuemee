{:include file="_g/header.tpl" Title="库存/新增品类":}
<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/calendar.js"></script>
<script type="text/javascript" src="/scripts/editor.js"></script>
<style>
	.uu li{margin-top:10px;}
	.longinp{width:600px;}
	.price_give{background-color:#CCDDFF;width:120px;height:50px;font-size:14px;font-weight:bold;color:#444;}
	.price_no{background-color:#CCCCFF}
</style>
<br />
<h1>添加SKU记录</h1>
<br />
<form name="form1" action="/index.php?call=mall.sku_create" method="post">
	<ul class="uu">
		<li>
			商品分类：
			<select onchange="get_catagory(this.value, this)" name="catagory_id" id="catagory_id">
				<option value="0">--请选择分类--</option>
				{:foreach from=$res item=c:}
				<option value="{:$c.id:}">{:$c.name:}</option>
				{:/foreach:}
			</select>
		</li>
		<li>
			供应商：
			<select  name="supplier_id" id="supplier_id">
				<option value="0">--请选择供应商--</option>
				{:foreach from=$supplier value=su:}
				<option value ="{:$su.id:}" id="supplier_id">{:$su.name:}</option>
				{:/foreach:}
			</select>
		</li>
		<li>
			货品名称(SKU)：<input type="text" name="name" class="longinp"/>
		</li>
		<li>
			商品标题(SPU)：<input type="text" id="spu_id" name="spu_title" value="{:$spulist.title:} " readonly class="longinp" />
			<input type="hidden" name="spu_id" value=" {:$id:}" />
		</li>
		<li>
			SKU详情：<div id="spu_intro" name="spu_intro" style="width:670px;"></div>
			<script type="text/javascript">
				var e = new window.wangEditor('#spu_intro');
				e.create();
				e.txt.html('{:$spulist.intro:}');
			</script>
			<input type="hidden" id="intro" name="intro"/>
		</li>
		<li style="display:none;">
			视频地址：<input type="text" name="video" class="longinp" value="{:$spulist.video:}"/>
		</li>
		<li>
			商品条码：<input type="text" id="barcode" name="barcode" value="{:$spulist.barcode:}" />
			*货号：<input type="text" id="serial" name="serial"  value="{:$spulist.serial:}"/>
		</li>
		<li>
			单位重量：<input type="text" id="weight" name="weight" value="{:$spulist.weight:}" />
			*单位：<select name="unit">
				<option value="1">件</option>
			</select>
		</li>
		<li>
			库存数量：<input type="text" id="depot" name="depot" value="" />
		</li>
		<li>
			成本价格：<input type="text" id="price_base" name="price_base" class="price_give" value=""/>
			对标价：<input type="text" id="price_ref" name="price_ref" class="price_give" value="" />
			零售价：<input type="text" id="price_market" name="price_market" class="price_give" value="" />
			平台价：<input type="text" id="price_sale" name="price_sale" class="price_give" value="" />
			佣金：<input type="text" id="rebate_vip" name="rebate_vip" class="price_give price_no" value="" readyonly="true" />
		</li>
		<li>
			邀请价：<input type="text" name="price_inv" id="price_inv" class="price_give" value="">
			会员价：<input type="text" name="price_vip" id="price_vip" class="price_give" value="">
		</li>
		<li>
			购买者赠送阅币：<input type="text" name="coin_buyer" id="coin_buyer" value="">
			分享者赠送阅币：<input type="text" name="coin_inviter" id="coin_inviter" value="">
		</li>
		<li>
			<input type="button" value="保存" onclick="javascript:check1();">
		</li>
	</ul>
</form>

<div style="width: 400px;height: 500px;border: 0px solid #000;z-index: 999;position: absolute;left: 900px;top: 150px;">
	<ul class="TaskPanel">
		<li>价格参考</li>
		<li></li>
		<li>成本价：<input type="number" class="input-money" id="price_base2" value="" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
		<li>阅米价：<input type="number" class="input-money" id="price_sale2" value="" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
		<li>会员价：<input type="number" class="input-money" id="price_vip2" value="" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
		<li>邀请价：<input type="number" class="input-money" id="price_inv2" value="" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
		<li>对标价：<input type="number" class="input-money" id="price_ref2" value="" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
		<li>零售价：<input type="number" class="input-money" id="price_market2" value="" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
		<li>佣金额：<input type="number" class="input-money" id="rebate_vip2" value="" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
		<li>省钱额：<input type="number" class="input-money" id="rebate_poor2" value="" min="0.00" max="999999.99" step="0.01" style="width:80px;" /></li>
		<li></li>
	</ul>
</div>
<span id="type">{:$type:}</span>
{:include file="_g/footer.tpl":}
<script>
	function check1()
	{
		var html = e.txt.html();
		var infoobj = document.getElementById('intro');
		infoobj.value = html;
		var cb = $('#price_base').val().trim();
		cb = cb.length > 0 ? parseFloat(cb) : 0;
		var sc = $('#price_market').val().trim();
		sc = sc.length > 0 ? parseFloat(sc) : 0;
		var pt = $('#price_sale').val().trim();
		pt = pt.length > 0 ? parseFloat(pt) : 0;
		var cat = $('#catagory_id').val();
		cat = cat.length > 0 ? parseFloat(cat) : 0;
		var db = $('#price_ref').val();
		db = db.length > 0 ? parseFloat(db) : 0;
		
		var hy = $('#price_vip').val().trim();
		var yq = $('#price_inv').val().trim();
		if (hy < 0.1 || cb == '') {
			alert('请确认会员价');
			exit;
		}
		if (yq < 0.1 || cb == '') {
			alert('请确认邀请价');
			exit;
		}
		if (cb == '' || cb < 0.01) {
			alert('成本价输入有误');
			exit;
		}
		if (sc == '' || cb < 0.01) {
			alert('零售价输入有误');
			exit;
		}
		if (pt == '' || cb < 0.01) {
			alert('平台价输入有误');
			exit;
		}
		if (db == '' || cb < 0.01) {
			alert('对标价输入有误');
			exit;
		}
		if (sc <= cb) {
			alert('零售价必须大于成本价');
			exit;
		}
		
		var suid = $('#supplier_id').val();
		if (suid == 2) {
			var ml = (pt - cb) / cb;
			if (ml < 0.02) {
				alert('毛利必须大于2%');
				exit;
			}
		} else {
			var ml = (pt - cb) / cb;
			if (ml < 0.05) {
				alert('毛利必须大于5%');
				exit;
			}
		}
		if (suid == 0) {
			alert('请选择供应商');
			exit;
		}
		if (cat == 0) {
			alert('请选择分类');
			exit;
		}

		document.form1.submit();
	}
</script>


<script>
	$(function () {
		var obj = document.getElementsByTagName('select')[0];
		var id = obj.value;
		get_catagory(id, obj);
	})
	function get_catagory(id, obj) {
		console.log(obj.nextSibling);
		if (obj.nextSibling !== null) {
			obj.parentNode.removeChild(obj.nextSibling);
		}
		YueMi.API.Admin.invoke('depot', 'get_catagory', {
			id: id
		}, function (t, q, r) {
			if (r.Re !== '') {
				var newNode = document.createElement('select');
				newNode.setAttribute('onchange', 'get_catagory(this.value,this)');
				newNode.setAttribute('name', 'catagory_id');
				obj.removeAttribute('name');
				var str = '';
				$.each(r.Re, function (key, val) {
					str += '<option value="' + val.id + '">' + val.name + '</option>';
				});
				newNode.innerHTML = str;
				obj.parentNode.insertBefore(newNode, null);
			}
		}, function (t, q, r) {
			//失败
		});
	}
	
	
	//价格
	$(document).on('input', '.price_give', function () {

		var cb = $('#price_base').val().trim();
		var ym = $('#price_sale').val().trim();
		var hy = $('#price_vip').val().trim();
		var yq = $('#price_inv').val().trim();
		var db = $('#price_ref').val().trim();
		var sc = $('#price_market').val().trim();
		var yj = $('#rebate_vip').val().trim();

		var type = $('#type').html();

		if (type == 'JD') {
			var cb2 = cb;
			var ym2 = db - (db - cb) * 0.056;
			var hy2 = hy;
			var yq2 = yq;
			var db2 = db;
			var sc2 = db2 * 1.1;
			var yj2 = (db - cb) * 0.504;
			var sd2 = sc2 - ym2;
			$('#price_base2').val(cb2);
			$('#price_sale2').val(ym2);
			$('#price_vip2').val(hy2);
			$('#price_inv2').val(yq2);
			$('#price_ref2').val(db2);
			$('#price_market2').val(sc2);
			$('#rebate_vip2').val(yj2);
			$('#rebate_poor2').val(sd2);
			$('#rebate_vip').val(yj2);
		} else if (type == 'YX') {
			var cb2 = cb;
			var ym2 = db - (db - cb) * 0.056;
			var hy2 = hy;
			var yq2 = yq;
			var db2 = db;
			var sc2 = db * 1.1;
			var yj2 = (db2 - cb2) * 0.54;
			var sd2 = sc2 - ym2;
			$('#price_base2').val(cb2);
			$('#price_sale2').val(ym2);
			$('#price_vip2').val(hy2);
			$('#price_inv2').val(yq2);
			$('#price_ref2').val(db2);
			$('#price_market2').val(sc2);
			$('#rebate_vip2').val(yj2);
			$('#rebate_poor2').val(sd2);
			$('#rebate_vip').val(yj2);
		} else {
			var cb2 = cb;
			var ym2 = ym;
			var hy2 = hy;
			var yq2 = yq;
			var db2 = db;
			var sc2 = sc;
			var yj2 = 0;
			var sd2 = sc - ym;
			$('#price_base2').val(cb2);
			$('#price_sale2').val(ym2);
			$('#price_vip2').val(hy2);
			$('#price_inv2').val(yq2);
			$('#price_ref2').val(db2);
			$('#price_market2').val(sc2);
			$('#rebate_vip2').val(yj2);
			$('#rebate_poor2').val(sd2);
			$('#rebate_vip').val(yj2);
		}
	});


</script>