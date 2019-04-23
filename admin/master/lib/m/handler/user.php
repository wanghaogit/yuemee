<?php

include "lib/AdminHandler.php";

/**
 * 用户管理
 * @auth
 */
class user_handler extends AdminHandler {

    function __construct(\Ziima\MVC\Context $ctx) {
        parent::__construct($ctx);
    }

    public function index(int $p = 0, string $m = '', string $v = '', string $n = '', string $c = '', int $l = 0) {
        $sql = "SELECT `u`.*," .
                "`uc`.`user_id` AS `cert_exists`,`invuser`.`mobile` AS `invmobile` ,`uc`.`status` AS `cert_status`,`uc`.`card_name` AS `cert_name`, `uc`.`card_no` AS `cert_no`, " .
                "`uw`.`app_open_id`,`uw`.`web_open_id`,`uw`.`name` AS `wechat_name`,`uw`.`avatar` AS `wechat_avatar` ,`re`.`province`,`re`.`city`,`re`.`country` " .
                "FROM `yuemi_main`.`user` AS `u` " .
                "LEFT JOIN `yuemi_main`.`user_cert` AS `uc` ON `uc`.`user_id` = `u`.`id` " .
                " LEFT JOIN `yuemi_main`.`user` AS `invuser` ON `invuser`.`id` = `u`.`invitor_id` " .
                "LEFT JOIN `yuemi_main`.`user_wechat` AS `uw` ON `uw`.`user_id` = `u`.`id` " .
                "LEFT JOIN `yuemi_main`.`region` AS `re` ON `re`.`id` = `u`.`region_id`";
        $sql2 = "SELECT count(*) AS `count`,`u`.*," .
                "`uc`.`user_id` AS `cert_exists`,`invuser`.`mobile` AS `invmobile` ,`uc`.`status` AS `cert_status`,`uc`.`card_name` AS `cert_name`, `uc`.`card_no` AS `cert_no`, " .
                "`uw`.`app_open_id`,`uw`.`web_open_id`,`uw`.`name` AS `wechat_name`,`uw`.`avatar` AS `wechat_avatar` ,`re`.`province`,`re`.`city`,`re`.`country` " .
                "FROM `yuemi_main`.`user` AS `u` " .
                "LEFT JOIN `yuemi_main`.`user_cert` AS `uc` ON `uc`.`user_id` = `u`.`id` " .
                " LEFT JOIN `yuemi_main`.`user` AS `invuser` ON `invuser`.`id` = `u`.`invitor_id` " .
                "LEFT JOIN `yuemi_main`.`user_wechat` AS `uw` ON `uw`.`user_id` = `u`.`id` " .
                "LEFT JOIN `yuemi_main`.`region` AS `re` ON `re`.`id` = `u`.`region_id`";

        $whr = [];
        $level[1] = " u.level_u > 0 AND u.level_d > 0 "; //总经理
        $level[2] = " u.level_u > 0 AND u.level_c > 0 "; //总监
        $level[3] = " u.level_u > 0 AND u.level_v > 0 AND u.level_d = 0 AND u.level_c = 0 "; //VIP
        $level[4] = " u.level_u > 0 AND u.level_v = 0 AND u.level_d = 0 AND u.level_c = 0 "; //普通用户
        if (preg_match('/^\d+$/i', $m)) {
            if (strlen($m) == 11) {
                $whr[] = "`u`.`mobile` = '$m'";
            } else if (strlen($m) < 11) {
                $whr[] = "`u`.`mobile` LIKE '%$m%'";
            }
        }
        if (preg_match('/^1\d{10}$/', $v)) {
            $inviter_id = $this->MySQL->scalar("SELECT `id` FROM `yuemi_main`.`user` WHERE `mobile` = '$v'");
            if ($inviter_id && $inviter_id > 0) {
                $whr[] = "`u`.`invitor_id` = $inviter_id";
            }
        }
        if ($n !== '') {
            $whr[] = "`u`.`name` LIKE '%$n%'";
        }
        if ($c !== '') {
            $whr[] = "`uc`.`card_no` = '{$c}'";
        }

        $search_time_start = strtotime($_GET['search_time_start'] ?? ""); // 开始时间
        $search_time_end = strtotime($_GET['search_time_end'] ?? ""); // 结束时间
        if ($search_time_start > 0)
            $whr[] = " u.reg_time >= {$search_time_start} ";
        if ($search_time_end > 0)
            $whr[] = " u.reg_time <= {$search_time_end} ";

        if ($l > 0) {
            $whr[] = $level[$l];
        }
        if ($whr) {
            $sql .= ' WHERE ' . implode(' AND ', $whr);
        }
        if ($whr) {
            $sql2 .= ' WHERE ' . implode(' AND ', $whr);
        }
        $count = $this->MySQL->row($sql2);
        $sql .= " ORDER BY `u`.`reg_time` DESC";
        $res = $this->MySQL->paging($sql, 20, $p);


        //另外一种连表查询方式，推荐使用
        $tmp = [];
        foreach ($res->Data as $u) {
            if ($u['invitor_id'] > 0 && !in_array($u['invitor_id'], $tmp))
                $tmp[] = $u['invitor_id'];
        }
        $map = [];
        if ($tmp) {
            $map = $this->MySQL->map("SELECT `id`,`name` FROM `yuemi_main`.`user` WHERE `id` IN (" . implode(',', $tmp) . ")", 'id', 'name');
        }
        foreach ($res->Data as &$u) {
            $u['invitor_name'] = $map[$u['invitor_id']] ?? '';
        }

        return [
            'data' => $res,
            'count' => $count['count']
        ];
    }

