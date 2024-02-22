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

//lay du lieu du an

$sql = "SELECT * FROM `nv4_vi_duanmoi_themmoiduan` WHERE is_duyet = 1 AND is_open = 1";
$res = $db->query($sql);
$list_du_an = $res->fetchAll();

$xtpl = new XTemplate('main.html', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
 
foreach($list_du_an as $key => $value){

    $value['url_chi_tiet'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=chitietduan&amp;id=' . $value['id'];
    $xtpl->assign('HINH_ANH', explode(',',$value['hinh_anh'])[0]);
    $xtpl->assign('VALUE', $value);
    $xtpl->parse('main.list');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
