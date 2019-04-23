{:include file="_g/header.tpl" Title="订单":}
<style type="text/css">
	.hidediv{
		width:500px;
		height:300px;
		z-index:999;
		position:absolute;
		left:500px;
		height:200px;
		background-color:#fff;
		border:1px solid #000;
		display:none;
	}
</style>

<!-- *************************************************** 切换标签 start *************************************************** -->
<div style="padding-bottom:10px">
	<a href="/index.php?call=order.index&type=0">{:if $_PARAMS.type == 0:}<B style="color:red">全部订单</B>{:else:}全部订单{:/if:}</a>
	<a href="/index.php?call=order.index&type=1">{:if $_PARAMS.type == 1:}<B style="color:red">新订单</B>{:else:}新订单{:/if:}</a>
	<a href="/index.php?call=order.index&type=2">{:if $_PARAMS.type == 2:}<B style="color:red">已支付</B>{:else:}已支付{:/if:}</a>
	<a href="/index.php?call=order.index&type=4">{:if $_PARAMS.type == 4:}<B style="color:red">待发货</B>{:else:}待发货{:/if:}</a>
	<a href="/index.php?call=order.index&type=5">{:if $_PARAMS.type == 5:}<B style="color:red">已发货</B>{:else:}已发货{:/if:}</a>
	<a href="/index.php?call=order.index&type=6">{:if $_PARAMS.type == 6:}<B style="color:red">已签收</B>{:else:}已签收{:/if:}</a>
	<a href="/index.php?call=order.index&type=7">{:if $_PARAMS.type == 7:}<B style="color:red">已确认</B>{:else:}已确认{:/if:}</a>
	<a href="/index.php?call=order.index&type=8">{:if $_PARAMS.type == 8:}<B style="color:red">已评价</B>{:else:}已评价{:/if:}</a>
	<a href="/index.php?call=order.index&type=11">{:if $_PARAMS.type == 11:}<B style="color:red">用户主动关闭</B>{:else:}用户主动关闭{:/if:}</a>
	<a href="/index.php?call=order.index&type=13">{:if $_PARAMS.type == 13:}<B style="color:red">退款关闭</B>{:else:}退款关闭{:/if:}</a>
	<a href="/index.php?call=order.index&type=14">{:if $_PARAMS.type == 14:}<B style="color:red">后台取消</B>{:else:}后台取消{:/if:}</a>
</div>
<!-- *************************************************** 切换标签 end *************************************************** -->

<!-- *************************************************** 数据列表 start *************************************************** -->
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		<B>订单管理</B>
		<!--<a href="/index.php?call=order.export&type=0" style="float:right">导出订单</a>-->
	</caption>
	<tr>
	<form action="/index.php" method="get">
		<td colspan="100" style="background-color:#F8F8FF; padding:8px 5px 8px 5px">
			供应商：
			<select name="supplier_id">
				<option value="0">--请选择--</option>
				{:foreach from=$supplier key=k value=v:}
				<option {:if $_PARAMS.supplier_id == $v['id']:}selected="selected"{:/if:} value="{:$v['id']:}">{:$v['name']:}</option>
				{:/foreach:}
			</select>
			<input type="hidden" name="call" value="{:$_RUNTIME->ticket->handler:}.{:$_RUNTIME->ticket->action:}" />
			下单时间
			<input type="text" class="input-date" id="search_time_start" name="search_time_start" readonly="readonly" value="{:$search_time_start | number.datetime:}" />
			-
			<input type="text" class="input-date" id="search_time_end" name="search_time_end" readonly="readonly" value="{:$search_time_end | number.datetime:}" />
			&nbsp; 姓名:&nbsp;<input name="search_name" value="{:$_PARAMS.search_name:}" />
			&nbsp; 手机号:&nbsp;<input name="search_mobile" value="{:$_PARAMS.search_mobile:}" /> 
			&nbsp; 订单号:&nbsp;<input name="search_order_id" value="{:$_PARAMS.search_order_id:}" />
			&nbsp; 物流单号:&nbsp;<input name="search_trans_id" value="{:$_PARAMS.search_trans_id:}" />
			&nbsp; 订单状态:&nbsp;<select name="type">
				{:foreach from=$StatusList key=k value=v:}
				<option {:if $_PARAMS.type == $k:}selected="selected"{:/if:} value="{:$k:}">{:$v:}</option>
				{:/foreach:}
			</select>
			&nbsp;

			<input type="submit" name="ActionName" id="sousuo" value="按搜索条件导出订单" style="float:right;"/>
			<input type="submit" name="ActionName" value="搜索" style="float:right;" />
		</td>
	</form>
