<?php

/**
 * NukeViet Content Management System
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 3 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_MOD_PAGE')) {
    exit('Stop!!!');
}

$tableName = 'nv4_vi_duanmoi_client';
//khai báo user đang đăng nhập
global  $user_info;

if(count($user_info) == 0) {
    nv_redirect_location('/nukeviet/users/login/'); 
}

$sql = "SELECT `nv4_vi_duanmoi_lich_su_quyen_gop`.ma_du_an ,
            `nv4_vi_duanmoi_lich_su_quyen_gop`.so_tien_quyen_gop, 
            `nv4_vi_duanmoi_themmoiduan`.`ten_du_an`,
            `nv4_vi_duanmoi_themmoiduan`.`id` AS id_du_an,
        SUM(`nv4_vi_duanmoi_lich_su_quyen_gop`.so_tien_quyen_gop) as tong_tien
        FROM `nv4_vi_duanmoi_lich_su_quyen_gop` 
        RIGHT JOIN `nv4_vi_duanmoi_themmoiduan` 
        ON `nv4_vi_duanmoi_lich_su_quyen_gop`.`ma_du_an` = `nv4_vi_duanmoi_themmoiduan`.`ma_du_an` 
        WHERE email_quyen_gop='". $user_info['email'] ."' GROUP BY `nv4_vi_duanmoi_lich_su_quyen_gop`.ma_du_an , `nv4_vi_duanmoi_lich_su_quyen_gop`.so_tien_quyen_gop, `nv4_vi_duanmoi_themmoiduan`.`ten_du_an`, `id_du_an`";

// print_r($sql);die;
$result = $db->query($sql);
$lich_su_client = $result->fetchAll();

$xtpl = new XTemplate('profile.html', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
foreach($lich_su_client as $key => $value){
    $value['so_tien_quyen_gop'] = $value['so_tien_quyen_gop'] * 23000;
    $value['url_chi_tiet'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=chitietduan&amp;id=' . $value['id_du_an'];
    $value['ngay_quyen_gop'] = date("d/m/Y H:i");
    $xtpl->assign('K', $key + 1);
    $xtpl->assign('V', $value);
    $xtpl->parse('main.lich_su_client');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
