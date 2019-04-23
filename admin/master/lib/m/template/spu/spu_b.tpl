{:include file="_g/header.tpl" Title="库存/无效SPU":}
<link rel="stylesheet" type="text/css" href="/styles/material_manager.css" />
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<tr>
		<td>查询</td>
		<td colspan="9">
			<form action="/index.php?call=mall.spu" method="get">
				<input type="hidden" name="call" value="{:$_RUNTIME->ticket->handler:}.{:$_RUNTIME->ticket->action:}" />
				供应商
				<select id="supplier_serch" name="sid">
					<option value="0">请选择</option>
					{:foreach from=$Supplier value=v:}
						<option value="{:$v.id:}" {:if $_PARAMS.sid == $v.id:}selected="selected"{:/if:} >{:$v.name:}</option>
					{:/foreach:}
				</select>

				品牌：
				<select id="brand_serch" name="bid">
					<option value="0">请选择</option>
					{:foreach from=$Brand value=v:}
						<option value="{:$v.id:}"  {:if $_PARAMS.bid == $v.id:}selected="selected"{:/if:} >{:$v.name:}</option>
					{:/foreach:}
				</select>

				关键字：
				<input type="text" value="{:$_PARAMS.q:}" name="q">
				<input type="submit" value="搜索">
			</form>
		</td>
	</tr>
	<tr>
		<th>ID</th>
		<th>货号</th>
		<th>品类</th>
		<th>商品标题</th>
		<th>状态</th>
		<th>操作</th>
	</tr>
	{:foreach from=$data->Data value=v:}
		<tr>
			<td align="center">{:$v.id:}</td>
			<td align="center">{:$v.serial:}</td>
			<td align="center">{:$v.catagory_id | array.find $Catagory,'id','fullname','':}</td>
			<td>
				<!--<a href="/index.php?call=extsku.extsku&spu_spu={:$v.id:}" title="查看外部SKU列表" style="float:left;"><i class="fas fa-arrow-alt-circle-left"></i></a>-->
				<a href="/index.php?call=spu.spu_info&spu_id={:$v.id:}">{:$v.title | string.key_highlight $_PARAMS.q:}</a>
				<!--<a href="/index.php?call=sku.sku&spuid={:$v.id:}" title="查看SKU列表" style="float:right;"><i class="fas fa-arrow-alt-circle-right"></i></a>-->
			</td>
			<td align="center">
				{:echo 0 | boolean.iconic:}
			</td>
			<td>
				<a onclick="operate_material({:$v.id:})"><i class="fas fa-film"></i>素材管理</a>
				<a href="/index.php?call=spu.spu_info&spu_id={:$v.id:}">SPU详情</a>
				<a href="/index.php?call=spu.edit_spu_info&id={:$v.id:}&ty=b">编辑</a>
			</td>
		</tr>
	{:/foreach:}
	<tr class="pager" style="width: 100%;">
		<td colspan="11">
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
<script type="text/javascript">
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
			schema: 'spu',
			spu_id: id
		}, function (t, r, q) {
			get_material(id, 0);
		}, function (t, r, q) {
			alert(q.__message);
		});
		YueMi.Upload.Admin.create('compTest_1', {
			__access_token: '{:$User->token:}',
			width: 60,
			height: 30,
			boxWidth: '60px;',
			boxHeight: '60px;',
			schema: 'spu-p',
			spu_id: id
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
			schema: 'spu-b',
			spu_id: id
		}, function (t, r, q) {
			get_material(id, 2);
		}, function (t, r, q) {
			alert(q.__message);
		});
	}
	function close_material_div() {
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
					schema = 'spu';
				} else if (i == 1) {
					schema = 'spu-p';
				} else if (i == 2) {
					schema = 'spu-b';
				}
				YueMi.Upload.Admin.create('compTest_' + i, {
					__access_token: '{:$User->token:}',
					width: 60,
					height: 30,
					boxWidth: '60px;',
					boxHeight: '60px;',
					schema: schema,
					spu_id: id
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
		YueMi.API.Admin.invoke('mall', 'get_material_img', {
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
							YueMi.API.Admin.invoke('mall', 'remove_material', {
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
		YueMi.API.Admin.invoke('mall', 'set_default', {
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
</script>
<script>

</script>

{:include file="_g/footer.tpl":}
