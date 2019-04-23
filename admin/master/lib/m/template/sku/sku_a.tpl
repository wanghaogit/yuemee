{:include file="_g/header.tpl" Title="库存/在线SKU":}
<link rel="stylesheet" type="text/css" href="/styles/material_manager.css" />
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<tr>
		<td>
			查询 
		</td>
		<td colspan="13">
			<form action="/index.php?call=sku.sku_a" method="GET" name="form1">
				<input type="hidden" name="call" value="{:$_RUNTIME->ticket->handler:}.{:$_RUNTIME->ticket->action:}" />
				<input type="text" id='suname' value='' placeholder='搜索供应商' />
				供应商:<select id="supplier_serch" name="sid1">
					<option value="0">请选择</option>
					{:foreach from=$supplier value=v:}
					<option value="{:$v.id:}" {:if $_PARAMS.sid == $v.id:}selected="selected"{:/if:}>{:$v.name:}</option>
					{:/foreach:}
				</select>
				<input type="hidden" value="0" name="supplier" id="supplier" />
				<input type="hidden" name="sid" id="sid3"/>
				关键字：<input type="text" value="{:$_PARAMS.q:}" name="q" placeholder="请输入关键字">
				ID：<input type="text" value="{:$_PARAMS.skuid:}" name="skuid" onkeyup="value=value.replace(/[^\d]/g,'')" placeholder="请输入skuID">
				
				
				分类：
			</form>
		</td>
		<td>
			<input type="button" onclick="subsearch()" value="搜索" style="width:100%;height:100%;"/>
		</td>
	</tr>
	<tr>
		<th>ID</th>
		<th>分类</th>
		<th>标题</th>
		<th>副标题（推广）</th>
		<th>品牌 </th>
		<th>福利</th>
		<th>供应商</th>
		<th>条码</th>
		<th>重量</th>
		<th>单位</th>
		<th>库存</th>
		<th>价格</th>
		<th>利润</th>
		<th>赠送阅币</th>
		<th>操作</th>
	</tr>
	{:foreach from=$data->Data value=SKU:}
	<tr>
		<td>

			{:$SKU.id:}
		</td>
		<td>{:$SKU.catname:}</td>
		<td style="font-size:14px">
			<!--<a href="/index.php?call=spu.spu&spu_id={:$SKU.spu_id:}" title="查看SPU列表" style="float:left;"><i class="fas fa-arrow-alt-circle-left"></i></a>-->
			<a href="/index.php?call=sku.sku_detail&id={:$SKU.id:}">
				{:$SKU.title| string.key_highlight $_PARAMS.q:}
			</a>
			<p style="color:red;">
				{:if($SKU.att_newbie) == 1:}新人专享商品{:/if:}
			</p>
		</td>
		<td>{:$SKU.subtitle:}</td>
		<td></td>
		<td>
			{:if $SKU.att_shipping == 0:}包邮<br>{:else:}{:/if:}
			{:if $SKU.att_refund == 1:}7天无理由退换<br>{:else:}{:/if:}
			{:if $SKU.limit_style == 1:}限购&nbsp;&nbsp;&nbsp;限购数量：{:$SKU.limit_size:}<br>{:else:}{:/if:}
		</td>
		<td>{:$SKU.sname:}</td>
		<td>{:$SKU.barcode:}</td>
		<td>{:$SKU.weight:}</td>
		<td>{:$SKU.unit:}</td>
		<td>{:$SKU.depot:}</td>
		<td>
			<ul>
				<li>成本价：<span style="float:right;">{:$SKU.price_base | number.currency:}</span></li>
				<li>平台价：<span style="float:right;">{:$SKU.price_sale | number.currency:}</span></li>
				<li>对标价：<span style="float:right;">{:$SKU.price_ref | number.currency:}</span></li>
				<li>零售价：<span style="float:right;">{:$SKU.price_market | number.currency:}</span></li>
				<li>返佣：<span style="float:right;">{:$SKU.rebate_vip | number.currency:}</span></li>
				<li>有邀请码：<span style="float:right;">{:$SKU.price_inv | number.currency:}</span></li>
				<li>无邀请码：<span style="float:right;">{:$SKU.price_sale | number.currency:}</span></li>
			</ul>
		</td>
		<td >
			{: $SKU.price_sale - $SKU.price_base :}
		</td>
		<td>{:if $SKU.coin_buyer > 0 && $SKU.coin_inviter > 0:}
			<ul>
				{:if $SKU.coin_buyer > 0:}<li>购买者赠送阅币：<span style="float:right">{:$SKU.coin_buyer:}</span></li>{:/if:}
				{:if $SKU.coin_inviter > 0:}<li>邀请者赠送阅币：<span style="float:right">{:$SKU.coin_inviter:}</span></li>{:/if:}
			</ul>
			{:/if:}</td>
		<td class="operator">
			<a onclick="operate_material({:$SKU.id:})"><i class="fas fa-film"></i>素材管理</a>
			<a onclick="copy_to_sku({:$SKU.id:})"><i class="fas fa-film"></i>素材复制</a>
			<a href="/index.php?call=sku.material&sku_id={:$SKU.id:}&t=2">商品素材</a>
			<a href="/index.php?call=sku.material&sku_id={:$SKU.id:}&t=3">内容素材</a>
			<a href="/index.php?call=sku.update_sku&id={:$SKU.id:}&p={:$_PARAMS.p:}&ty=a">编辑详情</a>
			{:if $SKU.status == 0:}<a style="color:green;"  onclick="upsku({:$SKU.id:})">通过</a><a style="color:red;" onclick="getoutsku({:$SKU.id:})">驳回</a>
			{:elseif $SKU.status == 1:}
			{:elseif  $SKU.status == 2:}<a href="" onclick="downsku({:$SKU.id:})">下架</a>
			{:elseif  $SKU.status == 3:}
			{:elseif  $SKU.status == 4:}
			{:else:}
			{:/if:}
		</td>
	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="20">
			{:include file="_g/pager.tpl" Result=$data:}
		</td>
	</tr>
