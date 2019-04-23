<?php

include "lib/ApiHandler.php";

/**
 * 团队管理接口
 */
class team_handler extends ApiHandler {

    function __construct(\Ziima\MVC\Context $ctx) {
        parent::__construct($ctx);
    }

    /**
     * 团队删除
     * @param \Ziima\MVC\REST\Request $request
     * @request    id     int     id
     * @slient
     */
    public function del(\Ziima\MVC\REST\Request $request) {
        $id = $request->body->id;
        $sql = "DELETE FROM `yuemi_main`. `team` WHERE id = '" . $id . "'";
        $this->MySQL->execute($sql);

        return [
            '__code' => 'OK',
            '__message' => '删除成功'
        ];
    }
    /**
     * 小组删除
     * @param \Ziima\MVC\REST\Request $request
     * @request    id     int     id
     * @slient
     */
    public function group_del(\Ziima\MVC\REST\Request $request) {
        $id = $request->body->id;
        $sql = "DELETE FROM `yuemi_main`. `team_group` WHERE id = '" . $id . "'";
        $this->MySQL->execute($sql);

        return [
            '__code' => 'OK',
            '__message' => '删除成功'
        ];
    }

}
