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
<form name="form1" action="/index.php?call=spu.edit_ext_spu" method="post">
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
			商品标题：<input type="text" value="{:$res.title:}" name="title" readonly="readonly" class="longinp" />
		</li>
		<li>
			<input type="hidden" value="0" id="tag" name="tag" />
			<label>规格：</label>
			<a onclick="javascript:reset()" style="color:red;">重新编辑</a>
			<a href="javascript:void(0);" title="添加规格" onclick="javascript:creat_specs();" id="aa" style="display: none;"  >
				<i class="fas fa-plus" ></i>
			</a>
		</li>
		<div id="guige" >
			{:foreach from=$specs value=v:}
			{:$v:}<br/>
			{:/foreach:}
		</div>
		<textarea rows="3" cols="20" id="specs" name="specs" style="display: none;">
			{:$Re.specs:}
		</textarea>
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
		<input type="hidden" name="ty" value="{:$ty:}"/>
</form>
<br />
<b id="hidsid" style="display:none;">{:$res.supplier_id:}</b>
<span id="a1"></span>
{:include file="_g/footer.tpl":}
<script>
	function check1()
	{
		//获得添加的规格
		var tag = $('#tag').val();
		if (tag > 0)
		{
			//修改规格
			var i = 1;
			var str = "";
			$("#guige > li > label").each(function () {
				if (i > 1)
				{
					var name = "\n" + this.innerHTML + ":";
				} else {
					var name = this.innerHTML + ":";
				}
				str += name;
				//获得添加的规格属性
				$("#guige > li:nth-child(" + i + ") input").each(function () {

					var shuxing = this.value + ",";
					str += shuxing;
				})
				i++;
			})

			//拆分，整理样式
			var strs = str.split("\n");
			var html = "";
			for (i = 0; i < strs.length; i++)
			{
				//去掉最后一个逗号
				if (i == 0)
				{
					html += strs[i].substring(0, strs[i].length - 1);
				} else {
					html += "\n" + strs[i].substring(0, strs[i].length - 1);
				}
			}

			$("#specs").val(html);
			//判断是否有规格

			var strings = $("#specs").val();
			if (strings.length > 0)
			{
				var nu = html.indexOf(":");
				if (nu == -1)
				{
					alert("请填写规格属性");
					exit;
				}
			}
			
			if(strings.indexOf("undefined") > 0)//索引位置，666  
			{  
				alert("商品规格错误，请重新选择");  
				exit;
			}
			

		}else{
			//这次没有修改规格，直接拿保存的
			var get_guige = $.trim($('#guige').text());
			$("#specs").val(get_guige);
		}
		
		
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
	
	
	//重选择规格
	function reset()
	{
		$('#guige').html("");
		$('#specs').html("");
		$('#tag').val('1');
		$('#aa').show();
		//select清空并且追加值清空
		$("input[name='big']").prop("checked", false);
	}

	function creat_specs()
	{
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '400px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fas fa-edit',
			title: '添加规格',
			content: '规格名称：<input type="text" class="input-text" id="dlg_input_specs" val="" size="40" /><br />' +
					"英文简称：<input type='text' onkeyup=\"value=value.replace(/[^a-zA-Z]/g,'' )\"  id='english' >",
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '添加',
					action: function () {
						var name = $("#dlg_input_specs").val();
						var en = $("#english").val();
						//<li>
						//	<label style='margin-left:60px;'>规格</label><a><i class="fas fa-plus"></i></a>
						//	<input type="text" id="title" name="title" value='属性值' style="width:200px;"/>
						//	<input type="text" id="title" name="title" value='属性值' style="width:200px;"/>
						//</li>
						var str = '';
						str += "<li>";
						str += "<label style='margin-left:60px;'>" + name + "</label>";
						str += "<a href='javascript:void(0);' title='添加属性' id='" + en + "'  onclick='javascript:shows(this.id);'><i class='fas fa-plus'></i></a>";
						str += "</li>";
						$("#guige").append(str);
					}
				},
				cancel: {text: '取消', btnClass: 'btn-blue', action: function () {}}
			}
		});
	}

	function shows(name)
	{
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '400px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fas fa-edit',
			title: '添加属性',
			content: '属性名称：<input type="text" class="input-text" id="shuxing" val="" size="40" /><br />' +
					"属性简称：<input type='text' onkeyup=\"value=value.replace(/[^a-zA-Z]/g,'' )\"  id='english' >",
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '添加',
					action: function () {
						var shuxing = $("#shuxing").val();
						var en = $("#english").val();
						var str = '';
						str += "<input id= '" + en + "'  value= '" + shuxing + "' style='margin-left:10px;'/>";
						//str += "<a id= '" + en + "' style='margin-left:20px;'>" + shuxing + "</a>";
						$("#" + name).after(str);
					}
				},
				cancel: {text: '取消', btnClass: 'btn-blue', action: function () {}}
			}
		});
	}
</script>

