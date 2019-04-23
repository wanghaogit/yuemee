{:include file="_g/header.tpl" Title="运营/APP":}
<script type="text/javascript" src="/scripts/editor.js"></script>
<a href="/index.php?call=runer.source">
	返回
</a>
<form name="form1" action="/index.php?call=runer.source_create&block_id={:$block_id:}" method="post" id="form1">
	<ul class="Form">
		<li>
			<label>数据源名称：</label>
			<input type="text" id="name" name="name"  style="width:275px;"/>
		</li>
		<li>
			<label>数据源代号：</label>
			<input type="text" id="alias" name="alias"  style="width:275px;"/>
		</li>
		<li>
			<label>数据源类型：</label>
			<select name="style" id="sel_style" style="width:100px;background-color:#fff;height:27px" id="style" onchange="javascript:select_menu(parseInt(this.options[this.selectedIndex].value));">
				<option value="0">SQL</option>
				<option value="1">PHP</option>
				<option value="2">选定商品</option>
				<option value="3">商品筛选</option>
				<option value="4">选定专题</option>
			</select>
			<label>数据源格式：</label>
			<label for="sel_type_0"><input type="radio" name="type" id="sel_type_0" checked="checked" value="0"/>自定义</label>
			<label for="sel_type_1"><input type="radio" name="type" id="sel_type_1" value="1"/>单品</label>
			<label for="sel_type_2"><input type="radio" name="type" id="sel_type_2" value="2"/>多品</label>

			<input type="button" id="btn_select_sku" value="选择商品" style="display:none;" />
			<input type="button" id="btn_select_page" value="选择专题" style="display:none;" />
		</li>
		<li>
			<label>驱动代码</label>
		</li>
		<li>
			<ul id="list_selected_sku" style="display:none;">
				<li>已选择商品</li>
			</ul>
			<ul id="list_selected_page" style="display:none;">
				<li>已选择专题</li>
			</ul>
			<textarea id="driver" name="driver" cols="120" rows="20"></textarea>
		</li>
		<li>
			<input type="button" value="保存" onclick="javascript:check1();"  style="width: 100px;margin-top: 20px;"/>
		</li>
	</ul>
</form>
<style>
	.TEMP_Sku_Selector {
		width:100%;float: none;clear: both;
	}
	.TEMP_Sku_Selector > li {
		display: block;float:left;clear: both;
		width:100%;height:100px;
	}
	.TEMP_Sku_Selector > li > ul {
		
	}
	.TEMP_Sku_Selector > li > ul > li {
		display: inline-block;margin:1px;
		width:60px;height:60px;float:left;clear:none;
	}
	.TEMP_Page_Selector {
		width:100%;float: none;clear: both;
	}
	.TEMP_Page_Selector > li {
		display: block;float:left;clear: both;
		width:100%;height:100px;
	}
	.TEMP_Page_Selector > li > ul {
		
	}
	.TEMP_Page_Selector > li > ul > li {
		display: inline-block;margin:1px;
		width:60px;height:60px;float:left;clear:none;
	}
