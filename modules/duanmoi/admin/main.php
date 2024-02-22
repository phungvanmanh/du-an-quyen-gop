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
include('project.php');

global $admin_info;
$page_title = $lang_module['main'];
$post = [];

if($nv_Request->isset_request('themmoi','post,get')){
    // $tag = explode(',', $_POST['id_tag']);
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

    //Kieu moi
    $post['hinh_anh']   = $list_anh;
    $post['slug_du_an'] = createSlug($_POST['ten_du_an']);
    $post['id_nguoi_tao'] = $admin_info['userid'];
    $check_slug_du_an   = first($post['slug_du_an'] , 'slug_du_an' , $table_duan);

    // $err = request($_POST, $arr_name);
    $err = request($post, $arr_name);
    if ($check_slug_du_an) {
        // $err[] = "Tên Dự Án Đã Tồn Tại!";
    }
    if(!empty($err)) {
        foreach($err as $key => $value){
            print_r($value . '|');
        }
        die;
    } else {
        $check = store($post, $table_duan);
     
        if($check){
            // logs($data);
            nv_jsonOutput([
                'status' =>  true,
                'mess'   =>  'Đã thêm mới dự án thành công!!',
            ]);
        } else {
            nv_jsonOutput([
                'status' =>  false,
                'mess'   =>  'Đã có lỗi hệ thống!',
            ]);
        }
    }
}
// print_r('OKk');
// die;
$xtpl = new XTemplate('main.html', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);



$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