    public function cert(int $p = 0, string $n = '', string $c = '', int $s = 0) {
        $sql = "select * from `yuemi_main`.`user_cert`";
        $whr = [];
        if ($n !== '') {
            $whr[] = " `card_name` LIKE '%{$n}%' ";
        }
        if ($c !== '') {
            $whr[] = " `card_no` = '{$c}' ";
        }
        if ($s !== 0) {
            $whr[] = " `status` = {$s} ";
        }
        if ($whr) {
            $sql .= ' WHERE ' . implode(' AND ', $whr);
        }
        $sql .= " ORDER BY `create_time` DESC ";

        $res = $this->MySQL->paging($sql, 13, $p);
        return ['data' => $res];
    }

    public function wechat(int $p = 0, string $m = '', string $n = '') {
        $sql = 'SELECT `uw`.*,`u`.`name` AS `invname`,`re`.`province`,`re`.`city`,`re`.`country` FROM `yuemi_main`.`user_wechat` AS `uw` LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `uw`.`invitor_id` LEFT JOIN `yuemi_main`.`region` AS `re` ON `re`.`id` = `uw`.`region_id`';
        $whr = [];
        if ($m > 0) {
            $whr[] = " `uw`.`mobile` = '{$m}' ";
        }
        if ($n !== '') {
            $whr[] = " `uw`.`name` LIKE '%{$n}%' ";
        }
        if ($whr) {
            $sql .= ' WHERE ' . implode(' AND ', $whr);
        }
        $sql .= 'ORDER BY `uw`.`id` DESC';
        $res = $this->MySQL->paging($sql, 13, $p);
        return [
            'data' => $res,
        ];
    }

    //用户收货地址
    public function address(int $p = 0, string $n = '', string $m = '', int $r = 0) {
        $sql = "SELECT a.*,r.province,r.city,r.country,`u`.`name` as `uname` " .
                "from user_address as a " .
                "left join region as r on r.id = a.region_id left join `yuemi_main`.`user` as `u` on `a`.`user_id` = `u`.`id`";
        $whr = [];
        if ($n !== '') {
            $whr[] = " `u`.`name` LIKE '%{$n}%' ";
        }
        if ($m !== '') {
            $whr[] = " `a`.`mobile` = '{$m}' ";
        }
        if ($r > 0) {
            $whr[] = " `a`.`region_id` = {$r} ";
        }
        if ($whr) {
            $sql .= ' WHERE ' . implode(' AND ', $whr);
        }
        $sql .= 'ORDER BY `a`.`create_time` DESC';
        $list = $this->MySQL->paging($sql, 25, $p);
        return[
            'list' => $list
        ];
    }

