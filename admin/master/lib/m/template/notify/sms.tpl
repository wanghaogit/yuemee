{:include file="_g/header.tpl" Title="短信":}
<table border="0" cellspacing="0" cellpadding="0" class="Grid">
    <tr>
        <th>短信通知ID</th>
        <th>所属用户ID</th>
        <th >目标手机号</th>
        <th>短信验证码</th>
        <th>短信内容</th>
        <th>回执ID</th>
        <th>发送时间</th>
        <th>过期时间</th>
    </tr>
    {:foreach from=$Result->Data value=v:}
    <tr>
        <td>{:$v.id:}</td>
        <td>{:$v.user_id:}</td>
        <td>{:$v.mobile:}</td>
        <td>{:$v.code:}</td>
        <td>{:$v.message:}</td>
        <td>{:$v.biz_id:}</td>
        <td>{:$v.create_time:}</td>
        <td>{:$v.expire_time:}</td>
    </tr>
    {:/foreach:}
	<tr class="paging">
        <td colspan="9">
            {:include file="_g/pager.tpl" Result=$Result:}
        </td>
    </tr>
</table>
{:include file="_g/footer.tpl":}