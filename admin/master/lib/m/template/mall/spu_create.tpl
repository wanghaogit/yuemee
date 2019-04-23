{:include file="_g/header.tpl" Title="库存/新增品类":}
<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/calendar.js"></script>
<script type="text/javascript" src="/scripts/editor.js"></script>
<style>
	.uu li{margin-top:10px;}
	.longinp{width:600px;}
</style>
<br />
<h1>添加SPU记录</h1>
<br />
<form name="form1" action="/index.php?call=mall.spu_create" method="post">
	<ul class="uu">
		<li>
			所属分类：<select onchange="get_catagory(this.value, this)" name="catagory_id" id="catagory_id">
				<option value ="0" >--请选择分类--</option>
				{:foreach from=$res item=c:}
				<option value="{:$c.id:}">{:$c.name:}</option>
				{:/foreach:}
			</select>
		</li>
		<li>
			品&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;牌：	<select  name="brand_id"  id="brand_id">
				<option value ="0" >--请选择品牌--</option>
				{:foreach from=$brand value=br:}
				<option value ="{:$br.id:}">{:$br.name:}</option>
				{:/foreach:}
			</select>
		</li>
		<li>
			供应商： <select  name="supplier_id" id="supplier_id">
				<option value ="0" >--请选择供应商--</option>
				{:foreach from=$supplier value=su:}
				<option value ="{:$su.id:}" >{:$su.name:}</option>
				{:/foreach:}
			</select>
		</li>
		<li>
			商品标题：<input type="text" id="title" name="title" class="longinp" />
		</li>
		<li>
			商品内容：<div id="spu_intro" name="spu_intro" style="width:670px;"></div>
			<script type="text/javascript">
				var e = new window.wangEditor('#spu_intro');
				e.create();
			</script>
			<input type="hidden" id="intro" name="intro"/>
		</li>
		<li style="display:none;">
			视频地址：<input type="text" name="video" class="longinp"/>
		</li>
		<li>
			商品条码：<input type="text" id="barcode" name="barcode"  />
			货号：<input type="text" id="serial" name="serial"  />
			排序：<input type="text" id="p_order" name="p_order"  />
		</li>
	
		<li>
			单位重量：<input type="text" id="weight" name="weight" />
			<select name="unit">
				<option value="0">件</option>
			</select>
		</li>
		
		<li>
			上架排期：<input type="text" id="online_time" name="online_time"  class="input-date" readonly="readonly" value="{:$time:}"/>-<input type="text" id="offline_time" name="offline_time"  class="input-date" readonly="readonly" value="{:$time:}"/>
		</li>
		<li>
			<input type="button" onclick="javascript:check1();" value="保存">
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
</script>