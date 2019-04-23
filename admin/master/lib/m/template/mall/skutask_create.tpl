{:include file="_g/header.tpl" Title="库存/SKU":}

<form name="form1" action="/index.php?call=mall.skutask_create" method="post" id="form1">
	<ul class="Form">
		<li>
			<label>请选择供应商</label>
			<input type="text" id='suname' value='' placeholder='搜索供应商' />
			‍‍<select id="sid" onchange="gradeChange()">
				<option id="0" value="0">--请先选择供应商--</option>
				{:foreach from=$List value=list:}
				<option id="{:$list.id:}" value="{:$list.name:}">{:$list.name:}</option>
				{:/foreach:}
			</select>
			<input type="hidden" value="0" name="supplier" id="supplier" />
		</li>
		<li>
			<label>请选择商品：</label>
			<a href="javascript:void(0);"  title="选择商品"
			   onclick="javascript:select_sku();">
				<i class="fas fa-plus-square" ></i>
			</a>
			<input type="hidden" id="sku_id" name="sku_id" />
			<input type="text" id="name" name="name"  readonly="readonly" style="width:275px;"/>
		</li>
		<li>
			<label>开始时间</label>
			<input type="text" id="online_time" name="online_time"  class="input-date"  value=""/>
		</li>
		<li>
			<label>结束时间</label>
			<input type="text" id="offline_time" name="offline_time"  class="input-date" readonly="readonly" value=""/>
		</li>
		<li>
			<label>是否改变标题</label>
			<input type="checkbox" name="title" id="title" class="Toggle" value="0" /><br/>

		</li>
		<li class="title" style="display: none;margin-left:50px;">
			<label>开始时间标题</label>
			<input type="text" id="start_name" name="start_name"  style="width:275px;"/>
			<label>结束时间标题</label>
			<input type="text" id="end_name" name="end_name"  style="width:275px;"/>
		</li>

		<li>
			<label>是否改变子标题</label>
			<input type="checkbox" name="subtitle" id="subtitle" class="Toggle" value="0" />
		</li>
		<li class="subtitle" style="display: none;margin-left:50px;">
			<label>开始时间子标题</label>
			<input type="text" id="start_subtitle" name="start_subtitle"  style="width:275px;"/>
			<label>结束时间子标题</label>
			<input type="text" id="end_subtitle" name="end_subtitle"  style="width:275px;"/>
		</li>


		<li>
			<label>是否改变平台价</label>
			<input type="checkbox" name="price" id="price" class="Toggle" value="0" />
		</li>
		<li class="price" style="display: none;margin-left:50px;">
			<label>开始时间平台价</label>
			<input type="text" id="start_price" name="start_price"  style="width:275px;"/>
			<label>结束时间平台价</label>
			<input type="text" id="end_price" name="end_price"  style="width:275px;"/>
		</li>


		<li>
			<label>是否改变库存</label>
			<input type="checkbox" name="qty" id="qty" class="Toggle" value="0" />
		</li>
		<li class="qty" style="display: none;margin-left:50px;">
			<label>开始时间库存</label>
			<input type="text" id="start_qty" name="start_qty"  style="width:275px;"/>
			<label>结束时间库存</label>
			<input type="text" id="end_qty" name="end_qty"  style="width:275px;"/>
		</li>


		<li>
			<label>是否改变限购</label>
			<input type="checkbox" name="limit" id="limit" class="Toggle" value="0" />
		</li>
		<li class="limit" style="display: none;margin-left:50px;">
			<label>开始时间限购</label>
			<input type="text" id="start_limit" name="start_limit"  style="width:275px;"/>
			<label>结束时间限购</label>
			<input type="text" id="end_limit" name="end_limit"  style="width:275px;"/>
		</li>


		<li>
			<label>是否改变佣金</label>
			<input type="checkbox" name="rebate" id="rebate" class="Toggle" value="0" />
		</li>
		<li class="rebate" style="display: none;margin-left:50px;">
			<label>开始时间佣金</label>
			<input type="text" id="start_rebate" name="start_rebate"  style="width:275px;"/>
			<label>结束时间佣金</label>
			<input type="text" id="end_rebate" name="end_rebate"  style="width:275px;"/>
		</li>
		<li>
			<label>到达结束时间后操作</label>  <br/>
			<input type="radio" name="s2_method" checked="checked" value="0"/>  恢复之前
			<input type="radio" name="s2_method" value="1"/>  使用修改的
			<input type="radio" name="s2_method" value="2"/>  下架
		</li>
	</ul>
	<input type="button" value="保存" onclick="javascript:check();" style="width:100px;margin-top: 20px;">
