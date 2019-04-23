{:include file="_g/header.tpl" Title="运营/APP":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
	<caption>
        {:if $ParentCatagory:}{:$ParentCatagory.name:}{:/if:} 应用内管理
        <a class="button button-blue" style="float:left;" href="/index.php?call=runer.dpage_create&parent_id={:if $ParentCatagory !== null:}{:$ParentCatagory.id:}{:else:}0{:/if:}">
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
            <a href="/index.php?call=runer.dpage&parent_id={:$ParentCatagory.parent_id:}">
                返回上级
            </a>
        </td>
    </tr>
    {:/if:}
	{:foreach from=$Result->Data value=v:}
	<tr>
		<td>{:$v.id:}</td>
		<td>
			<a href="/index.php?call=runer.dpage_block&page_id={:$v.id:}">{:$v.name:}</a>
			<i class="fas fa-edit" onclick="window.location.href='/index.php?call=runer.dpage_update&id={:$v.id:}';"></i>
		</td>
		<td>{:$v.alias:}</td>
		<td>
			<a href="javascript:void(0);" onclick="javascript:drop_press({:$v.id:})" style="color:red;"><i class="fas fa-times"></i>删除</a> | 
			<a href="javascript:void(0);" onclick="javascript:build_html({:$v.id:})" style="color:blue;">生成</a> 
		</td>
	</tr>
	{:/foreach:}
	<tr class="paging">
		<td colspan="9">
			{:include file="_g/pager.tpl" Result=$Result:}
		</td>
	</tr>
</table>
		<input type="hidden" id="run_release_id"/>		
<script type="text/javascript">
		
function win_open(){
	var id = $('#run_release_id').val();
	YueMi.API.Admin.invoke('runer', 'get_re_html', {
		__access_token : '{:$User->token:}',
		id : id
	}, 
	function (t, r, q) {
		if (q.__code === 'OK')
		{
			var win = window.open('','preview','width=750,height=600,toolbar=no,menubar=no,resizable=no,fullscreen=no,scrollbars=yes');
			win.document.write(q.Html);
		} else {
			console.log(q);
			alert(q.__message);
		}
	}, function (t, r, q) {
		alert(q.__message);
	});
}
	function build_html(id){
		YueMi.API.Admin.invoke('runer','build_html',{
				__access_token : '{:$User->token:}',
				id : id
		},function(t,r,q){
			if(q.__code === 'OK')
			{
				$('#run_release_id').val(q.rid);
			win_open();
			}else{
				console.log(q);
				alert(q.__message);
			}
		},function(t,r,q){
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
                        YueMi.API.Admin.invoke('runer','dpage_del',{
								__access_token : '{:$User->token:}',
                                id : id
                        },function(t,r,q){
                          if(q.__code === 'OK')
                          {
                               location.reload();
                          }else{
							  console.log(q);
                              alert(q.__message);
                          }
                        },function(t,r,q){
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
