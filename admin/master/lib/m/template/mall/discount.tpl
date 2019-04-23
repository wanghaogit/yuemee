{:include file="_g/header.tpl" Title="库存/品类":}
<style>
	#tab tr td{

	}
</style>
<table cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		优惠券管理
	</caption>
	<tr>
		<td colspan="15"><button onclick="newone()">新增优惠券</button></td>
	</tr>
	<tr>
		<th>ID</th>
		<th>类型</th>
		<th>SPU</th>
		<th>价值</th>
		<th>最小订单价</th>
		<th>有效期</th>
		<th>使用者</th>
		<th>创建者</th>
		<th>创建时间</th>
		<th>更新时间</th>
		<th>状态</th>
		<th>操作</th>

	</tr>
	{:foreach from=$res->Data item=S:}
	<tr>
		<td>{:$S.id:}</td>
		<td>{:if $S.type == 0:}未知{:elseif $S.type == 1:}商品券{:elseif $S.type == 2:}商家券{:elseif $S.type == 3:}品类券{:/if:}</td>
		<td>{:$S.title:}</td>
		<td>￥{:$S.value:}</td>
		<td>￥{:$S.price_small:}</td>
		<td>{:$S.expiry_date | number.datetime:}</td>
		<td>{:$S.UseName:}</td>
		<td>{:$S.CreName:}</td>
		<td>{:$S.create_time | number.datetime:}</td>
		<td>{:$S.update_time | number.datetime:}</td>
		<td>{:if $S.status == 0:}初始{:elseif  $S.status == 1:}已使用{:elseif  $S.status == 2:}<span style="color:red;">关闭</span>{:else:}{:/if:}</td>
		<td>{:if $S.status < 2:}<a onclick="close_card('{:$S.id:}')" style="color:red;">禁用</a>{:else:}{:/if:}</td>
	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="12">{:include file="_g/pager.tpl" Result=$res:}</td>
	</tr>
</table>
<script type="text/javascript">


	function newone() {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '600px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fas fa-shield',
			title: '新增优惠券',
			content: '正在加载...',
			onContentReady: function () {
				____generate_sku_list(this);
				$(".input-date").datetimepicker({
					autoclose: true,
					clearBtn: true,
					todayBtn: true,
					todayHighlight: true,
					fontAwesome: true,
					zIndex: 9999999999,
					format: 'yyyy-mm-dd hh:ii'
				});
			},
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '添加',
					action: function () {
						YueMi.API.Admin.invoke('mall', 'insert_discount', {
							__access_token: '{:$User->token:}',
							id: $('#card_id').val(),
							type: $('#card_type').val(),
							spuid: $('#spu_id').val(),
							val: $('#card_val').val(),
							small: $('#card_small').val(),
							can_use: $('#can_use').val()
						}, function (t, q, r) {
							location.reload();
						}, function (t, q, r) {
							alert(r.__message);
						});
					}
				},
				cancel: {
					text: '取消',
					btnClass: 'btn-blue',
					action: function () {

					}
				}
			}
		});
	}

	function ____generate_sku_list(self) {
		var idstr = '';
		YueMi.API.Admin.invoke('mall', 'get_discount_id', {
			__access_token: '{:$User->token:}',

		}, function (t, q, r) {
			idstr = r.id;
			var str = '<table style="width:100%;" id="tab">	<tr>		<td>ID</td>		<td><input type="text" value="' + idstr + '" id="card_id" style="width:300px;" readonly="readonly" /></td>	</tr>	<tr>		<td>类型</td>		<td>			<select id="card_type">				<option value="0">未知</option>				<option value="1">商品券</option>				<option value="2">商家券</option>				<option value="3">品类券</option>			</select>		</td>	</tr>	<tr>		<td>SPUID</td>		<td><input type="text" value="" id="spu_id" /></td>	</tr>	<tr>		<td>价值</td>		<td><input type="text" value="" id="card_val" /></td>	</tr>	<tr>		<td>最小订单价</td>		<td><input type="text" value="" id="card_small" /></td>	</tr>	<tr>		<td>有效期</td>		<td>			<input type="text" class="input-date" id="can_use" name="can_use" readonly="readonly" value="" />		</td>	</tr></table>';
			self.setContent(str);
			$(".input-date").datetimepicker({
					autoclose: true,
					clearBtn: true,
					todayBtn: true,
					todayHighlight: true,
					fontAwesome: true,
					zIndex: 9999999999,
					format: 'yyyy-mm-dd hh:ii'
				});
		}, function (t, q, r) {
			alert(r.__message);
		});


	}
	
	function close_card(id){
		YueMi.API.Admin.invoke('mall', 'close_card', {
			__access_token: '{:$User->token:}',
			id : id
		}, function (t, q, r) {
			location.reload();	
		}, function (t, q, r) {
			alert(r.__message);
		});
	}
</script>
{:include file="_g/footer.tpl":}