</tr>
{:foreach from=$DataList->Data value=Data:}
<tr onclick="ShowHideTr('Tr{:$Data.id:}')">
	<td colspan="100" style="padding-top:10px; padding-bottom:10px">
		<div style="float:left; width:16%">
			主订单号: {:if !empty($_PARAMS.search_order_id):}<B style="color:red">{:/if:}
				{:$Data.depend_id:}
				{:if !empty($_PARAMS.search_order_id):}</B>{:/if:}
			<br />
			订单号: {:if !empty($_PARAMS.search_order_id):}<B style="color:red">{:/if:}
				{:$Data.id:}
				{:if !empty($_PARAMS.search_order_id):}</B>{:/if:}
			<br />
			{:if $Data.supplier_id == 2 && $Data.ext_order_id != "":}
			内购订单号: {:$Data.ext_order_id:}<br />
			{:/if:}
			订单价格：{:$Data.c_amount | number.currency:}<br />
			订单群总价：{:$Data.t_amount | number.currency:}<br />
			退货原因: <br />
		</div>
		<div style="float:left; width:25%">
			购买用户: {:$Data.buy_user | string.key_highlight $_PARAMS.search_name:}<br /> 
			用户ID：{:$Data.buy_id:}<br/>
			用户电话: {:$Data.buy_mobile | string.key_highlight $_PARAMS.search_mobile:}<br />
			供应商： {:if($Data.supplier_id == 2):}{:$Data.supplier:}（{:$Data.ItemList[0]['SupplierName']:}）{:else:}{:$Data.supplier:}{:/if:}<br />
			货品总数量：{:$Data.qty:}<br />
			{:if $Data.trans_com == "":}
			物流单号：{:if $Data.supplier_id == 2:}<a href="/index.php?call=order.detail&id={:$Data.id:}&type={:$_PARAMS.type:}">同步物流信息</a>{:else:}无{:/if:}<br />
			物流公司：{:if $Data.supplier_id == 2:}<a href="/index.php?call=order.detail&id={:$Data.id:}&type={:$_PARAMS.type:}">同步物流信息</a>{:else:}无{:/if:}<br />
			{:else:}
			物流单号：{:$Data.trans_id:}<br />
			物流公司：{:$Data.trans_com:}<br />
			{:/if:}
			订单状态：{:$Data.status:}<br />
		</div>
		<div style="float:left; width:38%">
			支付回单号：{:$Data.pay_serial:}<br />
			确认收货: {:if $Data.trans_fin == 0:}否{:else:}是{:/if:}<br />
			用户留言: {:$Data.comment_user:}<br />
			<!--物流单号: {:$Data.trans_id:}<br />-->
			所属地区: {:$Data.Province:}-{:$Data.City:}-{:$Data.Country:}<br />
			收货地址: {:$Data.Province:}{:$Data.City:}{:$Data.Country:}，{:$Data.addr_detail:}，
			{:$Data.addr_name | string.key_highlight $_PARAMS.search_name:} -
			{:$Data.addr_mobile | string.key_highlight $_PARAMS.search_mobile:}<br />
		</div>
		<div style="float:left; width:16%">
			下单时间: {:$Data.create_time | number.datetime:}<br />
			支付时间: {:$Data.pay_time | number.datetime:}<br />
			收货时间: {:$Data.trans_time | number.datetime:}<br />
			最后更新: {:$Data.update_time | number.datetime:}<br />
			管理备注: {:$Data.comment_admin:}<br />
			<span style="font-weight:bold">
				<a href='/index.php?call=order.detail&id={:$Data.id:}&type={:$_PARAMS.type:}'>详情</a>&nbsp;
				{:if $Data.order_status <= 3:}
				<a onclick="javascript: off_order('{:$Data.id:}');"  style="color:red;">关闭</a>&nbsp;
				{:if empty($Data.pay_serial):}
				<a onclick="javascript: repair_wx('{:$Data.id:}');">刷新支付状态</a>&nbsp;
				{:/if:}
				{:/if:}

				{:if $Data.order_status == 4:}
				<a onclick="javascript: off_order('{:$Data.id:}');"  style="color:red;">关闭</a>&nbsp;
				{:if $Data.supplier_id == 2:}
				<a onclick="javascript: neigou_create('{:$Data.id:}');">发货(向内购下单)</a>&nbsp;
				{:else:}
				<a onclick="javascript: set_logistics('{:$Data.id:}', '{:$Data.trans_id:}', '{:$Data.trans_com:}');">发货</a>&nbsp;
				{:/if:}
				{:/if:}
				{:if  $Data.order_status == 5:}
				{:if $Data.supplier_id == 2:}
				<a onclick="javascript: neigou_create('{:$Data.id:}');">内购物流</a>&nbsp;
				{:else:}
				<a onclick="javascript:set_logistics('{:$Data.id:}', '{:$Data.trans_id:}', '{:$Data.trans_com:}');">修改物流</a>&nbsp;
				{:/if:}
				{:/if:}
				{:if  $Data.order_status > 4 && $Data.order_status < 10 :}
				<a href="/index.php?call=order.detail&id={:$Data.id:}&type={:$_PARAMS.type:}">查看物流</a>&nbsp;
				{:/if:}
				{:if  $Data.order_status > 20:}
				<a href="/index.php?call=order.sales_service&id={:$Data.id:}">售后记录</a>&nbsp;
				{:/if:}
				{:if  $Data.order_status > 5 && $Data.order_status < 9 :}
				<a onclick="javascript: off_order('{:$Data.id:}')" style="color:red;">关闭</a>&nbsp;
				{:/if:}
			</span>
		</div>
		<div style="float:left; width:5%; text-align: center; cursor:pointer">
			<di<br /><br /><br />
				<span style="font-size:28px">∨</span>
		</div>
	</td>
