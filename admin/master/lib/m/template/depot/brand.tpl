{:include file="_g/header.tpl" Title="库存/品牌":}
<table cellspacing="0" cellpadding="0" class="Grid">
    <caption>
        <a class="button button-blue" style="float:left;" onclick="newone()"> <i class="fas fa-plus"></i> 新增品牌 </a>
    </caption>
    <tr>
        <td>查询</td>
        <td colspan="23">
            <form method="GET" action="/index.php">
                <input type="hidden" name="call" value="{:#Z_HANDLER:}.{:#Z_ACTION:}" />
                <input type="hidden" name="p" value="{:$_PARAMS.p:}" />

                名称：<input type="text"  name="na" value="{:$_PARAMS.na:}" />
                英文名：<input type="text"  name="e" value="{:$_PARAMS.e:}" />
                供应商名名称：<input type="text"  name="sn" value="{:$_PARAMS.sn:}" />
                <input type="submit" value="查询" />
            </form>
        </td>
    </tr>
    <tr>
        <th>编号</th>
        <th>名称</th>
        <th>英文名</th>
        <th>logo</th>
        <th>所属供应商</th>
        <th>操作</th>
    </tr>
    {:foreach from=$Result->Data item=S:}
    <tr>
        <td>{:$S.id:}</td>
        <td>{:$S.name | string.key_highlight $_PARAMS.na:}</td>
        <td>{:$S.alias | string.key_highlight $_PARAMS.e:}</td>
        <td>
            {:if $S.logo:}
            <img  src='{:$S.logo:}'  style="width: 60px;height: 60px;"/>
            {:/if:}
        </td>
        <td>{:$S.sname | string.key_highlight $_PARAMS.sn:}</td>
        <td>
            <a class="upload_img" data-id="{:$S.id:}"> 上传 </a> |
            <a href="/index.php?call=depot.update_brand&id={:$S.id:}">修改</a> |
            <a onclick="delete2({:$S.id:})" style="color:red;">删除</a>
        </td>
    </tr>
    {:/foreach:}
    <tr>
        <td colspan="10">{:include file="_g/pager.tpl" Result=$Result:}</td>
    </tr>
</table>
<script type="text/javascript">
    function delete2(id) {
        YueMi.API.Admin.invoke('depot', 'delete_brand', {
            __access_token: '{:$User->token:}',
            id: id
        }, function (t, q, r) {
            location.reload();
        }, function (t, q, r) {
            alert('删除失败');
        });
    }
    function extsku() {
        alert("待开发...");
    }

    $(".upload_img").click(function (ev) {
        var id = $(this).attr('data-id');

        var oInput = document.createElement("input");
        oInput.type = "file";
        oInput.click();
        oInput.addEventListener("change", function () {
            var oFile = this.files[0];
            if (!/image\/\w+/.test(oFile.type)) {
                alert("请确保文件为图像类型");
                return false;
            }
            var fileSize = this.files[0].size;
            fileSize = Math.round(fileSize / 1000 * 200) / 100;                       //判断图片大小是否符合规范
            if (fileSize >= 100) {
                alert('照片最大尺寸大于100k，请重新上传!');
                return false;
            }
            var render = new FileReader();
            render.readAsDataURL(oFile);
            render.onload = function (e) {
                var event = this;
                console.log(event.result);
                YueMi.API.Admin.invoke('depot', 'brand_logo', {
                    id: id,
                    logo: event.result,

                }, function (t, r, q) {
                    if (q.__code === 'OK') {
                        location.reload();
                    } else {
                        alert(q.__message);
                    }
                    //成功
                }, function (t, q, r) {
                    //失败
                });
            }
        });
    });

    function newone() {
        $.confirm({
            useBootstrap: false,
            type: 'blue',
            boxWidth: '600px',
            escapeKey: 'cancel',
            backgroundDismiss: false,
            backgroundDismissAnimation: 'glow',
            icon: 'fas fa-shield',
            title: '新增品牌',
            content: '正在加载...',
            onContentReady: function () {
                ____generate_kuaidi_list(this);
            },
            buttons: {
                accept: {
                    btnClass: 'btn-red',
                    text: '添加',
                    action: function () {
                        var sname = $('#supplier').val();
                        var name = $('#brand_name').val();
                        var aname = $('#brand_alias').val();
                        if (name == '') {
                            alert('请输入品牌名称');
                            exit;
                        }
                        YueMi.API.Admin.invoke('depot', 'new_brand', {
                            __access_token: '{:$User->token:}',
                            supplier: sname,
                            brand_name: name,
                            brand_alias: aname
                        }, function (t, q, r) {
                            if (r.msg == 'OK') {
                                location.reload();
                            } else {
                                alert(r.msg);
                            }
                        }, function (t, q, r) {
                            alert('添加失败');
                        });
                    }
                },
                cancel: {
                    text: '取消',
                    btnClass: 'btn-blue',
                    action: function () {

                    }
                }
            }
        });
    }



    function ____generate_kuaidi_list(self) {
        YueMi.API.Admin.invoke('depot', 'supplier_list', {
            __access_token: '{:$User->token:}'
        }, function (t, q, r) {
            var str = ' 供应商名： ';
            str += '<select id="supplier">';
            $.each(r.res, function (k, v) {
                str += '<option value="' + v['id'] + '">' + v['name'] + '</option>';
            });
            str += '</select> <br/><br/>';
            str += ' 品牌名称： <input type="text" id="brand_name" /> <br/><br/> ';
            str += ' 英文名称： <input type="text" id="brand_alias" /> <br/><br/> ';
            self.setContent(str);
        }, function (t, q, r) {
            self.setContent("加载失败，请关闭对话框重试一次");
        });


    }


</script>
{:include file="_g/footer.tpl":}
