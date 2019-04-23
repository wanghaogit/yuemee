{:include file="_g/header.tpl" Title="用户":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		微信授权管理
	</caption>
	<tr>
		<td>搜索</td>
		<td colspan="14">
			<form action="/index.php" method="GET">
				<input type="hidden" name="call" value="{:#Z_HANDLER:}.{:#Z_ACTION:}" />
				<input type="hidden" name="p" value="{:$_PARAMS.p:}" />
				手机号：<input type="text" class="input-mobile" name="m" value="{:$_PARAMS.m:}" />
				昵称：<input type="text"  name="n" value="{:$_PARAMS.n:}" />
				<input type="submit" value="搜索" />
			</form>
		</td>
	</tr>
	<tr>
		<th rowspan="2">ID</th>
		<th rowspan="2">头像</th>
		<th colspan="2">来源</th>
		<th rowspan="2">手机号码</th>
		<th rowspan="2">微信账号</th>
		<th rowspan="2">昵称</th>
		<th rowspan="2">性别</th>
		<th rowspan="2">地区</th>
		<th colspan="5">推荐人</th>
		<th rowspan="2">更新时间</th>
	</tr>
	<tr>
		<th>APP</th>
		<th>公众号</th>
		<th>推荐人</th>
		<th>团队</th>
		<th>一线员工</th>
		<th>裂变种子</th>
		<th>裂变参数</th>
	</tr>
	{:foreach from=$data->Data value=v:}
	<tr>
		<td class="zid" title="{:$v.union_id:}">{:$v.id:}</td>
		<td align="center">{:if $v.avatar:}
			<img src="{:$v.avatar:}" style="width:48px;height:48px;" alt="" />
		{:else:}
			无
		{:/if:}</td>
		<td align="center" title="{:$v.app_open_id:}">{:$v.app_open_id | boolean.iconic:}</td>
		<td align="center" title="{:$v.web_open_id:}">{:$v.web_open_id | boolean.iconic:}</td>
		<td align="center">
			<a href="javascript:void(0);" title="登记手机号码" onclick="javascript:_do_edit_mobile({:$v.id:});" style="font-size:14px;">
				<i class="fas fa-mobile-alt"></i>
			</a>
			{:$v.mobile | string.key_highlight $_PARAMS.m:}
		</td>
		<td>
			<a href="javascript:void(0);" title="登记微信账号" onclick="javascript:_do_edit_account({:$v.id:});" style="font-size:14px;">
				<i class="fas fa-user"></i>
			</a>
			{:$v.account:}
		</td>
		<td>{:$v.name | string.key_highlight $_PARAMS.n:}</td>
		<td align="center">{:$v.gender | array.enum ['-','男','女']:}</td>
		<td align="center">{:$v.province:}-{:$v.city:}-{:$v.country:}</td>
		<td align="center">{:$v.invname:}</td>
		<td align="center">
			
		</td>
		<td align="center">
			
		</td>
		<td align="center">{:$v.tag_seed:}</td>
		<td align="center">{:$v.tag_param:}</td>
		<td>
			
			{:$v.create_time | number.datetime:}<br/>
			{:$v.update_time | number.datetime:}
		</td>
	</tr>
	{:/foreach:}
	<tr class="pager">
		<td colspan="16">
			{:include file="_g/pager.tpl" Result=$data:}
		</td>
	</tr>
</table>
<script type="text/javascript">
	function _do_edit_mobile(id){
        $.confirm({useBootstrap: false,type: 'blue',boxWidth: '300px',escapeKey: 'cancel',backgroundDismiss: false,backgroundDismissAnimation: 'glow',
            icon: 'fas fa-mobile-alt',
            title: '录入手机号码',
            content: '手机号码：<input type="text" class="input-mobile" maxlength="11" id="dlg_input_mobile" />',
            buttons: {accept: {
				btnClass: 'btn-red',
				text: '保存',
				action: function () {
					var v = $('#dlg_input_mobile').val().trim();
					if(! /^1\d{10}$/.test(v)){
						$('#dlg_input_mobile').focus().select();
						return false;
					}else{
						YueMi.API.Admin.invoke('user','set_wechat_mobile',{
							wxid : id,
							mobile : v
						},function(t,r,q){location.reload();},function(t,r,q){alert(q.__message);});
						return;
					}
				}},cancel: {text: '取消', btnClass: 'btn-blue', action: function () {}}
		}});
	}
	function _do_edit_account(id){
        $.confirm({useBootstrap: false,type: 'blue',boxWidth: '300px',escapeKey: 'cancel',backgroundDismiss: false,backgroundDismissAnimation: 'glow',
            icon: 'fas fa-user',
            title: '录入微信账号',
            content: '微信账号：<input type="text" class="input-account" maxlength="64" id="dlg_input_account" size="32" />',
            buttons: {accept: {
				btnClass: 'btn-red',
				text: '保存',
				action: function () {
					var v = $('#dlg_input_account').val().trim();
					if(! /^[a-z0-9\_]{5,64}$/.test(v)){
						$('#dlg_input_account').focus().select();
						return false;
					}else{
						YueMi.API.Admin.invoke('user','set_wechat_account',{
							wxid : id,
							account : v
						},function(t,r,q){location.reload();},function(t,r,q){alert(q.__message);});
						return;
					}
				}},cancel: {text: '取消', btnClass: 'btn-blue', action: function () {}}
		}});
	}
</script>
{:include file="_g/footer.tpl":}
