<?php

include "lib/ApiHandler.php";

/**
 * 通知管理接口
 */
class notify_handler extends ApiHandler {

    function __construct(\Ziima\MVC\Context $ctx) {
        parent::__construct($ctx);
    }

    /**
     * 系统公告删除
     * @param \Ziima\MVC\REST\Request $request
     * @request    id     char     公告id
     * @slient
     */
    public function del(\Ziima\MVC\REST\Request $request) {
        $id = $request->body->id;
        $sql = "DELETE FROM `yuemi_main`. `notice` WHERE id = '" . $id . "'";
        $this->MySQL->execute($sql);

        return [
            '__code' => 'OK',
            '__message' => '删除成功'
        ];
    }
    /**
     * 私信删除
     * @param \Ziima\MVC\REST\Request $request
     * @request    id     char     私信id
     * @slient
     */
    public function private_del(\Ziima\MVC\REST\Request $request)
    {
        $id = $request->body->id;
        $sql = "DELETE FROM `yuemi_main`. `mail` WHERE id = '" . $id . "'";
        $this->MySQL->execute($sql);
        return [
            '__code' => 'OK',
            '__message' => '删除成功'
        ];
        
    }

    /**
     * 系统公告修改
     * @param \Ziima\MVC\REST\Request $request
     * @request    id           char           公告id
     * @request    title        varchar        公告标题
     * @request    scope        tinyint        公告范围
     * @request    content      text           公告内容
     * @request    open_time    datetime       公开时间
     * @request    close_time   datetime       关闭时间
     */
    public function update(\Ziima\MVC\REST\Request $request) {
        $title = $request->body->title;
        $id = $request->body->id;
        $scope_id=$request->body->scope;
        $open_time=$request->body->open_time;
        $close_time=$request->body->close_time;
        $content=$request->body->content;
        $sql = "UPDATE `yuemi_main`.`notice` SET `status` = 1, title = '" . $title . "',content = '" . $content . "',scope = '" . $scope_id ."',open_time='" . $open_time . "',close_time='" . $close_time . "',close_time='" . $close_time . "'  WHERE  id='" . $id . "'";
       
        if ($this->MySQL->execute($sql)) {
            return [
                '__code' => 'OK',
                '__message' => '编辑成功'
            ];
        }
        return [
            '__code' => 'error',
            '__message' => '编辑失败'
        ];
    }

    /**
     * 系统公告关闭
     * @param \Ziima\MVC\REST\Request $request
     * @request    id         char        公告id
     * @request    status     tinyint     公告状态
     */
    public function status(\Ziima\MVC\REST\Request $request) {
        $status = $request->body->status;
        $id = $request->body->id;
        $sql = "UPDATE `yuemi_main`.`notice` SET  status = '" . $status . "'  WHERE  id='" . $id . "'";

        if ($this->MySQL->execute($sql)) {
            return [
                '__code' => 'OK',
                '__message' => '已关闭'
            ];
        }
        return [
            '__code' => 'error',
            '__message' => '关闭失败'
        ];
    }

    /**
     * 系统公告打开
     * @param \Ziima\MVC\REST\Request $request
     * @request    id         char        公告id
     * @request    status     tinyint     公告状态
     */
    public function open(\Ziima\MVC\REST\Request $request) {
        $status = $request->body->status;
        $id = $request->body->id;
        $sql = "UPDATE `yuemi_main`.`notice` SET  status = '" . $status . "'  WHERE  id='" . $id . "'";

        if ($this->MySQL->execute($sql)) {
            return [
                '__code' => 'OK',
                '__message' => '已打开'
            ];
        }
        return [
            '__code' => 'error',
            '__message' => '打开失败'
        ];
    }

	/**
     * 系统公告通过审核
     * @param \Ziima\MVC\REST\Request $request
     * @request    id         char        公告id
     */
    public function examine(\Ziima\MVC\REST\Request $request) {
        $id = $request->body->id;
        $sql = "UPDATE `yuemi_main`.`notice` SET  status = 2,`audit_user` = ".$this->User->id.",`audit_time` = '".date('Y-m-d H:i:s',Z_NOW)."', `audit_from` = ".$this->Context->Runtime->ticket->ip." WHERE  id='" . $id . "'";

        if ($this->MySQL->execute($sql)) {
            return [
                '__code' => 'OK',
                '__message' => '通过审核'
            ];
        }
        return [
            '__code' => 'error',
            '__message' => '打开失败'
        ];
    }
	
	
}
