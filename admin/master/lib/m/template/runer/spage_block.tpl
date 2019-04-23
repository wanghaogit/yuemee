{:include file="_g/header.tpl" Title="运营/APP":}
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
		width: 300px;
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
        {:$page_name:}模块管理
        <a class="button button-red" style="float:left;" href="/index.php?call=runer.spage&parent_id={:$parent_id:}" title="返回页面">
			<i class="fas fa-angle-left"></i>
		</a>
        <a class="button button-blue" style="float:left;" href="/index.php?call=runer.spage_block_create&page_id={:$page_id:}">
			<i class="fas fa-plus"></i> 新增模块
		</a>
    </caption>
	<tr>
		<th>ID</th>
		<th>模块名称</th>
		<th>模块代号</th>
		<th>模块类型</th>
		<th>尺寸模式</th>
		<th>模块宽度</th>
		<th>模块高度</th>
		<th>数据容量</th>
		<th>数据源</th>
		<th>操作</th>
	</tr>
	{:foreach from=$Result->Data value=v:}
		<tr>
			<td>{:$v.id:}</td>
			<td><a href="/index.php?call=runer.spage_block_update&id={:$v.id:}&page_id={:$page_id:}">{:$v.name:}</a></td>
			<td>{:$v.alias:}</td>
			<td>{:$v.source_type:}</td>
			<td>{:$v.sizer:}</td>
			<td>{:$v.width:}</td>
			<td>{:$v.height:}</td>
			<td>{:$v.capacity:}</td>
			<td>{:if $v.source_id == 0:}请选择{:/if:}<a href="/index.php?call=runer.source_update&id={:$v.source_id:}">{:$v.source_name:}</a> <i class="fas fa-edit" onclick="choice_source({:$v.id:})"></i> </td>
			<td>
				<a href="javascript:void(0);" onclick="javascript:drop_press({:$v.id:})" style="color:red;"><i class="fas fa-times"></i>删除</a> | 
				<a href="/index.php?call=runer.source_create&block_id={:$v.id:}" style="color:blue;"><i class="fas fa-edit"></i>新数据源</a> |
				<a href="javascript:void(0);" onclick="javascript:_do_preview({:$v.id:},{:$page_id:});" style="color:blue;"><i class="fas fa-search"></i>预览</a> 
			</td>
		</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="10">
			{:include file="_g/pager.tpl" Result=$Result:}
		</td>
	</tr>
</table>
<input type="hidden" id="block_id"/>
<div id="mb"></div>
<form class="form_add add_content" id="source_choice" name="form1" action="/teach.php?call=examination.examination_create_do" method="post">
	<h3 style="margin-bottom:20px;"><i class="fas fa-shield "></i>数据源选择</h3>
	<label>名称:</label>
	<label>
		<select id="source_show" style="width:200px;height:30px;background-color:#fff ">
		</select>
	</label>
	<input id="cancel" type="button" value="取 消" onclick="javascript:stop()">
	<input class="sub" name="ok" type="button" id="buttonss" value="提 交" onclick="javascript:save()">
</form>
<script type="text/javascript">
function save(){
	var id = $('#block_id').val();
	var source = $('#source_show').val();
	YueMi.API.Admin.invoke('runer', 'block_add_source', {
		__access_token : '{:$User->token:}',
		block_id : id,
		source_id: source
	}, 
	function (t, r, q) {
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
function choice_source(id){
	tankuang();
	$('#block_id').val(id)
}
function tankuang() {
    $('#mb').toggle();
    $('#source_choice').toggle();
    if ($(window).width() < 800) {
        $(window).width(800);
    }
    var left = $(window).width() / 2 - $('.form_add').width() / 2;
    var top = $(window).height() / 2 - $('.form_add').height() / 2;
    $('.form_add').css({ 'left': left, 'top': top });
	
	load_source()
}
function stop() {
    $('#mb').toggle();
    $('#source_choice').toggle();
}
function load_source(){
	YueMi.API.Admin.invoke('runer', 'source_load', {
	__access_token : '{:$User->token:}',
		}, 
	function (t, r, q) {
		if (q.__code === 'OK')
		{
			var str = '<option value="0" checked="checked">请选择</option>';
			$.each(q.Result,function(key,val){
				str += '<option value="'+val.id+'">'+val.name+'</option>';
			});
			$('#source_show').html(str);
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
						YueMi.API.Admin.invoke('runer', 'spage_block_del', {
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
