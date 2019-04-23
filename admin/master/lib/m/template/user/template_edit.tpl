{:include file="_g/header.tpl" Title="系统/邀请模板":}
<ul class="Form" style="float:left;">
	<li>
		<label>模板名称：</label>{:$Tpl->name:} /
		底图尺寸 {:$Tpl->body_width:} × {:$Tpl->body_height:}
		<input type="button" class="button button-blue" value="保存设置" onclick="javascript:save_layout();" />
	</li>
	<li>
		<label for="name_enable">
			显示昵称：
			<input type="checkbox" class="Toggle" name="name_enable" id="name_enable" value="1" {:if $Tpl->name_enable:}checked="checked"{:/if:} />
		</label>
		<span id="name_info">
			显示位置：<input type="number" class="input-number" id="name_x" value="{:$Tpl->name_x:}" min="0" max="{:$Tpl->body_width:}" step="1" />，
			<input type="number" class="input-number" id="name_y" value="{:$Tpl->name_y:}" min="0" max="{:$Tpl->body_height:}" step="1" />
			文字大小：<input type="number" class="input-number" id="name_size" value="{:$Tpl->name_size:}" min="0" max="64" step="1" />
			文字颜色：<input type="text" class="input-color" id="name_color" value="{:$Tpl->name_color:}" />
		</span>
	</li>
	<li>
		<label for="code_enable">
			显示代码：
			<input type="checkbox" class="Toggle" name="code_enable" id="code_enable" value="1" {:if $Tpl->code_enable:}checked="checked"{:/if:} />
		</label>
		<span id="code_info">
			显示位置：<input type="number" class="input-number" id="code_x" value="{:$Tpl->code_x:}" min="0" max="{:$Tpl->body_width:}" step="1" />，
			<input type="number" class="input-number" id="code_y" value="{:$Tpl->code_y:}" min="0" max="{:$Tpl->body_height:}" step="1" />
			文字大小：<input type="number" class="input-number" id="code_size" value="{:$Tpl->code_size:}" min="0" max="64" step="1" />
			文字颜色：<input type="text" class="input-color" id="code_color" value="{:$Tpl->code_color:}" />
		</span>
	</li>
	<li>
		<label for="avatar_enable">
			显示头像：
			<input type="checkbox" class="Toggle" name="avatar_enable" id="avatar_enable" value="1" {:if $Tpl->avatar_enable:}checked="checked"{:/if:} />
		</label>
		<span id="avatar_info">
			显示位置：<input type="number" class="input-number" id="avatar_x" value="{:$Tpl->avatar_x:}" min="0" max="{:$Tpl->body_width:}" step="1" />，
			<input type="number" class="input-number" id="avatar_y" value="{:$Tpl->avatar_y:}" min="0" max="{:$Tpl->body_height:}" step="1" />
			贴图大小：
			<input type="number" class="input-number" id="avatar_w" value="{:$Tpl->avatar_w:}" min="32" max="256" step="1" />
			×
			<input type="number" class="input-number" id="avatar_h" value="{:$Tpl->avatar_h:}" min="32" max="256" step="1" />
		</span>
	</li>
	<li>
		<label for="code_enable">
			显二维码：
			<input type="checkbox" class="Toggle" value="1"  checked="checked" id="qr_enable" />
		</label>
		显示位置：<input type="number" class="input-number" id="qr_x" value="{:$Tpl->qr_x:}" min="0" max="{:$Tpl->body_width:}" step="1" />，
		<input type="number" class="input-number" id="qr_y" value="{:$Tpl->qr_y:}" min="0" max="{:$Tpl->body_height:}" step="1" />
		贴图大小：
		<input type="number" class="input-number" id="qr_w" value="{:$Tpl->qr_w:}" min="64" max="256" step="1" />
		×
		<input type="number" class="input-number" id="qr_h" value="{:$Tpl->qr_h:}" min="64" max="256" step="1" />
	</li>
</ul>
<div style="width:{:$Tpl->body_width:}px;height:{:$Tpl->body_height:}px;position:relative;overflow:hidden;border-color:#888;border-style: solid;border-width: 2px 1px 1px 2px;float: left;clear:both;">
	<img id="prv_body" src="{:#URL_RES:}/upload{:$Tpl->body_url:}" style="position: absolute;left: 0px;top: 0px;z-index: 0;width:{:$Tpl->body_width:}px;height:{:$Tpl->body_height:}px;" />
	<div id="prv_name" style="{:if $Tpl->name_enable == 0:}display:none;{:/if:}position: absolute;z-index: 1;left: {:$Tpl->name_x:}px;top: {:$Tpl->name_y:}px;font-size: {:$Tpl->name_size:}px;color:{:$Tpl->name_color:};">{:$User->name:}</div>
	<img id="prv_avatar" src="/images/avatar.jpg" style="{:if $Tpl->avatar_enable == 0:}display:none;{:/if:}position:absolute;z-index: 2;left: {:$Tpl->avatar_x:}px;top: {:$Tpl->avatar_y:}px;width: {:$Tpl->avatar_w:}px;height: {:$Tpl->avatar_h:}px;" />
	<div id="prv_code" style="{:if $Tpl->code_enable == 0:}display:none;{:/if:}position: absolute;z-index: 3;left: {:$Tpl->code_x:}px;top: {:$Tpl->code_y:}px;font-size: {:$Tpl->code_size:}px;color:{:$Tpl->code_color:};">3f320f</div>
	<img id="prv_qr" src="/images/qr.png" style="position:absolute;z-index: 2;left: {:$Tpl->qr_x:}px;top: {:$Tpl->qr_y:}px;width: {:$Tpl->qr_w:}px;height: {:$Tpl->qr_h:}px;" />
</div>

<script type="text/javascript">
	$(document).ready(function () {
		$('#prv_qr').draggable({
			drag: function (e, ui) {
				$('#qr_x').val(ui.position.left);
				$('#qr_y').val(ui.position.top);
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
		$('#prv_code').draggable({
			drag: function (e, ui) {
				$('#code_x').val(ui.position.left);
				$('#code_y').val(ui.position.top);
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
			}
		}
	});

	function save_layout() {
		YueMi.API.Admin.invoke('user','layout_save',{
			id : {:$Tpl->id:},
			name_enable : $('#name_enable').is(':checked') ? 1 : 0,
			name_x : $('#name_x').val(),
			name_y : $('#name_y').val(),
			name_size : $('#name_size').val(),
			name_color : $('#name_color').val(),
			code_enable : $('#code_enable').is(':checked') ? 1 : 0,
			code_x : $('#code_x').val(),
			code_y : $('#code_y').val(),
			code_size : $('#code_size').val(),
			code_color : $('#code_color').val(),
			avatar_enable : $('#avatar_enable').is(':checked') ? 1 : 0,
			avatar_x : $('#avatar_x').val(),
			avatar_y : $('#avatar_y').val(),
			avatar_w : $('#avatar_w').val(),
			avatar_h : $('#avatar_h').val(),
			qr_x : $('#qr_x').val(),
			qr_y : $('#qr_y').val(),
			qr_w : $('#qr_w').val(),
			qr_h : $('#qr_h').val()
		},function(t,r,q){
			location.reload();
		},function(t,r,q){
			alert(q.__message);
		});
	}
</script>
{:include file="_g/footer.tpl":}
