{:include file="_g/header.tpl" Title="库存/品类":}

<table cellspacing="0" cellpadding="0" class="Grid">
    <caption>
        {:if $ParentCatagory:}{:$ParentCatagory->name:}{:/if:} 品类管理
        <a class="button button-blue" style="float:left;" href="/index.php?call=depot.catagory_create&clid={:if $ParentCatagory !== null:}{:$ParentCatagory->id:}{:else:}0{:/if:}"> <i class="fa fa-plus"></i> 新增品类 </a>
    </caption>
    <tr>
        <th rowspan="2">编号</th>
        <th rowspan="2">名称</th>
        <th rowspan="2">负责人</th>
        <th colspan="2">毛利风控</th>
        <th rowspan="2">平台佣金</th>
        <th colspan="3">标志位</th>
        <th rowspan="2">商家专区</th>
        <th rowspan="2">排序权重</th>
        <th rowspan="2">操作</th>
    </tr>
    <tr>
        <th>死线</th>
        <th>告警</th>
        <th>隐藏</th>
        <th>内部</th>
        <th>专区</th>
    </tr>
    {:if $ParentCatagory:}
    <tr>
        <td align="center">
            <i class="fa fa-reply"></i>
        </td>
        <td colspan="11">
            <a href="/index.php?call=depot.catagory&clid={:$ParentCatagory->parent_id:}">
                返回上级
            </a>
        </td>
    </tr>
    {:/if:}
    {:foreach from=$Result->Data item=S:}
    <tr>
        <td>{:$S.id:}</td>
        <td><a href="/index.php?call=depot.catagory&clid={:$S.id:}">{:$S.name:}</a></td>
        <td align="center">{:$S.manager_name:}</td>
        <td>{:$S.gratio_dead | number.percent 2:} </td>
        <td>{:$S.gratio_warn | number.percent 2:} </td>
        <td>{:$S.rratio_system | number.percent 2:} </td>
        <td align="center"><a href="/index.php?call=depot.changehidden&id={:$S.id:}&val={:$S.is_hidden:}">{:$S.is_hidden | boolean.iconic:}</a></td>
        <td align="center"><a href="/index.php?call=depot.changeinternal&id={:$S.id:}&val={:$S.is_internal:}">{:$S.is_internal | boolean.iconic:}</a></td>
        <td align="center"><a href="/index.php?call=depot.changeprivate&id={:$S.id:}&val={:$S.is_private:}">{:$S.is_private | boolean.iconic:}</a></td>
        <td align="center">{:$S.supplier_name:}</td>
        <td align="center">{:$S.p_order:}</td>
        <td class="operator">
			<a href="javascript:void(0);" onclick="javascript:drop_press({:$S.id:})">删除</a>
            <a href="/index.php?call=depot.spu&clid={:$S.id:}">SPU</a>
            <a href="/index.php?call=depot.sku&clid={:$S.id:}">SKU</a>
            <a href="/index.php?call=mall.shelf&clid={:$S.id:}">已上架</a>
			<a href="/index.php?call=depot.catagory_updata&cid={:$S.id:}">修改</a>
        </td>
    </tr>
    {:/foreach:}
    <tr class="paging">
        <td colspan="12">{:include file="_g/pager.tpl" Result=$Result:}</td>
    </tr>
</table>
	
	<xxx  xxx="">
		
	</xxx>
	
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
            title: '删除品类',
            content: '删除吗？',
            buttons: {
                accept: {
                    btnClass: 'btn-red',
                    text: '删除',
                    action: function () {
                        API_ADMIN.invoke('depot','del',{
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
