{:include file="_g/header.tpl" Title="排期":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		排期管理
		<a class="button button-blue" style="float:left;" href="/index.php?call=mall.skutask_create"> <i class="fas fa-plus"></i> 添加排期 </a>
	
	</caption>
	<tr>
		<td>
			查询 
		</td>
		<td colspan="5">
			<form action="/index.php" method="get" name="form1">
				<input type="hidden" name="call" value="{:$_RUNTIME->ticket->handler:}.{:$_RUNTIME->ticket->action:}" />
				状态：<select id="status_serch" name="status_serch" style="background: white;">
					<option value="-1" >--状态选择--</option>
					<option value="0" {:if $_PARAMS.status_serch == '0':}selected="selected"{:/if:}>待审</option>
					<option value="1" {:if $_PARAMS.status_serch == '1':}selected="selected"{:/if:}>拒绝</option>
					<option value="2" {:if $_PARAMS.status_serch == '2':}selected="selected"{:/if:}>删除</option>
					<option value="3" {:if $_PARAMS.status_serch == '3':}selected="selected"{:/if:}>批准</option>
					<option value="4" {:if $_PARAMS.status_serch == '4':}selected="selected"{:/if:}>排队</option>
					<option value="5" {:if $_PARAMS.status_serch == '5':}selected="selected"{:/if:}>启动</option>
					<option value="6" {:if $_PARAMS.status_serch == '6':}selected="selected"{:/if:}>结束</option>
				</select>
				上架排期																								
				<input type="text" id="online_time" name="online_time"  class="input-date"  value="{:$online_time | number.datetime:}"/>-
				<input type="text" id="offline_time" name="offline_time"  class="input-date" readonly="readonly" value="{:$offline_time | number.datetime:}"/>
				关键字：<input type="text" value="{:$_PARAMS.key_serch:}" name="key_serch">
				分类:

			</form>
		</td>
		<td>
			<input type="button" onclick="subsearch()" value="搜索" style="width:100%;height:100%;"/>
		</td>
	</tr>
	<tr>
		<th>ID</th>
		<th>分类</th>
		<th >商品名称</th>
		<th >开始时间</th>
		<th>结束时间</th>
		<th>状态</th>
		<th>操作</th>
	</tr>
	{:foreach from=$res->Data value=SKU:}
	<tr>
		<td >{:$SKU.id:}</td>
		<td >{:$SKU.Cata | array.find $catagory,'id','name','':}</td>
		<td >{:$SKU.Title | string.key_highlight $_PARAMS.key_serch:}</td>
		<td >{:$SKU.s1_time | number.datetime:}</td>
		<td >{:$SKU.s2_time | number.datetime:}</td>
		<td >{:$SKU.status | array.enum ['待审',',拒绝',',删除','批准','排队','启动','结束','过期']:}	</td>
		<td >
			<a href="/index.php?call=mall.info&id={:$SKU.id:}">查看详情</a>
			
			{:if $SKU.status == 5 :}<a href="/index.php?call=mall.update&id={:$SKU.id:}">修改</a>{:/if:}
		</td>
	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="20">
			{:include file="_g/pager.tpl" Result=$res:}
		</td>
	</tr>
</table>
<script type="text/javascript">
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
		YueMi.API.Admin.invoke('sku', 'myget_catagory', {
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
</script>
{:include file="_g/footer.tpl":}