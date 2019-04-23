{:include file="_g/header.tpl" Title="运营/数据源":}
<style>
	#mb{
		width: 100%;
		height: 100%;
		background: rgba(0, 0, 0, 0.5);
		position: fixed;
		top: 0;
		left: 0;
		z-index: 100;
		display: none;
	}
	.add_content{
		width: 600px;
		height: auto;
		margin: 50px auto;
		box-shadow: 0px 1px 10px 0px #f1f1f1;
		background: #fff;
		position: absolute;
		z-index: 110;
		display: none;
		text-align: center;
	}
	.form_add{
		border-radius: 5px;
		padding-bottom: 15px;
	}
	.form_add h3 {
		font-size: 18px;
		height: 30px;
		background-color: #C8DEFC;
		line-height: 30px;
		border-radius: 5px 5px 0px 0px/5px 5px 0px 0px;
		font-weight: 500;
	}
	.form_add>input {
		float: right;
		width: 50px;
		height: 25px;
		margin-right: 20px;
		margin-top: 20px;
		color: #FFF;
		text-shadow: -1px -1px 0 rgba(0, 0, 0, 0.3);
		font-weight: normal;
	}
</style>
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
        {:$page_name:}数据源管理
        <a class="button button-blue" style="float:left;" href="/index.php?call=runer.source_create&page_id={:$page_id:}">
			<i class="fas fa-plus"></i> 新增数据源
		</a>
    </caption>
	<tr>
		<th>ID</th>
		<th>页面名称</th>
		<th>页面代号</th>
			{:if $where == 'spage':}
		<th>运营模块名称</th>
			{:/if:}
		<th>数据源类型</th>
		<th>数据源格式</th>
		<th>操作</th>
	</tr>
	{:foreach from=$Result->Data value=v:}
	<tr>
		<td>{:$v.id:}</td>
		<td><a href="/index.php?call=runer.source_update&id={:$v.id:}">{:$v.name:}</a></td>
		<td>{:$v.alias:}</td>
		{:if $where == 'spage':}
		<td>{:$v.block_name:}</td>
		{:/if:}
		<td>{:if $v.style == 0:}SQL{:elseif $v.style == 1:}PHP{:elseif $v.style == 2:}缓存{:/if:}</td>
		<td>{:if $v.type == 0:}自定义{:elseif $v.type == 1:}单品{:elseif $v.type == 2:}多品{:/if:}</td>
		<td>
			<a href="javascript:void(0);" onclick="javascript:drop_press({:$v.id:})" style="color:red;"><i class="fas fa-times"></i>删除</a> | 
			<a href="javascript:void(0);" onclick="javascript:test({:$v.id:})" style="color:blue;">测试</a>
		</td>
	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="9">
			{:include file="_g/pager.tpl" Result=$Result:}
		</td>
	</tr>
</table>
<div id="mb"></div>
<form class="form_add add_content" id="source_test" name="form1"  style="overflow-x: scroll">
	<h3 style="margin-bottom:20px;"><i class="fas fa-shield "></i>数据源测试</h3>
	<div>
		<table  border="0" cellspacing="0" cellpadding="0" class="Grid">
			<tbody  id="test_win">

			</tbody>
		</table>
	</div>
	<input id="cancel" type="button" value="取 消" onclick="javascript:stop()">
</form>
<script type="text/javascript">
	function tankuang() {
		$('#mb').toggle();
		$('#source_test').toggle();
		if ($(window).width() < 800) {
			$(window).width(800);
		}
		//var left = $(window).width() / 2 - $('.form_add').width() / 2;
		var top = $(window).height() / 2 - $('.form_add').height() / 2;
		//$('.form_add').css({'left': left, 'top': top});
		$('.form_add').css({'top': top});
	}
	function stop() {
		$('#mb').toggle();
		$('#source_test').toggle();
	}
	function chose_goods(){
		
	}
	function test(id) {
		YueMi.API.Admin.invoke('runer', 'source_test', {
			__access_token : '{:$User->token:}',
			id: id
		}, function (t, r, q) {
			if (q.__code === 'OK')
			{
				if (q.Result === null) {
					alert('OK');
					return;
				}
				tankuang();
				if (r.type == 2 || r.type == 1){
					var str = "";
					var strth = "";
					$.each(q.Result, function (key, val) {

						str += "<tr>";
						strth += "<tr>";
						$.each(val, function (k, v) {
							if (strth.indexOf("</tr>") === -1) {
								strth += "<th>" + k + "</th>";
							}
							str += "<td>" + v + "</td>";
						});
						if (strth.indexOf("</tr>") === -1) {
							strth += "</tr>";
						}
						str += "</tr>";
					});
					$("#test_win").html('');
					$("#test_win").append(strth);
					$("#test_win").append(str);
				}
				if (r.type == 3){
					console.log(r);
				}
				if (r.type == 4){
					console.log(r);
				}
			} else {
				console.log(q);
				alert(q.__message);
			}
		}, function (t, r, q) {
			alert(q.__message);
		});
	}
	function drop_press(id) {
		$.confirm({
			useBootstrap: false,
			type: 'blue',
			boxWidth: '300px',
			escapeKey: 'cancel',
			backgroundDismiss: false,
			backgroundDismissAnimation: 'glow',
			icon: 'fa fa-shield',
			title: '删除品类',
			content: '删除吗？',
			buttons: {
				accept: {
					btnClass: 'btn-red',
					text: '删除',
					action: function () {
						YueMi.API.Admin.invoke('runer', 'source_del', {
							__access_token : '{:$User->token:}',
							id: id
						}, function (t, r, q) {
							if (q.__code === 'OK')
							{
								location.reload();
							} else {
								console.log(q);
								alert(q.__message);
							}
						}, function (t, r, q) {
							alert(q.__message);
						});
					}
				},
				cancel: {text: '取消', btnClass: 'btn-blue', action: function () {}}
			}
		});
	}
</script>
{:include file="_g/footer.tpl":}