    //银行卡
    public function bank(int $p = 0, string $n = '', string $c = '') {
        $sql = "SELECT u.*,b.name,r.name as aname,o.province,o.city,o.country,`uu`.`name` as user_name " .
                "FROM `yuemi_main`.`user_bank` " .
                "as u left join `yuemi_main`.bank as b " .
                "on u.bank_id = b.id left join `yuemi_main`.`user` as r " .
                "on u.audit_user = r.id left join `yuemi_main`.`region` as o " .
                "on o.id = u.region_id left join `yuemi_main`.`user` as `uu` on `uu`.`id` = `u`.`user_id`";
        $whr = [];
        if ($n !== '') {
            $whr[] = " `uu`.`name` LIKE '%{$n}%' ";
        }
        if ($c !== '') {
            $whr[] = " `u`.`card_no` = '{$c}' ";
        }
        if ($whr) {
            $sql .= ' WHERE ' . implode(' AND ', $whr);
        }
        $sql .= 'ORDER BY `u`.`create_time` DESC';

        $list = $this->MySQL->paging($sql, 25, $p);
        return[
            'list' => $list
        ];
    }

    //用户账目
    public function finance(int $p = 0, string $n = '', string $m = '') {
        $sql = "SELECT `f`.*, " .
                "`u`.`name` AS `user_name`,`u`.`mobile` AS `user_mobile`, " .
                "`c`.`card_no`,`c`.`card_name` " .
                "FROM `yuemi_main`.`user_finance` AS `f` " .
                "LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `f`.`user_id` " .
                "LEFT JOIN `yuemi_main`.`user_cert` AS `c` ON `c`.`user_id` = `f`.`user_id` ";
        $whr = [];
        if ($n !== '') {
            $whr[] = " `u`.`name` LIKE '%{$n}%' ";
        }
        if ($m !== '') {
            $whr[] = " `u`.`mobile` = '{$m}' ";
        }
        if ($whr) {
            $sql .= ' WHERE ' . implode(' AND ', $whr);
        }
        $sql .= " ORDER BY `u`.`id` DESC ";
        $list = $this->MySQL->paging($sql, 22, $p);
        return[
            'list' => $list
        ];
    }

    //手机设备
    public function device(int $p = 0, int $t = 0, int $vid = 0, int $mid = 0, int $rgn = 0, int $ver = 0, int $b = 0) {
        $sql = "SELECT d.*,dv.name as dname,ds.name as sname,`re`.`province`,`re`.city ,`re`.`country` " .
                "FROM `yuemi_main`.`device` AS `d` " .
                "left join `yuemi_main`.`device_vender` as dv " .
                "on dv.id=d.vendor_id " .
                "left join `yuemi_main`.`device_model` as ds" .
                " on ds.id=d.model_id left join `yuemi_main`.`region` as `re` on `re`.`id` = `d`.`region_id`";

        $whr = [];
        if ($b > 0) {
            $whr[] = " `d`.`vendor_id` = {$b} ";
        }
        if ($t > 0) {
            $whr[] = " `d`.`type` = {$t} ";
        }

        if ($whr) {
            $sql .= ' WHERE ' . implode(' AND ', $whr);
        }

        $sql .= " ORDER BY `d`.`id` DESC ";

        $result = $this->MySQL->paging($sql, 25, $p);
        return[
            'Result' => $result,
            'brand' => $this->MySQL->grid("SELECT * FROM `yuemi_main`.`device_vender`")
        ];
    }

