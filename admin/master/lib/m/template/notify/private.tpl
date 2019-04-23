{:include file="_g/header.tpl" Title="私信":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
    <caption>
        私信
    </caption>

    <tr>
        <th>邮件ID</th>
        <th>发送人ID</th>
        <th>发送人昵称</th>
        <th>接收人ID</th>
        <th>接收人昵称</th>
        <th>公告标题</th>
        <th>公告内容</th>
        <th>邮件状态</th>
        <th>发布时间</th>
        <th>发布IP</th>
        <th>阅读时间</th>
        <th>阅读IP</th>
        <th>操作</th>

    </tr>
    {:foreach from=$Result->Data item=R:}
    <tr>
        <td>{:$R.id:}</td>
        <td>{:$R.sender_id:}</td>
        <td>{:$R.sender_name:}</td>
        <td align="center">{:$R.reciver_id:}</td>
        <td align="center">{:$R.reciver_name:}</td>
        <td align="right">{:$R.title:}</td>
        <td align="right">{:$R.content:}</td>
        <td align="right">{:$R.status| array.enum ['草稿','待审','发布','关闭']:}</td>
        <td align="center">{:$R.create_time:}</td>
        <td align="center">{:$R.create_from:}</td>
        <td align="center">{:$R.recive_time:}</td>
        <td>{:$R.recive_from:}</td>
        <td><a href="javascript:void(0);" onclick="javascript:drop_press('{:$R.id:}')">删除</a></td>
    </tr>
    {:/foreach:}


</table>
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
            title: '删除公告',
            content: '删除吗？',
            buttons: {
                accept: {
                    btnClass: 'btn-red',
                    text: '删除',
                    action: function () {
                        YueMi.API.Admin.invoke('notify', 'private_del', {
                            id: id
                        }, function (t, r, q) {
                            if (q.__code === 'OK')
                            {
                                location.reload();
                            } else {
                                alert(q.__message);
                            }
                        }, function (t, r, q) {

                        });

                    }
                },
                cancel: {text: '取消', btnClass: 'btn-blue', action: function () {}}
            }
        });
    }

</script>
{:include file="_g/footer.tpl":}
