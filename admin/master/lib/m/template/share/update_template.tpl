{:include file="_g/header.tpl" Title="系统/邀请模板":}
<ul class="Form" style="float:left;">
	<li>
		<label>模板名称：</label>{:$Tpl.name:} /
		底图尺寸 {:$Tpl.body_width:} × {:$Tpl.body_height:}
		<input type="button" class="button button-blue" value="保存设置" onclick="javascript:save_layout();" />
	</li>
	<li>
		<label for="code_enable">
			商品文案配置：
			<input type="checkbox" class="Toggle" value="1"  checked="checked" id="title_enable" />
		</label>
		x:
		<input type="number" class="input-number" id="title_x" value="{:$title[0]:}" min="0" max="{:$Tpl.body_height:}" step="1" />
		y:
		<input type="number" class="input-number" id="title_y" value="{:$title[1]:}" min="0" max="{:$Tpl.body_height:}" step="1" />
		w:
		<input type="number" class="input-number" id="title_ww" value="{:$title[2]:}" min="0" max="{:$Tpl.body_width:}" step="1" />
		h:
		<input type="number" class="input-number" id="title_wh" value="{:$title[3]:}" min="0" max="{:$Tpl.body_width:}" step="1" />
		length：
		<input type="number" class="input-number" id="title_l" value="{:$title[4]:}" min="0" max="{:$Tpl.body_width:}" step="1" />
		大小：
		<input type="number" class="input-number" id="title_size" value="{:$title[5]:}" min="0" max="{:$Tpl.body_width:}" step="1" />
		颜色
		<input type="text" class="input-color" id="title_color" value="{:$title[6]:}" />
	</li>

	<li>
		<label for="material_enable">
			商品素材配置：
			<!--<input type="checkbox" class="Toggle" value="1"  checked="checked" id="material_enable" />-->
		</label>
		显示位置：
		<input type="number" class="input-number" id="material_x" value="{:$material[1]:}" min="0" max="{:$Tpl.body_height:}" step="1" />,
		<input type="number" class="input-number" id="material_y" value="{:$material[2]:}" min="0" max="{:$Tpl.body_height:}" step="1" />,
		<input type="number" class="input-number" id="material_tw" value="{:$material[3]:}" min="0" max="{:$Tpl.body_width:}" step="1" />,
		<input type="number" class="input-number" id="material_th" value="{:$material[4]:}" min="0" max="{:$Tpl.body_width:}" step="1" />,
		padding：
		<input type="number" class="input-number" id="material_p" value="{:$material[5]:}" min="0" max="64" step="1" />
		<input type="hidden" id="num" value="{:$material[0]:}" />
	</li>
	<li>
		<label for="name_enable">
			显示昵称：
			<input type="checkbox" class="Toggle" name="name_enable" id="name_enable" value="1" {:if $name[0]:}checked="checked"{:/if:} />
		</label>

		显示位置：<input type="number" class="input-number" id="name_x" value="{:$name[1]:}" min="0" max="{:$Tpl.body_width:}" step="1" />，
		<input type="number" class="input-number" id="name_y" value="{:$name[2]:}" min="0" max="{:$Tpl.body_height:}" step="1" />
		文字大小：<input type="number" class="input-number" id="name_size" value="{:$name[3]:}" min="0" max="64" step="1" />
		文字颜色：<input type="text" class="input-color" id="name_color" value="{:$name[4]:}" />

	</li>
	<li>
		<label for="price_enable">
			平台价格： 
			<input type="checkbox" class="Toggle" name="price_enable" id="price_enable" value="1" {:if $price[0]:}checked="checked"{:/if:} />
		</label>

		显示位置：<input type="number" class="input-number" id="price_x" value="{:$price[1]:}" min="0" max="{:$Tpl.body_width:}" step="1" />，
		<input type="number" class="input-number" id="price_y" value="{:$price[2]:}" min="0" max="{:$Tpl.body_height:}" step="1" />
		文字大小：<input type="number" class="input-number" id="price_size" value="{:$price[3]:}" min="0" max="64" step="1" />
		文字颜色：<input type="text" class="input-color" id="price_color" value="{:$price[4]:}" />

	</li>
	<li>
		<label for="market_enable">
			参考价格：
			<input type="checkbox" class="Toggle" name="market_enable" id="market_enable" value="1" {:if $market[0]:}checked="checked"{:/if:} />
		</label>

		显示位置：<input type="number" class="input-number" id="market_x" value="{:$market[1]:}" min="0" max="{:$Tpl.body_width:}" step="1" />，
		<input type="number" class="input-number" id="market_y" value="{:$market[2]:}" min="0" max="{:$Tpl.body_height:}" step="1" />
		文字大小：<input type="number" class="input-number" id="market_size" value="{:$market[3]:}" min="0" max="64" step="1" />
		文字颜色：<input type="text" class="input-color" id="market_color" value="{:$market[4]:}" />

	</li>
	<li>
		<label for="avatar_enable">
			显示头像：
			<input type="checkbox" class="Toggle" name="avatar_enable" id="avatar_enable" value="1" {:if $avatar[0]:}checked="checked"{:/if:} />
		</label>

		显示位置：<input type="number" class="input-number" id="avatar_x" value="{:$avatar[1]:}" min="0" max="{:$Tpl.body_width:}" step="1" />，
		<input type="number" class="input-number" id="avatar_y" value="{:$avatar[2]:}" min="0" max="{:$Tpl.body_height:}" step="1" />
		贴图大小：
		<input type="number" class="input-number" id="avatar_w" value="{:$avatar[3]:}" min="32" max="256" step="1" />
		×
		<input type="number" class="input-number" id="avatar_h" value="{:$avatar[4]:}" min="32" max="256" step="1" />

	</li>