</table>

<div id="material_div" class="material_div" style="display:none">
	<div class="t_div">
		<div id="m_title_div">标题</div>
	</div>
	<div class="m_menu">
		<ul class="TabPages">
			<li id="m_main" onclick="javascript:change_content_div(0, this);" class="m_checked_menu">主图</li>
			<li id="m_loop" onclick="javascript:change_content_div(1, this);">内容</li>
			<li id="m_cont" onclick="javascript:change_content_div(2, this);">题图</li>
		</ul>
	</div>
	<div class="m_content_div" id="m_content_0">
		<div id="compTest_0" ></div>
	</div>
	<div class="m_content_div" id="m_content_1" style="display:none">
		<div id="compTest_1" ></div>
	</div>
	<div class="m_content_div" id="m_content_2" style="display:none">
		<div id="compTest_2" ></div>
	</div>
	<div class="b_div">
		<input type="button" value="关闭" onclick="close_material_div();"/>
	</div>
	<input type="hidden" value="0" id="u_m_sku_id"/>
</div>
<div id="big_img_div" style="display: none" class="big_img_div">
	<div style="width:100%;text-align: center;margin-top: 30px;">
		<img src="" id="__big_img" width="600" height="400"/>
	</div>

	<input type="button" value="关闭" onclick="close_big_img();" style="position: absolute;left: 50%;width:60px;margin-left: -30px;"/>
