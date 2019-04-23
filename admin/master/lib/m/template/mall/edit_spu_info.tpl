{:include file="_g/header.tpl" Title="库存/新增品类":}
<script type="text/javascript" src="/scripts/editor.js"></script>
<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/calendar.js"></script>
<style>
	.uu li{margin-top:10px;}
	.longinp{width:600px;}
</style>

<br />
<h1>SPU-编辑</h1>
<br />
<form name="form1" action="/index.php?call=mall.edit_ext_spu" method="post">
	<ul class="uu">
		<li>
			<input type="hidden" name="id" value="{:$res.id:}" />
			商品分类：<!--{:$catagory:}-->
			<select onchange="get_catagory1(this.value, this)" name="catagory_id" id="catagory_id">
				{:foreach from=$res5 item=c:}
				<option value="{:$c.id:}" {:if $res.catagory_id == $c.id:}selected="selected"{:/if:}>{:$c.name:}</option>
				{:/foreach:}
			</select>
		</li>
		<li>
			商品品牌：<select  name="brand_id"  id="brand_id">
				<option value ="0" >--请选择品牌--</option>
				{:foreach from=$brand value=br:}
				<option value ="{:$br.id:}" {:if $res.brand_id == $br.id:}selected="selected"{:else:}{:/if:}>{:$br.name:}</option>
				{:/foreach:}
			</select>
		</li>
		<li>
			商品标题：<input type="text" value="{:$res.title:}" name="title" readonly="readonly" />
		</li>
		<li>
			商品内容：<div id="spu_intro" name="spu_intro" style="width:670px;"></div>
			<script type="text/javascript">
				var e = new window.wangEditor('#spu_intro');
				e.create();
				e.txt.html('{:$res.intro | string.js_format:}');
			</script>
			<input type="hidden" id="intro" name="intro"/>
		</li>
		<li>
			视频地址：<input type="text" value="{:$res.video:}" name="video""/>
		</li>
		<li>
			商品条码：<input type="text" value="{:$res.barcode:}" name="barcode" readonly="readonly"/>
			货号：<input type="text" value="{:$res.serial:}" name="serial" readonly="readonly"/>
		</li>
		<li>
			单位重量：<input type="text" value="{:$res.weight:}" name="weight" />
			单位：<input type="text" value="{:$res.unit:}" name="unit" />
		</li>
		
		<li>
			上架排期																								
			<input type="text" id="online_time" name="online_time"  class="input-date" readonly="readonly" value="{:$res.online_time | number.datetime:}"/>-
			<input type="text" id="offline_time" name="offline_time"  class="input-date" readonly="readonly" value="{:$res.offline_time | number.datetime:}"/>
		</li>
		<li>
			<input type="button" value="保存" onclick="javascript:check1();"/>
		</li>
	</ul>
</form>
<br />
<b id="hidsid" style="display:none;">{:$res.supplier_id:}</b>
<span id="a1"></span>
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
	function get_catagory(id, obj) {
		var sid = $('#hidsid').html();
		console.log(obj.nextSibling);
		while (obj.nextSibling !== null) {
			obj.parentNode.removeChild(obj.nextSibling);
		}
		YueMi.API.Admin.invoke('depot', 'get_catagory2', {
			id: id,
			supplier_id: sid
		}, function (t, q, r) {
			if (r.Re !== '') {
				var newNode = document.createElement('select');
				newNode.setAttribute('onchange', 'get_catagory(this.value,this)');
				newNode.setAttribute('name', 'catagory_id');
				newNode.setAttribute('style', 'width:100px;background: white;');
				obj.removeAttribute('name');
				var str = '<option value="0">-- 请选择 --</option>';
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
	function get_catagory1(id, obj) {
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
</script>