    public function vip(int $p = 0, string $n = '', string $i = '', string $m = '', string $ActionName = '', int $s = 0, string $im = '') {
        $ur = URL_RES;
        $sql = "select `s`.`mobile` AS `im`,`v`.*,`u`.`name` as `uname`,CONCAT('{$ur}','/upload',`u`.`avatar`) AS `upic`,`u2`.`level_c`,`u2`.`level_d`,`s`.`name` as `gname`,`uc`.`card_name` as `cname`,u.mobile as um " .
                "from `yuemi_main`.`vip` as `v` left join `yuemi_main`.`user` as `u` " .
                "on `u`.`id` = `v`.`user_id` left join `yuemi_main`.`user` as `s` on `v`.`cheif_id` = `s`.`id` " .
                "left join `yuemi_main`.`user_cert` as `uc` on `v`.`user_id` = `uc`.`user_id` left join `yuemi_main`.`user` as `u2` on `u2`.`id` = `v`.`user_id`";
        $whr = [];
        if ($n !== '') {
            $whr[] = " `u`.`name` LIKE '%{$n}%' ";
        }
        if ($i !== '') {
            $whr[] = " `s`.`name` LIKE '%{$i}%' ";
        }
        if ($im !== '') {
            $whr[] = " `s`.`mobile` LIKE '%{$im}%' ";
        }
        if ($m !== '') {
            $whr[] = " `u`.`mobile` LIKE '%{$m}%' ";
        }
        if ($s > 0) {
            $whr[] = " `v`.`status` = {$s} ";
        }
        $search_time_start = strtotime($_GET['search_time_start'] ?? ""); // 开始时间
        $search_time_end = strtotime($_GET['search_time_end'] ?? ""); // 结束时间
        if ($search_time_start > 0)
            $whr[] = " v.expire_time >= {$search_time_start} ";
        if ($search_time_end > 0)
            $whr[] = " v.expire_time <= {$search_time_end} ";


        $search_time_start_c = strtotime($_GET['search_time_start_c'] ?? ""); // 开始时间
        $search_time_end_c = strtotime($_GET['search_time_end_c'] ?? ""); // 结束时间
        if ($search_time_start_c > 0)
            $whr[] = " v.create_time >= {$search_time_start_c} ";
        if ($search_time_end_c > 0)
            $whr[] = " v.create_time <= {$search_time_end_c} ";


        $whr[] = " `u`.`level_v` > 0 ";
        if ($whr) {
            $sql .= ' WHERE ' . implode(' AND ', $whr);
        }
        $sql .= " ORDER BY `v`.`expire_time` DESC ";

        $res = $this->MySQL->paging($sql, 20, $p);
        foreach ($res->Data as $key => $val) {
            $userid = $val['user_id'];
            $res->Data[$key]['isreissue'] = 0;
            $sql = "SELECT has_gifts FROM `yuemi_main`.`vip` WHERE `user_id` = {$userid}";
            $sql1 = "SELECT COUNT(id) FROM `yuemi_main`.`vip_card` WHERE `rcv_user_id` = {$userid}";
            $sql2 = "SELECT COUNT(user_id) FROM `yuemi_main`.`cheif` WHERE `user_id` = {$userid}";
            $sql3 = "SELECT COUNT(user_id) FROM `yuemi_main`.`director` WHERE `user_id` = {$userid}";
            if ($this->MySQL->scalar($sql) == 0 && $this->MySQL->scalar($sql1) == 1 && $this->MySQL->scalar($sql2) == 0 && $this->MySQL->scalar($sql3) == 0) {
                $res->Data[$key]['isreissue'] = 1;
            }
        }
        $sum = $this->MySQL->row("SELECT count(*) AS `sum` FROM `yuemi_main`.`vip` AS `v`LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `v`.`user_id` WHERE `u`.`level_v` > 0 ");
        $cheif_num = $this->MySQL->scalar("SELECT count(*) AS num FROM `yuemi_main`.`cheif`");
        $director_num = $this->MySQL->scalar("SELECT count(*) AS num FROM `yuemi_main`.`director`");
        $vip_num = $sum['sum'] - $cheif_num - $director_num;
        $gifts = $this->MySQL->grid("SELECT sku.* FROM `yuemi_sale`.`sku` "
                . "LEFT JOIN `yuemi_sale`.`spu` ON sku.spu_id = spu.id "
                . "WHERE sku.`catagory_id` = 701 AND sku.`status` = 2 AND spu.`status` = 1 AND price_inv = 399");
        $cheif_num = $this->MySQL->scalar("SELECT count(*) FROM `yuemi_main`.`cheif`");
        $director_num = $this->MySQL->scalar("SELECT count(*) FROM `yuemi_main`.`director`");
        return [
            'gifts' => $gifts,
            'res' => $res,
            'sum' => $sum,
            'vip_num' => $vip_num,
            'cheif_num' => $cheif_num,
            'director_num' => $director_num
        ];
    }