</style>
<script>
	function select_menu(id) {
		if (id === 0) {
			switch_to_sql_source();
		} else if (id === 1) {
			switch_to_php_source();
		} else if (id === 2) {
			do_select_sku();
		} else if (id === 3) {
			do_filter_sku();
		} else if (id === 4) {
			do_select_page();
		} else {
			switch_to_php_source();
		}
	}

	function switch_to_sql_source() {
		$('#driver').show();
		$('#btn_select_sku').hide();
		$('#btn_select_page').hide();
		$('#list_selected_sku').hide();
		$('#list_selected_page').hide();
	}
	function switch_to_php_source() {
		$('#driver').show();
		$('#btn_select_sku').hide();
		$('#btn_select_page').hide();
		$('#list_selected_sku').hide();
		$('#list_selected_page').hide();
	}
	var sku_page_id = 0;
	var sku_search_key = '';
	var page_page_id = 0;
	function ____generate_sku_list(self) {
		YueMi.API.Admin.invoke('runer', 'dlg_select_sku', {
			__access_token : '{:$User->token:}',
			page_id: sku_page_id,
			search: sku_search_key
		}, function (t, r, q) {
			var html = '<div>' +
					'查找：<input type="text" id="dlgsku_search" placeholder="商品标题关键字" class="input-text" value="' + sku_search_key + '" />' +
					'<input type="button" id="sku_button" value="查找"/>' +
					'<a href="javascript:void(0);" id="dlgsku_goto_prev">上一页(' + sku_page_id + ')</a>' +
					'<a href="javascript:void(0);" id="dlgsku_goto_next">下一页(' + sku_page_id + ')</a>' +
					'</div><ul class="TEMP_Sku_Selector">';
			$.each(q.List, function (k, v) {
				html += '<li>' +
							'<div>' + v.Title + '</div><ul>';
				$.each(v.Albumn,function(k1,v1){
					html += '<li><img src="' + v1.Picture + '" style="width:60px;height:60px;" data-title="' + v.Title + '" data-id="sku-' + v.Id + '-' + v1.Albumn + '-' + v1.Id + '" /></li>';
				});
				html += '</ul></li>';
			});
			html += '</ul>';
			self.setContent(html);

			$('.TEMP_Sku_Selector li ul li img').click(function () {
				var id = $(this).attr('data-id');
				var title = $(this).attr('data-title');
				var html = $('#driver').html();
				if (!html.indexOf(id)){
					alert('已经选择');
				} else {
					$('#driver').html(html + id + ',');
					var src = $(this).attr('src');
					var img = '';
					img = '<li><img src="' + src + '" width="80"/><span>'+title+'</span></li>';
					$('#list_selected_sku').append(img);
				}
				
			});
			$('#dlgsku_goto_next').click(function () {
				sku_page_id++;
				____generate_sku_list(self);
			});
			$('#dlgsku_goto_prev').click(function () {
				if (sku_page_id === 0){
					alert('已经是第一页');
				} else {
					sku_page_id--;
					____generate_sku_list(self);
				}
			});
			$('#sku_button').click(function () {
				sku_search_key = $('#dlgsku_search').val();
				____generate_sku_list(self);
			})
		}, function (t, r, q) {
			self.setContent('出错了，关闭对话框重新来一次吧。');
		});
	}
	function ____generate_page_list(self) {
		YueMi.API.Admin.invoke('runer', 'dlg_select_page', {
			__access_token : '{:$User->token:}',
			page_id: page_page_id
		}, function (t, r, q) {
			var html = '<div>' +
					'<a href="javascript:void(0);" id="dlgsku_goto_prev">上一页(' + sku_page_id + ')</a>' +
					'<a href="javascript:void(0);" id="dlgsku_goto_next">下一页(' + sku_page_id + ')</a>' +
					'</div><ul class="TEMP_Page_Selector">';
			$.each(q.List, function (k, v) {
				html += '<li>' +
							'<div>' + v.Title + '</div><ul  class="TEMP_Sku_Selector">';
				$.each(v.Albumn,function(k1,v1){
					html += '<li><img src="' + v1.Picture + '" style="width:100px;height:100px;" data-id="dpage-' + v.Id + '-' + v1.index + '" data-title="'+ v.Title + '"/></li>';
				});
				html += '</ul></li>';
			});
			html += '</ul>';
			self.setContent(html);

			$('.TEMP_Page_Selector li ul li img').click(function () {
				var id = $(this).attr('data-id');
				var html = $('#driver').html();
				if (!html.indexOf(id)){
					alert('已经选择');
				} else {
					$('#driver').html(html + id + ',');
					var title = $(this).attr('data-title');
					$('#list_selected_page').html(title);
					var src = $(this).attr('src');
					var img = '';
					img = '<li><img src="' + src + '" width="100"/><span>'+title+'</span></li>';
					$('#list_selected_page').append(img);
				}
				
			});
			$('#dlgsku_goto_next').click(function () {
				page_page_id++;
				____generate_page_list(self);
			});
			$('#dlgsku_goto_prev').click(function () {
				if (page_page_id === 0){
					alert('已经是第一页');
				} else {
					page_page_id--;
					____generate_page_list(self);
				}
			});
		}, function (t, r, q) {
			self.setContent('出错了，关闭对话框重新来一次吧。');
		});
	}
	function do_select_sku() {
		//$('#driver').hide();
		$('#btn_select_page').hide();
		$('#btn_select_sku').show();
		$('#list_selected_sku').show();
		$('#list_selected_page').hide();
	}
	$('#btn_select_sku').click(function () {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '600px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fas fa-shield',
			title: '选择商品',
			content: '正在加载...',
			onContentReady: function () {
				sku_page_id = 0;
				____generate_sku_list(this);
			},
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '选择',
					action: function () {

					}
				},
				cancel: {
					text: '取消', 
					btnClass: 'btn-blue', 
					action: function () { 
						$('#driver').html('');
						$('#list_selected_sku').html('<li>已选择商品</li>');
					}
				}
			}
		});
	});
	function do_filter_sku() {

	}
	function do_select_page() {
		//$('#driver').hide();
		$('#btn_select_sku').hide();
		$('#btn_select_page').show();
		$('#list_selected_sku').hide();
		$('#list_selected_page').show();
	}
	$('#btn_select_page').click(function () {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '600px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fas fa-shield',
			title: '选择专题',
			content: '正在加载...',
			onContentReady: function () {
				page_page_id = 0;
				____generate_page_list(this);
			},
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '选择',
					action: function () {

					}
				},
				cancel: {
					text: '取消', 
					btnClass: 'btn-blue', 
					action: function () {
						$('#driver').html('');
						$('#list_selected_page').html('<li>已选择专题</li>');
					}
				}
			}
		});
	});
	function check1(){
		$('#form1').submit();
	}
</script>
{:include file="_g/footer.tpl":}