</div>
<script>
	function downsku(id) {
		YueMi.API.Admin.invoke('mall', 'downsku', {
			__access_token: '{:$User->token:}',
			id: id
		}, function (t, q, r) {
			location.reload();
		}, function (t, q, r) {
			alert('下架失败：' + r.__message);
		});
	}
	function upsku(id) {
		YueMi.API.Admin.invoke('mall', 'upsku', {
			__access_token: '{:$User->token:}',
			id: id
		}, function (t, q, r) {
			location.reload();
		}, function (t, q, r) {
			alert('上架失败：' + r.__message);
		});
	}
	function getoutsku(id) {
		YueMi.API.Admin.invoke('mall', 'getoutsku', {
			__access_token: '{:$User->token:}',
			id: id
		}, function (t, q, r) {
			location.reload();
		}, function (t, q, r) {
			alert('驳回失败：' + r.__message);
		});
	}
	$(function () {
		var obj = document.getElementsByTagName('select')[0];
		var id = obj.value;
		get_catagory(id, obj);
	})
	function get_catagory(id, obj) {
		//console.log(obj.nextSibling);
		if (obj.nextSibling !== null) {
			obj.parentNode.removeChild(obj.nextSibling);
		}
		YueMi.API.Admin.invoke('depot', 'get_catagory', {
			__access_token: '{:$User->token:}',
			id: id
		}, function (t, q, r) {
			if (r.Re !== '') {
				var newNode = document.createElement('select');
				newNode.setAttribute('onchange', 'get_catagory(this.value,this)');
				newNode.setAttribute('name', 'catagory_id');
				obj.removeAttribute('name');
				var str = '<option value="0">请选择</option>';
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

	function subsearch() {
		$('#sid3').val($('#supplier_serch').val());
		$("#tag").val('');
		document.form1.submit();
	}
	function dosubsearch() {
		$("#tag").val('tag');
		document.form1.submit();
	}
	function allsearch() {
		$("#tag").val('all');
		document.form1.submit();
	}

	function operate_material(id) {
		document.getElementById('material_div').style.display = "";
		get_material(id, 0);
		document.getElementById('u_m_sku_id').value = id;
		YueMi.Upload.Admin.create('compTest_0', {
			__access_token: '{:$User->token:}',
			width: 60,
			height: 30,
			boxWidth: '60px;',
			boxHeight: '60px;',
			schema: 'sku',
			sku_id: id
		}, function (t, r, q) {
			get_material(id, 0);
		}, function (t, r, q) {
			alert(q.__message);
		});
		YueMi.Upload.Admin.create('compTest_1', {
			width: 60,
			height: 30,
			boxWidth: '60px;',
			boxHeight: '60px;',
			schema: 'sku-p',
			sku_id: id
		}, function (t, r, q) {
			get_material(id, 1);
		}, function (t, r, q) {
			alert(q.__message);
		});
		YueMi.Upload.Admin.create('compTest_2', {
			__access_token: '{:$User->token:}',
			width: 60,
			height: 30,
			boxWidth: '60px;',
			boxHeight: '60px;',
			schema: 'sku-b',
			sku_id: id
		}, function (t, r, q) {
			location.reload();
			get_material(id, 2);
		}, function (t, r, q) {
			alert(q.__message);
		});
	}
	function close_material_div() {
		location.reload();
		document.getElementById('material_div').style.display = "none";
		$('#__ul_img').remove();
	}
	function change_content_div(n, obj) {
		var divobj = document.getElementsByClassName('m_content_div');
		var liobj = document.getElementsByClassName('m_menu');
		var id = document.getElementById('u_m_sku_id').value;
		document.getElementsByClassName('m_checked_menu')[0].setAttribute('class', '');
		obj.setAttribute('class', 'm_checked_menu');
		for (var i = 0; i < 3; i++) {
			if (i == n) {
				divobj[n].style.display = "";
				get_material(id, n);
				divobj[i].innerHTML = "";
				divobj[i].innerHTML = "<div id='compTest_" + i + "' ></div>";
				var schema = '';
				if (i == 0) {
					schema = 'sku';
				} else if (i == 1) {
					schema = 'sku-p';
				} else if (i == 2) {
					schema = 'sku-b';
				}
				YueMi.Upload.Admin.create('compTest_' + i, {
					__access_token: '{:$User->token:}',
					width: 60,
					height: 30,
					boxWidth: '60px;',
					boxHeight: '60px;',
					schema: schema,
					sku_id: id
				}, function (t, r, q) {
					get_material(id, n);
				}, function (t, r, q) {
					alert(q.__message);
				});
			} else {
				divobj[i].style.display = "none";
				divobj[i].innerHTML = "";
				divobj[i].innerHTML = "<div id='compTest_" + i + "' ></div>";
			}
		}
	}
	function get_material(id, type) {
		YueMi.API.Admin.invoke('mall', 'sku_get_material_img', {
			__access_token: '{:$User->token:}',
			id: id,
			type: type
		}, function (t, q, r) {
			if (r.Re !== '') {
				document.getElementById("m_title_div").innerHTML = "";
				document.getElementById("m_title_div").innerHTML = r.Title;
				var ulobj = document.getElementById("__ul_img");
				if (ulobj !== null) {
					$('#__ul_img').remove();
				}
				var str = '';
				str += "<ul id='__ul_img'>";
				$.each(r.data, function (key, val) {
					var classstyle = '';
					if (val.IsDefault == 1) {
						classstyle = 'default_img';
					}
					str += "<li  data-picture='" + val.Picture + "' style='height:100px;' class='" + classstyle + "'>";
					str += "<div><img src='https://r.yuemee.com/upload" + val.Picture + "'width='80'height='80' onclick='get_big_img(this.parentNode.parentNode)'/><div>";
					if (type !== 1) {
						str += "<div><a href='javascript:;' onclick='remove_img(" + val.Id + "," + type + ")'>删除</a> | ";
						str += "<a href='javascript:;' onclick='default_img(" + val.Id + "," + type + ")'>设为默认</a></div>";
					}
					str += "</li>";
				});
				str += "</ul>";
				$('#m_content_' + type).append(str);


			}
		}, function (t, q, r) {
			//失败
		});
	}
	function get_big_img(obj) {
		document.getElementById('big_img_div').style.display = "";
		var src = obj.getAttribute('data-picture');
		document.getElementById('__big_img').setAttribute('src', 'https://r.yuemee.com/upload' + src);
	}
	function close_big_img() {
		document.getElementById('big_img_div').style.display = "none";
		document.getElementById('__big_img').setAttribute('src', '');
	}
	function remove_img(id, type) {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '300px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fa fa-shield',
			title: '删除品类',
			content: '删除吗？',
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '删除',
					action: function () {
						var imgobj = $('#__ul_img').children();
						if (imgobj.length > 1) {
							var spuid = document.getElementById('u_m_sku_id').value;
							YueMi.API.Admin.invoke('mall', 'sku_remove_material', {
								__access_token: '{:$User->token:}',
								id: id
							}, function (t, q, r) {
								if (r.Re !== '') {
									get_material(spuid, type);
								}
							}, function (t, q, r) {
								//失败
							});
						} else {
							alert('不可全部删除');
						}
					}
				},
				cancel: {text: '取消', btnClass: 'btn-blue', action: function () {}}
			}
		});

	}
	function default_img(id, type) {
		var spuid = document.getElementById('u_m_sku_id').value;
		YueMi.API.Admin.invoke('mall', 'sku_set_default', {
			__access_token: '{:$User->token:}',
			id: spuid,
			type: type,
			mid: id
		}, function (t, q, r) {
			if (r.Re !== '') {
				get_material(spuid, type);
			}
		}, function (t, q, r) {
			//失败
		});
	}
	function copy_to_sku(sku_id) {
		YueMi.API.Admin.invoke('mall', 'copy_to_sku', {
			__access_token: '{:$User->token:}',
			sku_id: sku_id
		}, function (t, q, r) {
			alert(r.__message);
		}, function (t, q, r) {
			//失败
		});
	}
	
	//搜索供应商
	$("#suname").blur(function(){
		var name = $('#suname').val();
		YueMi.API.Admin.invoke('mall', 'search_supplier', {
			__access_token: '{:$User->token:}',
			name: name
		}, function (t, q, r) {
			//循环option，写入select
			var str = '';
			$.each(r.List, function (key, val) {
				str += '<option value="' + val.id + '">' + val.name + '</option>';
			});
			$('#supplier_serch').html(str);
		}, function (t, q, r) {
			//失败
		});
	});
</script>
{:include file="_g/footer.tpl":}