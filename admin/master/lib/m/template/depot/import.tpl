{:include file="_g/header.tpl" Title="库存/新增品类":}
<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/calendar.js"></script>
<br />
<h1>外部SPU/SKU数据导入</h1>
<br />
<form name="form1" action="/index.php?call=depot.designer_create" method="post">
	<ul class="Form">
		<li>
			<label>起始时间：</label>
			<input type="text" id="time1" name="time1"  class="input-date" readonly="readonly" value=" " />&nbsp;格式：2005-06-26 19:32:50
		</li>
		<li>
			<label>终止时间：</label>
			<input type="text" id="time2" name="time2" class="input-date" readonly="readonly" value=" " />&nbsp;格式：2024-07-01 06:12:50
		</li>
		<li>
			<input type="button" value="开始导入" onclick="javascript:check1();" />
		</li>
	</ul>
</form>
<br />
<span id="a1"></span>
{:include file="_g/footer.tpl":}
<script>
	$(".input-date").datetimepicker({
		autoclose: true,
		clearBtn: true,
		todayBtn: true,
		todayHighlight: true,
		fontAwesome: true,
		zIndex: 9999,
		format: 'yyyy-mm-dd hh:ii'
	});

	function check1(i = 1)
	{
		if ($('#time1').val() == '')
		{
			alert('请输入起始时间');
			return;
		}
		if ($('#time2').val() == '')
		{
			alert('请输入终止时间');
			return;
		}

		$('#a1').html('正在导入第 <b>' + i + '</b> 页数据。。。。');
		YueMi.API.Admin.invoke('depot', 'extspu_import', {
			time1: $('#time1').val(),
			time2: $('#time2').val(),
			page: i
		}, function (target, request, response) {
			check1(++i);
			//alert(response.__message);
		}, function (target, request, response) {
			alert(response.__message);
			i--;
			$('#a1').html('共导入 <b>' + i + '</b> 页数据');
		});

		return;
	}
</script>

