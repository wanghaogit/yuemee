{:include file="_g/header.tpl" Title="排期":}

<form name="form1" action="/index.php?call=mall.info&id={:$res['id']:}" method="post" id="form1">
	<ul class="Form">
		<li>
			<label>所选商品：</label>

			<input type="hidden" id="sku_id" value="{:$res['id']:}" name="sku_id" />
			<input type="text" id="name" name="name" value="{:$res['title']:}"  readonly="readonly" style="width:275px;"/>
		</li>
		<li>
			<label>开始时间</label>
			<input type="text" id="online_time" name="online_time"  class="input-date"  value="{:$res['s1_time']  | number.datetime:}"/>
		</li>
		<li>
			<label>结束时间</label>
			<input type="text" id="offline_time" name="offline_time"  class="input-date" readonly="readonly" value="{:$res['s2_time']  | number.datetime:}"/>
		</li>
		<li>
			<label>是否改变标题</label>
			<input type="checkbox" name="title" id="title" class="Toggle" value="{:$res['uf_title']:}" {:if($res['uf_title'] == 1):}checked{:/if:} /><br/>

		</li>
		<li class="title" {:if($res['uf_title'] == 0):}style="margin-left:50px;display: none;"{:/if:} >
			<label>开始时间标题</label>
			<input type="text" id="start_name" name="start_name" value="{:$res['s1_title']:}" style="width:275px;"/>
			<label>结束时间标题</label>
			<input type="text" id="end_name" name="end_name"  value="{:$res['s2_title']:}" style="width:275px;"/>
		</li>

		<li>
			<label>是否改变子标题</label>
			<input type="checkbox" name="subtitle" id="subtitle" class="Toggle" value="{:$res['uf_subtitle']:}" {:if($res['uf_subtitle'] == 1):}checked{:/if:}/>
		</li>
		<li class="subtitle"  {:if($res['uf_subtitle'] == 0):}style="margin-left:50px;display: none;"{:/if:}>
			<label>开始时间子标题</label>
			<input type="text" id="start_subtitle" name="start_subtitle"  value="{:$res['s1_subtitle']:}" style="width:275px;"/>
			<label>结束时间子标题</label>
			<input type="text" id="end_subtitle" name="end_subtitle" value="{:$res['s2_subtitle']:}" style="width:275px;"/>
		</li>


		<li>
			<label>是否改变平台价</label>
			<input type="checkbox" name="price" id="price" class="Toggle" value="{:$res['uf_price']:}" {:if($res['uf_price'] == 1):}checked{:/if:} />
		</li>
		<li class="price" {:if($res['uf_price'] == 0):}style="margin-left:50px;display: none;"{:/if:}>
			<label>开始时间平台价</label>
			<input type="text" id="start_price" name="start_price" value="{:$res['s1_price']:}"  style="width:275px;"/>
			<label>结束时间平台价</label>
			<input type="text" id="end_price" name="end_price" value="{:$res['s2_price']:}" style="width:275px;"/>
		</li>


		<li>
			<label>是否改变库存</label>
			<input type="checkbox" name="qty" id="qty" class="Toggle" value="{:$res['uf_qty']:}" {:if($res['uf_qty'] == 1):}checked{:/if:} />
		</li>
		<li class="qty" {:if($res['uf_qty'] == 0):}style="margin-left:50px;display: none;"{:/if:}>
			<label>开始时间库存</label>
			<input type="text" id="start_qty" name="start_qty" value="{:$res['s1_qty']:}" style="width:275px;"/>
			<label>结束时间库存</label>
			<input type="text" id="end_qty" name="end_qty" value="{:$res['s2_qty']:}" style="width:275px;"/>
		</li>


		<li>
			<label>是否改变限购</label>
			<input type="checkbox" name="limit" id="limit" class="Toggle" value="{:$res['uf_limit']:}" {:if($res['uf_limit'] == 1):}checked{:/if:}  />
		</li>
		<li class="limit"  {:if($res['uf_limit'] == 0):}style="margin-left:50px;display: none;"{:/if:}>
			<label>开始时间限购</label>
			<input type="text" id="start_limit" name="start_limit" value="{:$res['s1_limit']:}" style="width:275px;"/>
			<label>结束时间限购</label>
			<input type="text" id="end_limit" name="end_limit" value="{:$res['s1_limit']:}" style="width:275px;"/>
		</li>


		<li>
			<label>是否改变佣金</label>
			<input type="checkbox" name="rebate" id="rebate" class="Toggle" value="{:$res['uf_rebate']:}" {:if($res['uf_rebate'] == 1):}checked{:/if:} />
		</li>
		<li class="rebate" {:if($res['uf_rebate'] == 0):}style="margin-left:50px;display: none;"{:/if:}>
			<label>开始时间佣金</label>
			<input type="text" id="start_rebate" name="start_rebate" value="{:$res['s1_rebate']:}" style="width:275px;"/>
			<label>结束时间佣金</label>
			<input type="text" id="end_rebate" name="end_rebate" value="{:$res['s2_rebate']:}"  style="width:275px;"/>
		</li>
		<li>
			<label>到达结束时间后操作</label>  <br/>
			<input type="radio" name="s2_method"  {:if($res['s2_method'] == 0):}checked="checked" {:/if:} value="0"/>  恢复之前
			<input type="radio" name="s2_method" {:if($res['s2_method'] == 1):}checked="checked" {:/if:}  value="1"/>  使用修改的
			<input type="radio" name="s2_method"  {:if($res['s2_method'] == 2):}checked="checked" {:/if:} value="2"/>  下架
		</li>
	</ul>
		
	{:if $res['status'] == 0 ||  $res['status'] == 3 ||  $res['status'] == 4:}
		<input type="submit" value="修改" style="width:100px;margin-top: 20px;"/>
		<input type="button" value="通过" onclick="javascript:ok({:$res['id']:});" style="width:100px;margin-top: 20px;">
		<input type="button" value="驳回" onclick="javascript:no({:$res['id']:});" style="width:100px;margin-top: 20px;">
		<input type="button" value="删除" onclick="javascript:del({:$res['id']:});" style="width:100px;margin-top: 20px;">
	{:/if:}