</tr>
<tr  id='Tr{:$Data.id:}' style="display:none">
	<td>
		<table border="0" cellspacing="0" cellpadding="0" class="Grid">
			<tr>
				<td>缩略图</td>
				<td>品类</td>
				<td>商品名称</td>
				<td>供应商</td>
				<td>规格</td>
				<td>数量</td>
				<td>结算单价</td>
				<td>结算总价</td>
				<td>优惠券金额</td>
				<td>成本价</td>
				<td>分享者</td>
				<td>返佣目标用户</td>
				<td>返佣金额</td>
			</tr>
			{:foreach from=$Data.ItemList value=item:}
			<tr>
				<td><img style="width:50px;height:50px;" src="{:#URL_RES:}/upload{:$item.picture:}"/></td>
				<td>{:$item.CatagoryName:}</td>
				<td>{:$item.title:}</td>
				<td>{:$item.SupplierName:}</td>
				<td>{:$item.specs:}</td>
				<td>{:$item.qty:}</td>
				<td>{:$item.price | number.currency:}</td>
				<td>{:$item.money | number.currency:}</td>
				<td>{:$item.tiprice:}</td>
				<td>{:$item.price_base | number.currency:}</td>
				<td>{:if $item.share_id > 0:}{:$item.share_id:}{:/if:}</td>
				<td>{:if $item.rebate_user > 0:}{:$item.rebate_user:}{:/if:}</td>
				<td>{:if $item.rebate_vip < 0 :}0{:else:}{:$item.rebate_vip | number.currency:}{:/if:}</td>
			</tr>
			{:/foreach:}
		</table>
		<div>物流信息：<br />{:$Data.trans_trace:}</div>
	</td>
</tr>
{:/foreach:}
</table>
<!-- *************************************************** 数据列表 end *************************************************** -->

<div style="text-align:center">{:include file="_g/pager.tpl" Result=$DataList:}</div>
<div style="clear: both;height:20px">&nbsp;</div>