    public function invite(int $p = 0, string $u = '', string $i = '', string $um = '', string $im = '') {
        $i = trim($i);
        $u = trim($u);
        $sql = "SELECT `u`.`name`,`u`.`mobile`,`u`.`id` AS `uid`,`uu`.`id` AS `iid`,`uu`.`name` AS `iname`,`uu`.`mobile` AS `imobile` " .
                "FROM `yuemi_main`.`user` AS `u` " .
                "INNER JOIN `yuemi_main`.`user` AS `uu` ON `uu`.`id` = `u`.`invitor_id` " .
                "WHERE `u`.`id` > 0 ";
        $whr = [];
        if (!empty($u)) {
            $whr[] = " (`u`.`name` LIKE '%{$u}%' OR `u`.`mobile` LIKE '%{$u}%') ";
        }
        if (!empty($i)) {
            $whr[] = " (`uu`.`name` LIKE '%{$i}%' OR `uu`.`mobile` LIKE '%{$i}%') ";
        }
        if (!empty($um)) {
            $whr[] = " `u`.`mobile` LIKE '%{$um}%' ";
        }
        if (!empty($im)) {
            $whr[] = " `uu`.`mobile` LIKE '%{$im}%' ";
        }
        if ($whr) {
            $sql .= ' AND ' . implode(' AND ', $whr);
        }
        $sql .= " ORDER BY `u`.`id` DESC ";
        $res = $this->MySQL->paging($sql, 30, $p);
        return [
            'res' => $res
        ];
    }

    public function template() {
        $result = $this->MySQL->grid("SELECT * FROM `yuemi_main`.`invite_template` ORDER BY `id` DESC");

        return [
            'Grid' => $result
        ];
    }

    public function template_edit(int $id) {
        $this->FactoryInviteTemplate = new \yuemi_main\InviteTemplateFactory(MYSQL_WRITER, MYSQL_READER);
        $tpl = $this->FactoryInviteTemplate->load($id);
        if ($tpl === null) {
            throw new \Ziima\MVC\Redirector('/index.php?call=user.template');
        }
        return [
            'Tpl' => $tpl
        ];
    }

    /**
     * 用户地址信息获取
     * by wanghao 2018/4/5
     * * */
    public function user_address_info(int $id, int $p = 0, int $t = 0) {
        $sq2 = "select * " .
                "from `yuemi_main`.`user_address`" .
                " where user_id = $id";
        //先写SQL主体，注意缩进
        $sql = "SELECT * " .
                "FROM `yuemi_main`.`region` ";
        //准备用技巧来拼接SQL条件
        $whr = [];
        //-------上面的留着别动-----
        //各种SQL高级玩法
        $Current = null;
        if (strlen($t) != 6) {
            $whr[] = "`id` LIKE '%0000'";
        }

        //-------下面也不要改----
        if (!empty($whr)) {
            $sql .= ' WHERE ' . implode(' AND ', $whr);
        }
        //固定追加排序子句
        $sql .= " ORDER BY `id` asc";
        //固定调用 paging
        $result = $this->MySQL->paging($sql, 50, $p);
        //一定是return
        $res = $this->MySQL->grid($sq2);
        return [
            'res' => $res[0],
            'Current' => $Current,
            'Result' => $result
        ];
    }

    /**
     * 用户地址信息修改
     * by wanghao 2018/4/5
     * * */
    public function address_doedit() {
        $id = intval($_POST['id']);
        $user_id = intval($_POST['uid']);
        $region_id = intval($_POST['region_id']);
        $address = $this->MySQL->encode($_POST['addressinfo']);
        $contacts = $this->MySQL->encode($_POST['person']);
        $mobile = intval($_POST['mobile']);
        $status = intval($_POST['status']);
        $UserAddressEntity = new \yuemi_main\UserAddressEntity();
        $UserAddressEntity->id = $id;
        $UserAddressEntity->user_id = $user_id;
        $UserAddressEntity->region_id = $region_id;
        $UserAddressEntity->contacts = $contacts;
        $UserAddressEntity->mobile = $mobile;
        $UserAddressEntity->status = $status;
        $UserAddressEntity->address = $address;
        $UserAddressFactory = new \yuemi_main\UserAddressFactory(MYSQL_WRITER, MYSQL_READER);
        if (!$UserAddressFactory->update($UserAddressEntity)) {
            throw new \Exception('修改user_address表失败！');
        } else {
            throw new \Ziima\MVC\Redirector('/index.php?call=user.address');
        }
    }