</ul>
<div style="width:{:$Tpl.body_width:}px;height:{:$Tpl.body_height:}px;position:relative;overflow:hidden;border-color:#888;border-style: solid;border-width: 2px 1px 1px 2px;float: left;clear:both;">
	<!--<img  src="{:#URL_RES:}/upload{:$Tpl.body_url:}" style="position: absolute;left: 0px;top: 0px;z-index: 0;width:{:$Tpl.body_width:}px;height:{:$Tpl.body_height:}px;" />-->
	<img  src="/images/z2a6jw9lgsc1.png" style="position: absolute;left: 0px;top: 0px;z-index: 0;width:{:$Tpl.body_width:}px;height:{:$Tpl.body_height:}px;" />
	<div id="prv_name" style="{:if $name[0] == 0:}display:none;{:/if:}position: absolute;z-index: 1;left: {:$name[1]:}px;top: {:$name[2]:}px;font-size: {:$name[3]:}px;color:{:$name[4]:};">User.name</div>
	<img id="prv_avatar" src="/images/avatar.jpg" style="{:if $avatar[0] == 0:}display:none;{:/if:}position:absolute;z-index: 5;left: {:$avatar[1]:}px;top:{:$avatar[2]:}px;width: {:$avatar[3]:}px;height: {:$avatar[4]:}px;" />
	<div id="prv_price" style="{:if $price[0] == 0:}display:none;{:/if:}position: absolute;z-index: 3;left: {:$price[1]:}px;top: {:$price[2]:}px;font-size: {:$price[3]:}px;color:{:$price[4]:};">￥99999999.00</div>
	<div id="prv_title"  style="position:absolute;z-index: 2;left: {:$title[0]:}px;font-weight:bold;top: {:$title[1]:}px;width: {:$title[2]:}px;height: {:$title[3]:}px;font-size:{:$title[5]:}px;color:{:$title[6]:};border:1px dotted black;overflow:hidden;" > 标题标题标题标题标题标题</div>
	<div id="prv_market"  style="{:if $market[0] == 0:}display:none;{:/if:}position: absolute;z-index: 3;left: {:$market[1]:}px;top: {:$market[2]:}px;font-size: {:$market[3]:}px;text-decoration:line-through;color:{:$market[4]:};">￥123456.00</div>


	<div id="prv_material"  style="position:absolute;border:1px solid red;z-index: 2;left: {:$material[1]:}px;top: {:$material[2]:}px;width: {:$material[3]:}px;height: {:$material[4]:}px;padding:{:$material[5]:}px;" >

		{:if $material[0] == 2:}

		<div id="prv2_material" class="material" style="border:1px solid #000;float:left;width:{:$material[3]/2-13:}px;height:{:$material[4]:}px;margin-right:3%;"></div>
		<div id="prv3_material" class="material" style="border:1px solid #000;float:left;width:{:$material[3]/2-13:}px;height:{:$material[4]:}px;"></div>
		{:/if:}
		{:if $material[0] == 3:}
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-13:}px;height:{:$material[4]:}px;margin-right:2%;"></div>
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-13:}px;height:{:$material[4]:}px;margin-right:2%;"></div>
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-13:}px;height:{:$material[4]:}px;"></div>
		{:/if:}
		{:if $material[0] == 4:}
		<div id="prv2_material" class="material" style="border:1px solid #000;float:left;width:{:$material[3]/2-13:}px;height:{:$material[4]/2-13:}px;margin-right:3%;"></div>
		<div id="prv3_material" class="material" style="border:1px solid #000;float:left;width:{:$material[3]/2-13:}px;height:{:$material[4]/2-13:}px;"></div>
		<div id="prv2_material" class="material" style="border:1px solid #000;float:left;width:{:$material[3]/2-13:}px;height:{:$material[4]/2-13:}px;margin-right:3%;margin-top:3%;"></div>
		<div id="prv3_material" class="material" style="border:1px solid #000;float:left;width:{:$material[3]/2-13:}px;height:{:$material[4]/2-13:}px;margin-top:3%;"></div>
		{:/if:}
		{:if $material[0] == 6:}
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-12:}px;height:{:$material[4]/2-11:}px;margin-right:2%;"></div>
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-12:}px;height:{:$material[4]/2-11:}px;margin-right:2%;"></div>
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-12:}px;height:{:$material[4]/2-11:}px;"></div>
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-12:}px;height:{:$material[4]/2-11:}px;margin-right:2%;margin-top:2%;"></div>
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-12:}px;height:{:$material[4]/2-11:}px;margin-right:2%;margin-top:2%;"></div>
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-12:}px;height:{:$material[4]/2-11:}px;margin-top:2%;"></div>

		{:/if:}
		{:if $material[0] == 9:}
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-12:}px;height:{:$material[4]/3-11:}px;margin-right:2%;"></div>
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-12:}px;height:{:$material[4]/3-11:}px;margin-right:2%;"></div>
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-12:}px;height:{:$material[4]/3-11:}px;"></div>
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-12:}px;height:{:$material[4]/3-11:}px;margin-right:2%;margin-top:3%;"></div>
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-12:}px;height:{:$material[4]/3-11:}px;margin-right:2%;margin-top:3%;"></div>
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-12:}px;height:{:$material[4]/3-11:}px;margin-top:3%;"></div>
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-13:}px;height:{:$material[4]/3-11:}px;margin-right:2%;margin-top:3%;"></div>
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-13:}px;height:{:$material[4]/3-11:}px;margin-right:2%;margin-top:3%;"></div>
		<div  class="material" style="border:1px solid #000;float:left;width:{:$material[3]/3-13:}px;height:{:$material[4]/3-11:}px;margin-top:3%;"></div>

		{:/if:}
	</div>


