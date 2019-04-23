{:include file="_g/header.tpl" Title="通知/编辑":}
<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/calendar.js"></script>
<script type="text/javascript" src="/scripts/editor.js"></script>
<form name="form1" action="/index.php?call=notify.notice_create" method="post">
	<ul class="Form">
		<li>
			<label>公告标题：</label>
			<input type="text" id="title" name="title"  />
		</li>
		<li>
			公告内容：<div id="e_content" name="e_content" style="width:670px;"></div>
			<script type="text/javascript">
				var e = new window.wangEditor('#e_content');
				e.create();
			</script>
			<input type="hidden" id="content" name="content"/>
		</li>
		<li>
			<label>公开时间：</label>
			<input type="text" id="open_time" name="open_time"  class="input-date" readonly="readonly" value="{:$time:}"/>
		</li>
        <li>
			<label>关闭时间：</label>
			<input type="text" id="close_time" name="close_time"  class="input-date" readonly="readonly" value="{:$time:}"/>
		</li>
		<li>
			<label>是否草稿：</label>
			<input type="checkbox" class="Toggle" id="pi_enable" name="status" value="1" />
		</li>
		
		<li>
			<label>公告范围：</label>
			<select id="scope_id" name="scope_id"/>
		<option value="0">-- 请选择 --</option>
		<option value="0">全体</option>
		<option value="1">非VIP</option>
		<option value="2">VIP</option>
		<option value="3">总监</option>
		<option value="4">经理</option>
		<option value="5">供应商</option>
		<option value="6">员工</option>
		</select>
		</li>
        <li>
			<input type="button" value="发布" onclick="javascript:check1();" />
		</li>


	</ul>

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
	function check1()
	{
		if ($('#title').val() == '')
		{
			alert('请输入公告标题');
			return;
		}
		var html = e.txt.html();
		var infoobj = document.getElementById('content');
		infoobj.value = html;
		document.form1.submit();
	}
</script>
{:include file="_g/footer.tpl":}