    /**
     * 用户银行信息详情
     * by wanghao 2018/4/5
     * * */
    public function user_bank_info($id) {
        $sql = "select * " .
                "from `yuemi_main`.`user_bank`" .
                " where user_id = $id";
        $sql2 = "select * from bank";
        $res = $this->MySQL->grid($sql);
        $bank = $this->MySQL->grid($sql2);
        return [
            'res' => $res[0],
            'bank' => $bank
        ];
    }

    /**
     * 用户银行信息修改
     * by wanghao 2018/4/5
     * * */
    public function doeidt_userbank() {
        $id = intval($_POST['id']);
        $region_id = $_POST['region_id'];
        $bank_id = intval($_POST['bank_id']);
        $card_no = intval($_POST['card_no']);
        $status = intval($_POST['status']);
        $row = $this->MySQL->column("SELECT `name` FROM `yuemi_main`.`bank` WHERE `id` = {$bank_id}");
        $this->MySQL->execute("UPDATE `yuemi_main`.`user_bank` SET `region_id` = {$region_id},`bank_id` = {$bank_id},`card_no` = {$card_no},`status` = {$status},`bank_name` = '{$row[0]}'  WHERE `id` = {$id}");
        throw new \Ziima\MVC\Redirector('/index.php?call=user.bank');
    }

    /**
     * VIP缴费情况
     * by wanghao 2018/4/6
     * * */
    public function vip_money(int $uid = 0, int $p = 0) {
        $sql = "select v.*,u.name from `yuemi_main`.`vip_buff` as `v` " .
                "left join `yuemi_main`.`user` as `u`" .
                " on u.id=v.user_id where v.user_id=$uid";
        $res = $this->MySQL->paging($sql, 50, $p);
        $ru = $res->Data;
        $con = count($ru);
        for ($i = 0; $i < $con; $i++) {
            $ru[$i]['create_time'] = date('Y-m-d h:i:s', $ru[$i]['create_time']);
            $ru[$i]['expire_time'] = date('Y-m-d h:i:s', $ru[$i]['expire_time']);
        }
        $res->Data = $ru;
        return [
            'res' => $res
        ];
    }

    public function change_money() {
        $num = floatval($_POST['money']);
        $id = intval($_POST['id']);
        $row = $this->MySQL->row("SELECT `coin`" .
                " FROM `yuemi_main`.`user_finance`" .
                " WHERE `user_id` = {$id}");
        $newnum = $row['coin'] + $num;
        $this->MySQL->execute("UPDATE `yuemi_main`.`user_finance` SET `coin` = {$newnum} WHERE `user_id` = {$id}");
        throw new \Ziima\MVC\Redirector('/index.php?call=user.finance');
    }

    public function vip_card(int $uid = 0, int $p = 0) {
        $sql = "SELECT v.*, u.name AS Uname " .
                "FROM `yuemi_main`.`vip_card` AS v " .
                "LEFT JOIN `yuemi_main`.`user` AS u ON v.rcv_user_id = u.id ";

        if ($uid > 0) {
            $sql .= " WHERE v.owner_id = {$uid} ";
        }
        $res = $this->MySQL->paging($sql, 30, $p);
        return [
            'Data' => $res
        ];
    }

