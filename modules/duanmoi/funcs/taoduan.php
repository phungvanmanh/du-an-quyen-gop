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
include('project.php');

global $user_info;
$databaseName = 'nv4_vi_duanmoi_themmoiduan';
// $arr_name = [
//     'ten_du_an'         => 'Ten du an',
//     'so_tien'           => 'Tien quyen gop',
//     'hinh_anh'          => 'Hinh anh du an',
//     'mo_ta_chi_tiet'    => 'Mo ta chi tiet',
//     'thoi_han'          => 'Thời hạn',
//     'mo_ta_ngan'        => 'Mo ta ngan',
//     'is_open'           => 'Trang thai',
// ];

// $page_title = $lang_module['taoduan'];
$xtpl = new XTemplate('taoduan.html', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
//thêm mới dự án
if($nv_Request->isset_request('themmoi','post,get')){
    $list_anh = '';
    $link_luu_anh = NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_name;
    for ($i=0; $i < $_POST['so_luong_anh']; $i++) { 
        $upload = new NukeViet\Files\Upload('images', $global_config['forbid_extensions'], $global_config['forbid_mimes'], NV_UPLOAD_MAX_FILESIZE, 1600, 300);
        $upload->setLanguage($lang_global);
        $upload_info = $upload->save_file($_FILES['anh_' . $i] , $link_luu_anh, false, $global_config['nv_auto_resize']);
        if (strlen($list_anh) > 10) {
            $list_anh .= ',' . NV_BASE_SITEURL . '/' . NV_UPLOADS_DIR . '/' .$module_name . '/' . $upload_info['basename'];
        } else {
            $list_anh = NV_BASE_SITEURL . '/' . NV_UPLOADS_DIR . '/' .$module_name . '/' . $upload_info['basename'];
        }
    }
    foreach ($duan as $key => $value) {
        $post[$value] = $_POST[$value];
    }
    $post['hinh_anh'] = $list_anh;
    $post['slug_du_an'] = createSlug($_POST['ten_du_an']);
    $err = request($_POST, $arr_name);
    
    if(!empty($err)){
        foreach($err as $key => $value){
            print_r($value);
        }
        die;
    }else{
        $post['id_nguoi_tao'] = $user_info['userid'];
        // print_r($post);die;
        $exe = store_client($post, $databaseName);
        if($exe){
            die('success');
        }else{
            die('error');
        }   
        
    }
}
$button  ='<button type="button" id="create" class="btn btn-primary">Thêm mới</button>';

if(!$user_info['userid']) {
    $errors = '<script type="text/javascript">toastr.error("Bạn cần Đăng Nhập Vào Hệ Thống!")</script>';
    $button  ='<button type="button" id="create" class="btn btn-primary" disabled>Thêm mới</button>';
    $xtpl->assign('err', $errors);
}

$xtpl->assign('BUTTON', $button);

$xtpl->parse('main');
$contents = $xtpl->text('main');


include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
