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

include 'modules/duanmoi/Model/card_profile.php';
// print_r($table_profile);die;
global $user_info;
if(count($user_info) == 0) {
    nv_redirect_location('/nukeviet/users/login/'); 
}
$ss = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=card&amp;id=' . $user_info['userid'];
$client = first_client($user_info['userid'] , 'user_id' , $table_profile);
if($nv_Request->isset_request('update', 'post,get')) {
    $post = [];
    foreach ($fillable_card_profile as $key => $value) {
        $post[$value] = $_POST[$value];
    }
    $post['link_card']    = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=card&id=' . $user_info['userid'];;
                            
    if($client) {
        $create = update_client($client['id'] , $post , $table_profile);
    } else {
        $post['user_id'] = $user_info['userid'];
        $post['email']   = $user_info['email'];
        $create = store_client($post , $table_profile);
    }
    
    if($create) {
        nv_jsonOutput([
            'status'    => true,
            'mess'      => "Đã cập nhập profile thành công!!",
        ]);
    } else {
        nv_jsonOutput([
            'status'    => false,
            'mess'      => "Đã có lỗi hệ thống!!",
        ]);
    }
}
// print_r($user_info['photo']);die;
$xtpl = new XTemplate('cardinfo.html', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('USER' , $user_info);
$xtpl->assign('INFO' , $client);
$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';