    public function vipinv_pic(int $uid = 0) {
        $my = $this->MySQL->row("SELECT `u`.`id`,`u`.`name`,`u`.`mobile`,`uc`.`card_name` FROM `yuemi_main`.`user` AS `u` LEFT JOIN `yuemi_main`.`user_cert` AS `uc` ON `uc`.`user_id` = `u`.`id` WHERE `u`.`id` = {$uid}");
        $cheif = $this->MySQL->row("SELECT `u`.`id`,`u`.`name`,`u`.`mobile`,`uc`.`card_name` FROM `yuemi_main`.`vip` AS `v` LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `v`.`cheif_id` LEFT JOIN `yuemi_main`.`user_cert` AS `uc` ON `v`.`cheif_id` = `uc`.`user_id` WHERE `v`.`user_id` = {$uid}");
        if (!empty($cheif['id'])) {
            $director = $this->MySQL->row("SELECT `u`.`id`,`u`.`name`,`u`.`mobile`,`uc`.`card_name` FROM `yuemi_main`.`cheif` AS `c` LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `c`.`director_id` LEFT JOIN `yuemi_main`.`user_cert` AS `uc` ON `uc`.`user_id` = c`.`director_id` WHERE `c`.`user_id` = {$cheif['id']}");
            if (empty($director['id'])) {
                $director = '';
            }
        } else {
            $cheif = '';
            $director = '';
        }
        $list = $this->get_vip_tree($uid, 1);
        if (!empty($list)) {
            $html = $this->get_html($list);
        } else {
            $html = '';
        }
        return [
            'director' => $director,
            'cheif' => $cheif,
            'my' => $my,
            'html' => $html
        ];
    }

    private function get_vip_tree($uid, $level) {
        $data = $this->MySQL->grid("SELECT `u`.`id`,`u`.`name`,`u`.`level_v` AS `v`,`u`.`mobile`,`uc`.`card_name` FROM `yuemi_main`.`user` AS `u` LEFT JOIN `yuemi_main`.`user_cert` AS `uc` ON `uc`.`user_id` = `u`.`id` WHERE `invitor_id` = {$uid}");
        $level++;
        if (!empty($data) && $level < 7) {
            $tree = [];
            foreach ($data as $v) {
                $child = $this->get_vip_tree($v['id'], $level);
                $tree[] = array('self' => $v, 'child' => $child);
            }
            return $tree;
        }
    }

    public function vipinv_pic2(int $uid = 0) {
        $my = $this->MySQL->row("SELECT `id`,`name` FROM `yuemi_main`.`user` AS `u` WHERE `u`.`id` = {$uid}");
        $parent = $this->get_inv_parent($uid);
        $list = $this->get_tree($uid, 1);
        if (empty($parent)) {
            $parent = 123;
        }
        if (!empty($list)) {
            $html = $this->get_html($list);
        } else {
            $html = '';
        }

        return[
            'html' => $html,
            'my' => $my,
            'parent' => $parent,
            'child' => $list
        ];
    }

    private function get_html($list) {
        $html = '';
        foreach ($list as $t) {
            if ($t['child'] == null) {
                if ($t['self']['v'] == 1) {
                    $html .= "<li><a href='/index.php?call=user.vipinv_pic&uid=" . $t['self']['id'] . "' style='background-color:#B22222;color:#FFF;' title='实名：" . $t['self']['card_name'] . "&#10;手机号：" . $t['self']['mobile'] . "'>" . $t['self']['name'] . '</a>';
                } else {
                    $html .= "<li><a href='#' title='实名：" . $t['self']['card_name'] . "&#10;手机号：" . $t['self']['mobile'] . "'>{$t['self']['name']}</a></li>";
                }
            } else {
                if ($t['self']['v'] == 1) {
                    $html .= "<li><a href='/index.php?call=user.vipinv_pic&uid=" . $t['self']['id'] . "' style='background-color:#B22222;color:#FFF;' title='实名：" . $t['self']['card_name'] . "&#10;手机号：" . $t['self']['mobile'] . "'>" . $t['self']['name'] . '</a>';
                } else {
                    $html .= "<li><a href='#' title='实名：" . $t['self']['card_name'] . "&#10;手机号：" . $t['self']['mobile'] . "'>" . $t['self']['name'] . '</a>';
                }

                $html .= $this->get_html($t['child']);
                $html = $html . "</li>";
            }
        }
        return $html ? '<ul>' . $html . '</ul>' : $html;
    }

    private function get_tree($id, $level) {
        $data = $this->get_inv_child($id);
        $level++;
        if (!empty($data) && $level < 7) {
            $tree = [];
            foreach ($data as $v) {
                $child = $this->get_tree($v['id'], $level);
                $tree[] = array('self' => $v, 'child' => $child);
            }
            return $tree;
        }
    }