</div>
<script type="text/javascript">
	$(document).ready(function () {
		$('#prv_title').draggable({
			drag: function (e, ui) {
				$('#title_x').val(ui.position.left);
				$('#title_y').val(ui.position.top);
			}
		});
		$('#prv_avatar').draggable({
			drag: function (e, ui) {
				$('#avatar_x').val(ui.position.left);
				$('#avatar_y').val(ui.position.top);
			}
		});
		$('#prv_name').draggable({
			drag: function (e, ui) {
				$('#name_x').val(ui.position.left);
				$('#name_y').val(ui.position.top);
			}
		});
		$('#prv_price').draggable({
			drag: function (e, ui) {
				$('#price_x').val(ui.position.left);
				$('#price_y').val(ui.position.top);
			}
		});
		$('#prv_market').draggable({
			drag: function (e, ui) {
				$('#market_x').val(ui.position.left);
				$('#market_y').val(ui.position.top);
			}
		});
		$('#prv_material').draggable({
			drag: function (e, ui) {
				$('#material_x').val(ui.position.left);
				$('#material_y').val(ui.position.top);
			}
		});

		var x = document.getElementsByTagName('input');
		for (var i = 0; i < x.length; i++) {
			var a = x[i].id.split('_');
			var m = a[0];
			var f = a[1];
			if (a[1] === 'enable') {
				x[i].addEventListener('click', function () {
					var m = this.id.split('_')[0];
					var c = document.getElementsByTagName('input');
					for (var j = 0; j < c.length; j++) {
						var ca = c[j].id.split('_');
						if (ca[0] === m && ca[1] !== 'enable') {
							c[j].disabled = !this.checked;
						}
					}
					document.getElementById('prv_' + m).style.display = this.checked ? 'block' : 'none';
				}, false);
			} else if (a[1] === 'x') {
				x[i].addEventListener('input', function () {
					var m = this.id.split('_')[0];
					document.getElementById('prv_' + m).style.left = this.value.toString() + 'px';
				}, false);
			} else if (a[1] === 'y') {
				x[i].addEventListener('input', function () {
					var m = this.id.split('_')[0];
					document.getElementById('prv_' + m).style.top = this.value.toString() + 'px';
				}, false);
			} else if (a[1] === 'w') {
				x[i].addEventListener('input', function () {
					var m = this.id.split('_')[0];
					document.getElementById(m + '_h').value = this.value;
					document.getElementById('prv_' + m).style.width = this.value.toString() + 'px';
					document.getElementById('prv_' + m).style.height = this.value.toString() + 'px';
				}, false);
			} else if (a[1] === 'h') {
				x[i].addEventListener('input', function () {
					var m = this.id.split('_')[0];
					document.getElementById(m + '_w').value = this.value;
					document.getElementById('prv_' + m).style.height = this.value.toString() + 'px';
					document.getElementById('prv_' + m).style.width = this.value.toString() + 'px';
				}, false);
			} else if (a[1] === 'size') {
				x[i].addEventListener('input', function () {
					var m = this.id.split('_')[0];
					document.getElementById('prv_' + m).style.fontSize = this.value.toString() + 'px';
				}, false);
			} else if (a[1] === 'color') {
				x[i].addEventListener('change', function () {
					var m = this.id.split('_')[0];
					document.getElementById('prv_' + m).style.color = this.value.toString();
				}, false);
			} else if (a[1] === 'tw') {
				x[i].addEventListener('input', function () {
					var m = this.id.split('_')[0];
					var num = $('#num').val();
					var s = this.value.toString() / num - 13;
					
					if (num == 4) {
						var s = this.value.toString() / 2 - 13;
					}
					if (num == 6 || num == 9) {
						var s = this.value.toString() / 3 - 12;
					}
					$(".material").width(s);
					document.getElementById('prv_' + m).style.width = this.value.toString() + 'px';
				}, false);
			} else if (a[1] === 'th') {
				x[i].addEventListener('input', function () {
					var m = this.id.split('_')[0];
					var s = this.value.toString();
					var num = $('#num').val();
					if (num == 4) {
						var s = this.value.toString() / 2 - 13;
					}
					if (num == 6) {
						var s = this.value.toString() / 2 - 11;
					}
					if (num == 9) {
						var s = this.value.toString() / 3 - 11;
					}
					document.getElementById('prv_' + m).style.height = this.value.toString() + 'px';
					$(".material").height(s);
				}, false);
			} else if (a[1] === 'p') {
				x[i].addEventListener('input', function () {
					var m = this.id.split('_')[0];
					document.getElementById('prv_' + m).style.padding = this.value.toString() + 'px';
				}, false);
			} else if (a[1] === 'ww') {
				x[i].addEventListener('input', function () {
					var m = this.id.split('_')[0];
					document.getElementById('prv_' + m).style.width = this.value.toString() + 'px';
				}, false);
			} else if (a[1] === 'wh') {
				x[i].addEventListener('input', function () {
					var m = this.id.split('_')[0];
					document.getElementById('prv_' + m).style.height = this.value.toString() + 'px';
				}, false);
			}
		}
	});

	function save_layout() {
		YueMi.API.Admin.invoke('share', 'template_save', {
			id: {:$Tpl.id:},
			title_x: $('#title_x').val(),
			title_y: $('#title_y').val(),
			title_w: $('#title_ww').val(),
			title_h: $('#title_wh').val(),
			title_length: $('#title_l').val(),
			title_size: $('#title_size').val(),
			title_color: $('#title_color').val(),

			material_count: '{:$material[0]:}',
			material_x: $('#material_x').val(),
			material_y: $('#material_y').val(),
			material_w: $('#material_tw').val(),
			material_h: $('#material_th').val(),
			material_padding: $('#material_p').val(),

			name_open: $('#name_enable').is(':checked') ? 1 : 0,
			name_x: $('#name_x').val(),
			name_y: $('#name_y').val(),
			name_size: $('#name_size').val(),
			name_color: $('#name_color').val(),

			avatar_open: $('#avatar_enable').is(':checked') ? 1 : 0,
			avatar_x: $('#avatar_x').val(),
			avatar_y: $('#avatar_y').val(),
			avatar_w: $('#avatar_w').val(),
			avatar_h: $('#avatar_h').val(),

			price_open: $('#price_enable').val(),
			price_x: $('#price_x').val(),
			price_y: $('#price_y').val(),
			price_size: $('#price_size').val(),
			price_color: $('#price_color').val(),

			market_open: $('#market_enable').is(':checked') ? 1 : 0,
			market_x: $('#market_x').val(),
			market_y: $('#market_y').val(),
			market_size: $('#market_size').val(),
			market_color: $('#market_color').val(),
			__access_token : '{:$User->token:}'

		}, function (t, r, q) {
			location.reload();
		}, function (t, r, q) {
			alert(q.__message);
		});
	}
</script>
{:include file="_g/footer.tpl":}