</form>




<script type="text/javascript">

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
	function generate_sku_list(self) {
		YueMi.API.Supplier.invoke('sku', 'select_sku', {
			__access_token: '{:$User->token:}',
			page: page_page_id,
			search: sku_search_key
		}, function (t, r, q) {
			var html = '<div>' +
					'查找：<input type="text" id="dlgsku_search" placeholder="商品标题关键字" class="input-text" value="' + sku_search_key + '" />' +
					'<input type="button" id="sku_button" value="查找"/>' +
					'<a href="javascript:void(0);" id="dlgsku_goto_prev">上一页(' + sku_page_id + ')</a>' +
					'<a href="javascript:void(0);" id="dlgsku_goto_next">下一页(' + sku_page_id + ')</a>' +
					'</div><ul class="TEMP_Sku_Selector">';
			$.each(q.List, function (k, v) {
				html += '<li>';
				html += '<a href="javascript:void(0);" onclick="create(' + v.id + ',\'' + v.title + '\')">' + v.title + '</a>';
				html += '</li>';
			});
			html += '</ul>';
			self.setContent(html);

			$('#dlgsku_goto_next').click(function () {
				sku_page_id++;
				____generate_sku_list(self);
			});
			$('#dlgsku_goto_prev').click(function () {
				if (sku_page_id === 0) {
					alert('已经是第一页');
				} else {
					sku_page_id--;
					____generate_sku_list(self);
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


		if (tag)
		{
			document.form1.submit();
		}

	}

	//审核通过
	function ok(id)
	{
		YueMi.API.Admin.invoke('mall', 'ok', {
			__access_token: '{:$User->token:}',
			id: id
		}, function (t, q, r) {
			alert(r.__message);
			 window.location.href = '/index.php?call=mall.sku_task';
		}, function (t, q, r) {
			alert(r.__message);
			//失败
		});
	}
	//驳回
	function no(id)
	{
		YueMi.API.Admin.invoke('mall', 'no', {
			__access_token: '{:$User->token:}',
			id: id
		}, function (t, q, r) {
			alert(r.__message);

		}, function (t, q, r) {
			//失败
			alert(r.__message);
		});
	}
	//删除
	function del(id)
	{
		YueMi.API.Admin.invoke('mall', 'del', {
			__access_token: '{:$User->token:}',
			id: id
		}, function (t, q, r) {
			alert(r.__message);

		}, function (t, q, r) {
			//失败
			alert(r.__message);
		});
	}
</script>
{:include file="_g/footer.tpl":}