    /**
     * 获取邀请关系父级（总监）
     * @param type $uid
     */
    private function get_inv_parent($uid) {
        //user
        $row1 = $this->MySQL->row("SELECT `uu`.`id`,`uu`.`name` FROM `yuemi_main`.`user` AS `u` LEFT JOIN `yuemi_main`.`user` AS `uu` ON `u`.`invitor_id` = `uu`.`id` WHERE `u`.`id` = {$uid}");
        //vip
        $row2 = $this->MySQL->row("SELECT u.`id`,`u`.`name` FROM `yuemi_main`.`user` AS `u` LEFT JOIN `yuemi_main`.`vip` AS `v` ON `v`.`cheif_id` = `u`.`id` WHERE `v`.`user_id` = {$uid}");
        //cheif
        $row3 = $this->MySQL->row("SELECT `u`.`id`,`u`.name FROM `yuemi_main`.`user` AS `u` LEFT JOIN `yuemi_main`.`cheif` AS `c` ON `c`.`director_id` = `u`.`id` WHERE `c`.`user_id` = {$uid}");
        //director
        if (!empty($row1['id'])) {
            $arr = $row1;
        } else {
            if (!empty($row2['id'])) {
                $arr = $row2;
            } else {
                if (!empty($row3['id'])) {
                    $arr = $row3;
                } else {
                    $arr = '';
                }
            }
        }
        return $arr;
    }

    /**
     * 获取邀请关系子级
     * @param type $uid
     */
    private function get_inv_child($uid) {
        $arr = [];
        //user
        $list1 = $this->MySQL->grid("SELECT `id`,`name` FROM `yuemi_main`.`user` WHERE `invitor_id` = {$uid}");
        //vip
        $list2 = $this->MySQL->grid("SELECT `u`.`id`,`u`.`name` FROM `yuemi_main`.`vip` AS `v` LEFT JOIN `yuemi_main`.`user` AS `u` ON `u`.`id` = `v`.`user_id`  WHERE `cheif_id` = {$uid}");
        //cheif
        $list3 = $this->MySQL->grid("SELECT `u`.`id`,`u`.`name` FROM `yuemi_main`.`user` AS `u` LEFT JOIN `yuemi_main`.`cheif` AS `c` ON `c`.`user_id` = `u`.`id` WHERE `c`.`director_id` = {$uid}");
        //director
        foreach ($list1 AS $k => $v) {
            $arr[] = $list1[$k];
        }
        foreach ($list2 AS $k => $v) {
            $arr[] = $list2[$k];
        }
        foreach ($list3 AS $k => $v) {
            $arr[] = $list3[$k];
        }
        return $arr;
    }

    /**
     * 销售佣金
     * @param int $uid
     */
    public function share_money(int $uid, int $p = 0) {
        $sql = " SELECT `r`.*,`buy_u`.`name` AS `buyname`,`share_u`.`name` AS `sharename`,`cheif_u`.`name` AS `cheifname`,`director_u`.`name` AS `directorname`,`k`.`title`,`km`.`file_url` " .
                " FROM `yuemi_sale`.`rebate` AS `r`" .
                " LEFT JOIN `yuemi_main`.`user` AS `buy_u` ON `buy_u`.`id` = `r`.`buyer_id` " .
                " LEFT JOIN `yuemi_main`.`user` AS `share_u` ON `share_u`.`id` = `r`.`share_user_id` " .
                " LEFT JOIN `yuemi_main`.`user` AS `cheif_u` ON `cheif_u`.`id` = `r`.`cheif_id` " .
                " LEFT JOIN `yuemi_main`.`user` AS `director_u` ON `director_u`.`id` = `r`.`director_id` " .
                " LEFT JOIN `yuemi_sale`.`sku` AS `k` ON `k`.`id` = `r`.`sku_id` " .
                " LEFT JOIN `yuemi_sale`.`sku_material` AS `km` ON `km`.`sku_id` = `r`.`sku_id` " .
                " WHERE `share_user_id` = {$uid} ";
        $list = $this->MySQL->paging($sql, 30, $p);
        return [
            'res' => $list
        ];
    }

}
