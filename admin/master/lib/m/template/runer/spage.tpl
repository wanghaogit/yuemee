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
	{:if $ParentCatagory:}{:$ParentCatagory.name:}{:/if:} 应用内管理
	<a class="button button-blue" style="float:left;" onclick="add_page({:$parent_id:})">
		<i class="fas fa-plus"></i> 新增页面配置
	</a>
</caption>
<tr>
	<th>ID</th>
	<th>页面名称</th>
	<th>页面代号</th>
	<th>操作</th>
</tr>
{:if $ParentCatagory:}
    <tr>
        <td align="center">
            <i class="fas fa-reply"></i>
        </td>
        <td colspan="3">
            <a href="/index.php?call=runer.spage&parent_id={:$ParentCatagory.parent_id:}">
                返回上级
            </a>
        </td>
    </tr>
{:/if:}
{:foreach from=$Result->Data value=v:}
	<tr>
		<td>{:$v.id:}</td>
		<td>
			<a href="/index.php?call=runer.spage_block&page_id={:$v.id:}" title="页面模块配置">{:$v.name:}</a>
			<i class="fas fa-edit" onclick="update_page({:$v.id:})"></i>
		</td>
		<td>{:$v.alias:}</td>
		<td>
			<a href="javascript:void(0);" onclick="javascript:drop_press({:$v.id:})" style="color:red;"><i class="fas fa-times"></i>删除</a> | 
			<a href="/index.php?call=runer.spage&parent_id={:$v.id:}" style="color:blue;">子页面</a>
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
<form class="form_add add_content" id="update_page" name="form1" action="/teach.php?call=examination.examination_create_do" method="post">
	<h3 style="margin-bottom:20px;"><i class="fas fa-shield "></i>编辑页面</h3>
	<label style="margin-left:15px;margin-bottom: 10px;">
		名称:<input name="name" id="update_name" style="width: 200px;height: 20px; margin-left: 4px;outline: none; height: 30px;border: 1px solid #A3A3A3; border-radius: 3px;"/>
	</label>
	<input type="hidden" id="parent_id" name="parent_id"/>
	<input id="cancel" type="button" value="取 消" onclick="javascript:stop()">
	<input class="sub" name="ok" type="button" id="buttonss" value="提 交" onclick="javascript:save()">
</form>

<form class="form_add add_content" id="add_page" name="form1" action="/teach.php?call=examination.examination_create_do" method="post">
	<h3 style="margin-bottom:20px;"><i class="fas fa-shield "></i>编辑页面</h3>
	<label style="display: block;margin:15px;">
		名称:<input name="name" id="add_name" style="width: 220px;height: 20px;outline: none; height: 30px;border: 1px solid #A3A3A3; border-radius: 3px;"/>
	</label>
	<label style="display: block;margin:15px;">
		别名:<input name="name" id="add_alias" style="width: 220px;height: 20px;outline: none; height: 30px;border: 1px solid #A3A3A3; border-radius:3px;"/>
	</label>
	<input id="cancel" type="button" value="取 消" onclick="javascript:stop1()">
	<input class="sub" name="ok" type="button" id="buttonss" value="提 交" onclick="javascript:save_add()">
</form>
<input type="hidden" id="update_id"/>
<script type="text/javascript">
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
						YueMi.API.Admin.invoke('runer', 'spage_del', {
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
	

function tankuang() {
    $('#mb').toggle();
    $('#update_page').toggle();
    if ($(window).width() < 800) {
        $(window).width(800);
    }
    var left = $(window).width() / 2 - $('.form_add').width() / 2;
    var top = $(window).height() / 2 - $('.form_add').height() / 2;
    $('.form_add').css({ 'left': left, 'top': top });
}
function tankuang1() {
    $('#mb').toggle();
    $('#add_page').toggle();
    if ($(window).width() < 800) {
        $(window).width(800);
    }
    var left = $(window).width() / 2 - $('.form_add').width() / 2;
    var top = $(window).height() / 2 - $('.form_add').height() / 2;
    $('.form_add').css({ 'left': left, 'top': top });
}

function update_page(id) {
    tankuang();
	$('#update_id').val(id);
}
function add_page(id) {
    tankuang1();
	$('#parent_id').val(id);
}
function stop() {
    $('#mb').toggle();
    $('#update_page').toggle();
}
function stop1() {
    $('#mb').toggle();
    $('#add_page').toggle();
}
function save(){
	var id = $('#update_id').val();
	var name = $('#update_name').val();
	YueMi.API.Admin.invoke('runer', 'spage_update', {
		id: id,
		name :name
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
function save_add(){
	var alias = $('#add_alias').val();
	var name = $('#add_name').val();
	var parent_id = $('#parent_id').val();
	YueMi.API.Admin.invoke('runer', 'spage_add', {
		__access_token : '{:$User->token:}',
		alias: alias,
		name :name,
		parent_id:parent_id
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
</script>
{:include file="_g/footer.tpl":}
