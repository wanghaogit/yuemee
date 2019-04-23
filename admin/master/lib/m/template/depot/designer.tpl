{:include file="_g/header.tpl" Title="库存/设计师":}
<table cellspacing="0" cellpadding="0" class="Grid">
	<caption>
		设计师管理
		<a class="button button-blue" style="float:left;" href="/index.php?call=depot.designer_create"> <i class="fas fa-plus"></i> 新增设计师 </a>
	</caption>
	<tr>
		<th>编号</th>
		<th>姓名</th>
		<th >操作</th>
	</tr>
	{:foreach from=$Result->Data item=S:}
		<tr>
			<td>{:$S.id:}</td>
			<td>{:$S.name:}</td>
			<td>
				<a href="javascript:void(0);" onclick="javascript:drop_press({:$S.id:});">删除</a> |
				<a href="/index.php?call=depot.spu&did={:$S.id:}">SPU</a> |
				<a href="/index.php?call=depot.sku&did={:$S.id:}">SKU</a> |
				<a href="/index.php?call=mall.shelf&bid={:$S.id:}">已上架</a>
			</td>
		</tr>
	{:/foreach:}
		<tr>
			<td colspan="10">{:include file="_g/pager.tpl" Result=$Result:}</td>
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
            title: '删除设计师',
            content: '删除吗？',
            buttons: {
                accept: {
                    btnClass: 'btn-red',
                    text: '删除',
                    action: function () {
                        API_ADMIN.invoke('depot','designer_del',{
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
