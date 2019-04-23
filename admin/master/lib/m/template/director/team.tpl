{:include file="_g/header.tpl" Title="团队":}
<table cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		团队管理
		<a class="button button-blue" style="float:left;" href="/index.php?call=director.team_create"> <i class="fas fa-plus"></i> 创建团队 </a>
	</caption>
	<tr>
		<th rowspan="2">编号</th>
		<th rowspan="2">名称</th>
		<th colspan="3">总经理</th>
		<th rowspan="2">创建人</th>
		<th rowspan="2">创建时间</th>
		<th rowspan="2">操作</th>
	</tr>
	<tr>
		<th>会员</th>
		<th>VIP</th>
		<th>经理</th>
	</tr>
	{:foreach from=$Result->Data item=S:}
		<tr>
			<td>{:$S.id:}</td>
			<td>{:$S.name:}</td>
			<td>
				{:$S.Mobile:}
			</td>
			<td>
				{:$S.v_expiretime:}
			</td>
			<td>
				{:$S.d_expiretime:}
			</td>
			<td>
				{:$S.create_user:}
			</td>
			<td>{:$S.create_time:}</td>
			<td>
				<a href="javascript:void(0);" onclick="javascript:do_drop('{:$S.id:}');">删除</a>
			</td>
		</tr>
	{:/foreach:}
		<tr>
			<td colspan="10">{:include file="_g/pager.tpl" Result=$Result:}</td>
		</tr>
</table>
<script>
    function do_drop(id) {
        $.confirm({
            useBootstrap: false,
            type: 'blue',
            boxWidth: '300px',
            escapeKey: 'cancel',
            backgroundDismiss: false,
            backgroundDismissAnimation: 'glow',
            icon: 'fa fa-shield',
            title: '删除团队',
            content: '删除吗？',
            buttons: {
                accept: {
                    btnClass: 'btn-red',
                    text: '删除',
                    action: function () {
                        YueMi.API.Admin.invoke('team','del',{
                                id : id
                        },function(t,r,q){
                          if(q.__code === 'OK')
                          {
                               location.reload();
                          }else{
                              alert(q.__message);
                          }
                        },function(t,r,q){
                            
                        });
                  
                    }
                },
                cancel: {text: '取消', btnClass: 'btn-blue', action: function () {}}
            }
        });
    }
</script>
{:include file="_g/footer.tpl":}
