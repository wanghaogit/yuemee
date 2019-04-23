{:include file="_g/header.tpl" Title="通知/编辑":}
<script type="text/javascript" src="/scripts/editor.js"></script>
<script type="text/javascript" src="{:#URL_RES:}/v1/scripts/calendar.js"></script>
<form action="/index.php?call=notify.notice_edit" method="post" name="form1">
<ul>
    <li><b>修改公告</b></li>
    <label>公告标题</label>
    <input type="text"  name="title" value="{:$Result.title:}" id="title" style="margin-left: 10px;"/>
    <input type="hidden" name="id" value="{:$Result.id:}" id="gid"/>
    <li> 
        <label style="float: left;">公告内容：</label><br>
        
		<div id="content" name="content"></div>
		<script type="text/javascript">
			var e = new window.wangEditor('#content');
			e.create();
			e.txt.html('{:$Result.content | string.js_format:}');
		</script>
		<input type="hidden" id="cca" name="content"/>
     </li>
    <li>
    <label>公告范围：</label>
    <select id="scope_id" name="scope_id"/>
        <option value="0">{:$Result.scope | array.enum ['全体','非VIP','VIP','总监','经理','供应商','员工','管理员']:}</option>
        <option value="1">非VIP</option>
        <option value="2">VIP</option>
        <option value="3">总监</option>
        <option value="4">经理</option>
        <option value="5">供应商</option>
        <option value="6">员工</option>
    </select>
    </li>
<li>
    <label>公开时间：</label>
    <input type="text" id="open_time" name="open_time"  class="input-date" readonly="readonly" value="{:$Result.open_time:}" />
</li>
<li>
    <label>关闭时间：</label>
    <input type="text" id="close_time" name="close_time" class="input-date" readonly="readonly" value="{:$Result.close_time:}" />
</li>
<input type="button" value="保存" onclick="javascript:sub();"/>
</ul>
</form>
<script type="text/javascript">
	function sub(){
		var html = e.txt.html();
		$('#cca').val(html);
		document.form1.submit();
	}
	 $(".input-date").datetimepicker({
		 autoclose : true,
		 clearBtn : true,
		 todayBtn : true,
		 todayHighlight : true,
		 fontAwesome : true,
		 zIndex : 9999,
		 format: 'yyyy-mm-dd hh:ii'
	 });
    function xiugai() {
        var title = $("#title").val();
        var content=$("#content").text().trim();
		console.log(content);
        var id = $("#gid").val();
        var scope=$("#scope_id").find("option:selected").val();
        var status=$("input[type='radio']:checked").val();
        var open_time=$("#open_time").val();
        var close_time=$("#close_time").val();
        $.confirm({
            useBootstrap: false,
            type: 'blue',
            boxWidth: '300px',
            escapeKey: 'cancel',
            backgroundDismiss: false,
            backgroundDismissAnimation: 'glow',
            icon: 'fa fa-shield',
            title: '修改公告',
            content: '确认修改吗？',
            buttons: {
                accept: {
                    btnClass: 'btn-red',
                    text: '修改',
                    action: function () {
                        YueMi.API.Admin.invoke('notify', 'update', {
                            title: title,
                            id: id,
                            content:content,
                            scope:scope,
                            status:status,
                            open_time:open_time,
                            close_time:close_time
                            
                        }, function (t, r, q) {
                            if (q.__code === 'OK')
                            {
                                window.location.href = "/index.php?call=notify.index";
                            } else {
                                alert(222);
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