</form>




<script type="text/javascript">
	//**************************************************************************选择供应商

       function gradeChange(){
        var objS = document.getElementById("sid");
        var suid = objS.options[objS.selectedIndex].id;
        $("#supplier").val(suid);
       }


	function subsearch() {
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
	function select_sku() {
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
				generate_sku_list(this);
			},
			buttons: {

				cancel: {
					text: '关闭',
					btnClass: 'btn-red',
					action: function () {
						$('#driver').html('');
						$('#list_selected_sku').html('<li>已选择商品</li>');
					}
				}
			}
		});
	}
	var sku_page_id = 0;
	var sku_search_key = '';
	var page_page_id = 0;
	var s_id = $('#supplier').val();

	function generate_sku_list(self) {
		
			var s_id = $('#supplier').val();
			YueMi.API.Admin.invoke('mall', 'select_sku', {
			__access_token: '{:$User->token:}',
			page: page_page_id,
			search: sku_search_key,
			supplier: s_id
		}, function (t, r, q) {
			var html = '<div>' +
					'查找：<input type="text" id="dlgsku_search" placeholder="商品标题关键字" class="input-text" value="' + sku_search_key + '" />' +
					'<input type="button" id="sku_button" value="查找"/>' +
					'<a href="javascript:void(0);" id="dlgsku_goto_prev">上一页(' + sku_page_id + ')</a>' +
					'<a href="javascript:void(0);" id="dlgsku_goto_next">下一页(' + sku_page_id + ')</a>' +
					'</div><ul class="TEMP_Sku_Selector">';
			$.each(q.List, function (k, v) {
				html += '<li>';
				html += '<a href="javascript:void(0);" onclick="create(' + v.id + ',\'' + v.title + '\')">' + v.id + ',' + v.title + '</a>';
				html += '</li>';
			});
			html += '</ul>';
			self.setContent(html);

			$('#dlgsku_goto_next').click(function () {
				sku_page_id++;
				generate_sku_list(self);
			});
			$('#dlgsku_goto_prev').click(function () {
				if (sku_page_id === 0) {
					alert('已经是第一页');
				} else {
					sku_page_id--;
					generate_sku_list(self);
				}
			});
			$('#sku_button').click(function () {
				sku_search_key = $('#dlgsku_search').val();
				generate_sku_list(self);
			})
		}, function (t, r, q) {
			alert(q.__message);
			self.setContent('出错了，关闭对话框重新来一次吧。');
		});
	}
	
	
	function generate_page_list(self) {
		
			var s_id = $('#supplier').val();
			YueMi.API.Admin.invoke('mall', 'select_sku', {
			__access_token: '{:$User->token:}',
			page: page_page_id,
			search: sku_search_key,
			supplier: s_id
		}, function (t, r, q) {
			var html = '<div>' +
					'查找：<input type="text" id="dlgsku_search" placeholder="商品标题关键字" class="input-text" value="' + sku_search_key + '" />' +
					'<input type="button" id="sku_button" value="查找"/>' +
					'<a href="javascript:void(0);" id="dlgsku_goto_prev">上一页(' + sku_page_id + ')</a>' +
					'<a href="javascript:void(0);" id="dlgsku_goto_next">下一页(' + sku_page_id + ')</a>' +
					'</div><ul class="TEMP_Sku_Selector">';
			$.each(q.List, function (k, v) {
				html += '<li>';
				html += '<a href="javascript:void(0);" onclick="create(' + v.id + ',\'' + v.title + '\')">' + v.id + ',' + v.title + '</a>';
				html += '</li>';
			});
			html += '</ul>';
			self.setContent(html);

			$('#dlgsku_goto_next').click(function () {
				sku_page_id++;
				generate_sku_list(self);
			});
			$('#dlgsku_goto_prev').click(function () {
				if (sku_page_id === 0) {
					alert('已经是第一页');
				} else {
					sku_page_id--;
					generate_page_list(self);
				}
			});
			$('#sku_button').click(function () {
				sku_search_key = $('#dlgsku_search').val();
				generate_page_list(self);
			})
		}, function (t, r, q) {
			alert(q.__message);
			self.setContent('出错了，关闭对话框重新来一次吧。');
		});
	}
	function create(id, title) {
		$('#sku_id').val(id);
		$('#name').val(title);
	}
	$('#title').click(function () {
		var num = $("#title").val();
		if (num == 1)
		{
			//关闭
			$("#title").val('0');
			$(".title").css('display', 'none');
		} else {
			//打开
			$("#title").val('1');
			$(".title").css('display', 'block');
		}

	})

	$('#subtitle').click(function () {
		var num = $("#subtitle").val();
		if (num == 1)
		{
			//关闭
			$("#subtitle").val('0');
			$(".subtitle").css('display', 'none');
		} else {
			//打开
			$("#subtitle").val('1');
			$(".subtitle").css('display', 'block');
		}

	})

	$('#price').click(function () {
		var num = $("#price").val();
		if (num == 1)
		{
			//关闭
			$("#price").val('0');
			$(".price").css('display', 'none');
		} else {
			//打开
			$("#price").val('1');
			$(".price").css('display', 'block');
		}

	})

	$('#qty').click(function () {
		var num = $("#qty").val();
		if (num == 1)
		{
			//关闭
			$("#qty").val('0');
			$(".qty").css('display', 'none');
		} else {
			//打开
			$("#qty").val('1');
			$(".qty").css('display', 'block');
		}
	})

	$('#limit').click(function () {
		var num = $("#limit").val();
		if (num == 1)
		{
			//关闭
			$("#limit").val('0');
			$(".limit").css('display', 'none');
		} else {
			//打开
			$("#limit").val('1');
			$(".limit").css('display', 'block');
		}
	})

	$('#rebate').click(function () {
		var num = $("#rebate").val();
		if (num == 1)
		{
			//关闭
			$("#rebate").val('0');
			$(".rebate").css('display', 'none');
		} else {
			//打开
			$("#rebate").val('1');
			$(".rebate").css('display', 'block');
		}
	})

	function check() {
		var tag = true;
		var supplier = $("#supplier").val();
		if (!supplier)
		{
			alert("请选择供应商");
			tag = false;

		}
		
		var sku_name = $("#name").val();
		if (!sku_name)
		{
			alert("请选择商品");
			tag = false;

		}
		
		var start = $('#online_time').val();
		if (!start)
		{
			alert("请选择开始时间");
			tag = false;
		}
		
		var end = $("#offline_time").val();
		if (!end)
		{
			alert("请选择结束时间");
			tag = false;
		}
		
		var date = new Date(start);
		var date = new Date(start.replace(/-/g, '/'));
		start = date.getTime();

		var enddate = new Date(end);
		var enddate = new Date(end.replace(/-/g, '/'));
		end = enddate.getTime();
		if (parseInt(start) > parseInt(end))
		{
			alert("开始时间必须小于结束时间");
			tag = false;
		}

		if (tag)
		{
			document.form1.submit();
		}

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
				str += '<option id="0" value="0">--请先选择供应商--</option>';
				str += '<option id = "'+val.id+'" value="' + val.name + '">' + val.name + '</option>';
			});
			$('#sid').html(str);
		}, function (t, q, r) {
			//失败
		});
	});
</script>
{:include file="_g/footer.tpl":}