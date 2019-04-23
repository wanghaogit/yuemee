{:include file="_g/header.tpl" Title="库存/品类":}
<table cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		优惠券管理
	</caption>
	<tr>
		<th>优惠券ID</th>
		<th>供应商ID</th>
		<th>面额</th>
		<th>发行数量</th>
		<th>操作</th>
	</tr>
	{:foreach from=$data->Data item=S:}
		<tr>
			<td>{:$S.id:}</td>
			<td>{:$S.name_1:}</td>
			<td>{:$S.money:}</td>
			<td>{:$S.quantity:}</td>
			<td align="center"> <a href="javascript:void(0);" onclick="javascript:drop_press('{:$S.id:}')">删除</a></td>
		</tr>
	{:/foreach:}
		<tr class="paging">
			<td colspan="12">{:include file="_g/pager.tpl" Result=$data:}</td>
		</tr>
</table>
<script type="text/javascript">

    var API_ADMIN = new Invoker({
        udid: '000000000000000000000000',
        url: 'http://z.ym.cn/api.php',
        applet_token: 'b31ed652c66e11b41b6f7378',
        access_token: function () {
            var m = /\buser\_token\=([a-z0-9]+)\b/i.exec(document.cookie);
            if (m && m.length > 0) {
                return m[1];
            }
            return '';
        }
    });
    
    function drop_press(id) {
        $.confirm({
            useBootstrap: false,
            type: 'blue',
            boxWidth: '300px',
            escapeKey: 'cancel',
            backgroundDismiss: false,
            backgroundDismissAnimation: 'glow',
            icon: 'fa fa-shield',
            title: '删除优惠',
            content: '删除吗？',
            buttons: {
                accept: {
                    btnClass: 'btn-red',
                    text: '删除',
                    action: function () {
                        API_ADMIN.invoke('mall','ticket_del',{
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
