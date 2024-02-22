<?php

/**
 * NukeViet Content Management System
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 3 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_ADMIN')) {
    exit('Stop!!!');
}

$page_title = $lang_module['danhsachclient'];

$sql = "SELECT * FROM `nv4_vi_duannuke_client`"; 
$res = $db->query($sql);
$list = $res->fetchAll();


$xtpl = new XTemplate('danhsachclient.html', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
foreach($list as $key => $value){
    $xtpl->assign('KEY', $key + 1);
    $xtpl->assign('VALUE', $value);
    $xtpl->parse('main.list');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