<!-- 添加物流(发货)、修改物流 -->
<div class="hidediv" style="margin-top: -400px;position: fixed;bottom: 40%;">
	<a style="float:right;font-size: 18px;margin-right: 12px;" id="yincang">x</a>
	<div style="padding:20px">
		<input type="hidden" id="OrderId" value="" />
		<div>物流单号：<input type="text" value="" id="TransNum" style="width:60%" /></div>
		<div style="padding-top:15px">
			物流公司：
			<select id="TransCom"></select>
		</div>
		<div>&nbsp;</div>
		<center>
			<button style="width:300px; height:60px; font-size:24px" id="changed">保 存</button>
		</center>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    $("input").keydown(function (e) {
      var curKey = e.which;
      if (curKey == 13) {
		 $("#sousuo").val("搜索");
       // $("#lbtn_JumpPager").click();
        //return false;
      }
    });
});
	var TransComCodeDefault = '';

	// 日期选择框
	$(".input-date").datetimepicker({
		lang: "ch",
		autoclose: 1,
		minView: "hour",
		startDate: '2018-01-01',
		autoclose: true,
		clearBtn: true,
		todayBtn: true,
		todayHighlight: true,
		fontAwesome: true,
		zIndex: 9999,
		format: 'yyyy-mm-dd HH:ii:ss'
	});

	// 显示、隐藏tr
	function ShowHideTr(id) {
		if (document.getElementById(id).style.display == 'none') {
			document.getElementById(id).style.display = '';
		} else {
			document.getElementById(id).style.display = 'none';
		}
	}

	// 浮动框关闭
	$("#yincang").click(function () {
		$(".hidediv").hide();
	});

	/** ******************************** 修改物流 ******************************** **/

	// 物流单号失去焦点
	$("#TransNum").blur(function () {
		// 加载物流公司列表
		$("#TransCom").empty();
		$("#TransCom").append("<option value=''>加载中...</option>");
		YueMi.API.Admin.invoke('order', 'transnum_to_transcom_list', {
			order_id: '' + $("#TransNum").val(),
		}, function (t, q, r) {
			var data = r.data;
			$("#TransCom").empty();
			$("#TransCom").append("<option value=''>请选择物流公司</option>");
			for (var i = 0; i < data.length; i++)
			{
				if (TransComCodeDefault == data[i].key) {
					$("#TransCom").append("<option value='" + data[i].key + "' selected='selected'>" + data[i].val + "</option>");
				} else {
					$("#TransCom").append("<option value='" + data[i].key + "'>" + data[i].val + "</option>");
				}
			}
		}, function (t, q, r) {
			if (r.__code == 'E_AUTH') {
				alert("登录超时，请重新登录!");
				window.location.href = '/index.php?call=default.login';
			}
			alert("网络错误!");
		});
	});

	/**
	 * 修改物流 - 加载界面
	 * @param {type} id				阅米订单Id
	 * @param {type} TransNum		物流订单Id
	 * @param {type} TransComCode	物流公司代码
	 */
	function set_logistics(id, TransNum, TransComCode)
	{
		$("#TransCom").empty();
		$("#TransCom").append("<option value=''>加载中...</option>");
		TransComCodeDefault = TransComCode;
		$('#OrderId').val(id);
		$('#TransNum').val(TransNum);
		$('.hidediv').fadeIn();
		$("input").blur(function () {
			$("input").css("background-color", "#D6D6FF");
		});
		// 加载物流公司列表
		YueMi.API.Admin.invoke('order', 'transnum_to_transcom_list', {
			__access_token: '{:$User->token:}',
			order_id: '' + TransNum,
		}, function (t, q, r) {
			var data = r.data;
			$("#TransCom").empty();
			$("#TransCom").append("<option value=''>请选择物流公司</option>");
			for (var i = 0; i < data.length; i++)
			{
				if (TransComCodeDefault == data[i].key) {
					$("#TransCom").append("<option value='" + data[i].key + "' selected='selected'>" + data[i].val + "</option>");
				} else {
					$("#TransCom").append("<option value='" + data[i].key + "'>" + data[i].val + "</option>");
				}
			}
		}, function (t, q, r) {
			if (r.__code == 'E_AUTH') {
				alert("登录超时，请重新登录!");
				window.location.href = '/index.php?call=default.login';
			}
			alert("网络错误!");
		});
	}

	$('#changed').click(function () {
		var OrderId = $('#OrderId').val();
		var TransNum = $('#TransNum').val();
		var TransCom = $('#TransCom').val();
		if (TransNum.length < 1) {
			alert('请输入物流单号');
			exit;
		}
		if (TransCom.length < 1) {
			alert('请选择物流公司');
			exit;
		}
		YueMi.API.Admin.invoke('order', 'change', {
			__access_token: '{:$User->token:}',
			OrderId: OrderId,
			TransNum: TransNum,
			TransCom: TransCom,
		}, function (t, q, r) {
			$('.hidediv').fadeOut();
			location.reload();
		}, function (t, q, r) {
			if (r.__code == 'E_AUTH') {
				alert("登录超时，请重新登录!");
				window.location.href = '/index.php?call=default.login';
			}
			alert("网络错误!");
		});
	});

	/** ******************************** 内购 ******************************** **/

	/**
	 * 创建内购订单
	 * @param {type} OrderId
	 */
	function neigou_create(OrderId)
	{
		alert("内购商品后台自动下单，如果一直无法下单，请联系技术人员检查原因!");
	}

	/** ******************************** 补单 ******************************** **/

	/**
	 * 微信补单
	 * @param string OrderId 订单Id
	 */
	function repair_wx(OrderId)
	{
		YueMi.API.Admin.invoke('order', 'repair_wx', {
			__access_token: '{:$User->token:}',
			order_id: OrderId,
		}, function (t, q, r) {
			alert(r.__message);
			location.reload();
		}, function (t, q, r) {
			if (r.__code == 'E_AUTH') {
				alert("登录超时，请重新登录!");
				window.location.href = '/index.php?call=default.login';
			}
			alert("网络错误!");
		});
	}

	/************************************关闭订单*************************************/
	function off_order(id)
	{
		YueMi.API.Admin.invoke('order', 'off_order', {
			__access_token: '{:$User->token:}',
			order_id: id
		}, function (t, q, r) {
			alert(r.__message);
			location.reload();
		}, function (t, q, r) {
			alert("网络错误!")
		});
	}
	
	
	/*************************************关闭订单wh*******************************************/
	function close_order(id){
		YueMi.API.Admin.invoke('order', 'close_order', {
			__access_token: '{:$User->token:}',
			order_id: id
		}, function (t, q, r) {
			alert(r.__message);
			location.reload();
		}, function (t, q, r) {
			alert("网络错误!")
		});
	}
</script>
{:include file="_g/footer.tpl":}
