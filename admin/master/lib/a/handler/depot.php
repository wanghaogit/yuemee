<?php

include "lib/ApiHandler.php";

/**
 * 库存管理接口
 */
class depot_handler extends ApiHandler {

    function __construct(\Ziima\MVC\Context $ctx) {
        parent::__construct($ctx);
    }

    /**
     * 修改品牌图标
     * @param \Ziima\MVC\REST\Request $request
     * @request    id		int			品牌id
     * @request    logo		string		品牌图标的Base64
     * @slient
     */
    public function brand_logo(\Ziima\MVC\REST\Request $request) {
        $id = $request->body->id;
        $logo = $request->body->logo;
        $sql = "UPDATE `yuemi_sale`.`brand` SET  logo = '" . $logo . "'  WHERE  id='" . $id . "'";
        if ($this->MySQL->execute($sql)) {
            return [
                '__code' => 'OK',
                '__message' => '更新失败'
            ];
        }
    }

    /**
     * 供应商暂停合作
     * @param \Ziima\MVC\REST\Request $request
     * @request    id         char        供应商id
     * @request    status     smallint    供应商状态
     */
    public function break_off(\Ziima\MVC\REST\Request $request) {
        $status = $request->body->status;
        $id = $request->body->id;
        $sql = "UPDATE `yuemi_main`.`supplier` SET  status = '" . $status . "'  WHERE  id='" . $id . "'";
        if ($this->MySQL->execute($sql)) {
            return [
                '__code' => 'OK',
                '__message' => '已经终止了合作'
            ];
        }
        return [
            '__code' => 'error',
            '__message' => '终止合作失败'
        ];
    }

    /**
     * 供应商继续合作
     * @param \Ziima\MVC\REST\Request $request
     * @request    id         char        供应商id
     * @request    status     smallint    供应商状态
     */
    public function turn_on(\Ziima\MVC\REST\Request $request) {
        $status = $request->body->status;
        $id = $request->body->id;
        $sql = "UPDATE `yuemi_main`.`supplier` SET  status = '" . $status . "'  WHERE  id='" . $id . "'";
        if ($this->MySQL->execute($sql)) {
            return [
                '__code' => 'OK',
                '__message' => ' '
            ];
        }
        return [
            '__code' => 'error',
            '__message' => '继续合作打开失败失败'
        ];
    }

    /**
     * 获取分类ID
     * @param \Ziima\MVC\REST\Request $request
     */
    public function get_catagory(\Ziima\MVC\REST\Request $request) {
        $id = $request->body->id;
        $sql = "SELECT * FROM `yuemi_sale`.`catagory` WHERE `parent_id` = {$id}";

        $re = $this->MySQL->grid($sql);
        if (empty($re)) {
            return [
                'Re' => '',
                '__code' => 'OK',
                '__message' => ''
            ];
        }
        return [
            'Re' => $re,
            '__code' => 'OK',
            '__message' => ''
        ];
    }

    /**
     * 获取分类ID2
     * @param \Ziima\MVC\REST\Request $request
     */
    public function get_catagory2(\Ziima\MVC\REST\Request $request) {
        $suid = $request->body->supplier_id;
        $supplier = \yuemi_main\SupplierFactory::Instance()->load($suid);
        $tableName = '`yuemi_sale`.`ext_' . $supplier->alias . '_catagory`';
        $id = $request->body->id;
        $sql = "SELECT * FROM " . $tableName . " WHERE `parent_id` = {$id}";
        $re = $this->MySQL->grid($sql);

        if (empty($re)) {
            return [
                'Re' => '',
                '__code' => 'OK',
                '__message' => ''
            ];
        }
        return [
            'Re' => $re,
            '__code' => 'OK',
            '__message' => ''
        ];
    }

    /**
     * 修改供应商管理
     * @param \Ziima\MVC\REST\Request $request
     */
    public function user_revip(\Ziima\MVC\REST\Request $request) {
        $id = $request->body->id;
        $mobile = $request->body->mobile;
        $row = $this->MySQL->row("SELECT `u`.* FROM `yuemi_main`.`user` AS `u` WHERE `mobile` = {$mobile}");
        if (!empty($row) && $row['level_u'] == 1) {
            $uid = $row['id'];
            $row2 = $this->MySQL->row("SELECT `id` FROM `yuemi_main`.`supplier` WHERE `user_id` = {$uid}");
            $row3 = $this->MySQL->row("SELECT `id` FROM `yuemi_main`.`supplier_user` WHERE `user_id` = {$uid}");
            if (!empty($row2)) {
                //绑定其他供应商
                return [
                    '__code' => 'OK',
                    '__message' => '该手机已绑定其他供应商'
                ];
            } elseif (!empty($row3)) {
                //供应商子账户
                return [
                    '__code' => 'OK',
                    '__message' => '该手机已绑定其他供应商子账户'
                ];
            } else {
                $this->MySQL->execute("UPDATE `yuemi_main`.`supplier` SET `user_id` = {$uid} WHERE `id` = {$id}");
                return [
                    '__code' => 'OK',
                    '__message' => '修改成功'
                ];
            }
        } else {
            //用户不存在
            return [
                '__code' => 'OK',
                '__message' => '手机号错误或无此用户'
            ];
        }
    }

    /**
     * 删除品牌
     * @param \Ziima\MVC\REST\Request $requestv
     */
    public function delete_brand(\Ziima\MVC\REST\Request $request) {
        $id = $request->body->id;
        $this->MySQL->execute("DELETE FROM `yuemi_sale`.`brand` WHERE `id` = {$id}");
        return 'OK';
    }

    /**
     * 供应商列表
     * @param \Ziima\MVC\REST\Request $request
     */
    public function supplier_list(\Ziima\MVC\REST\Request $request) {
        return [
            'res' => $this->MySQL->grid("SELECT * FROM `yuemi_main`.`supplier`")
        ];
    }

    /**
     * 新增品牌
     * @param \Ziima\MVC\REST\Request $request
     */
    public function new_brand(\Ziima\MVC\REST\Request $request) {
        $sid = $request->body->supplier;
        $name = $request->body->brand_name;
        $alias = $request->body->brand_alias;
        $rol = $this->MySQL->row("SELECT `id`,`name` FROM `yuemi_sale`.`brand` WHERE `name` = '{$name}' ");
        if (!empty($rol)) {
            return [
                'msg' => $name.'已经添加过了！'
            ];
        }
        $BrandEntity = new \yuemi_sale\BrandEntity();
        $BrandEntity->supplier_id = $sid;
        $BrandEntity->name = $name;
        $BrandEntity->alias = $alias;

        $BrandFactory = new \yuemi_sale\BrandFactory(MYSQL_WRITER, MYSQL_READER);
        if (!$BrandFactory->insert($BrandEntity)) {
            return [
                'msg' => $name.'添加失败！'
            ];
        }else{
            return [
                'msg' => 'OK'
            ];
        }
    }

}
