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

$sql = "SELECT `nv4_vi_duanmoi_themmoiduan`.*,nv4_users.first_name ,nv4_users.last_name, nv4_users.userid
        FROM `nv4_vi_duanmoi_themmoiduan` 
        LEFT JOIN nv4_users 
        on nv4_vi_duanmoi_themmoiduan.id_nguoi_tao = nv4_users.userid 
        WHERE nv4_vi_duanmoi_themmoiduan.is_duyet = 2;";
$res = $db->query($sql);
$list_du_an = $res->fetchAll();

if($nv_Request->isset_request('chuyen','post,get')){
    if(!empty($_POST['id_chuyen'])){
        $sql = "SELECT * FROM `nv4_vi_duanmoi_themmoiduan` WHERE id=" . $_POST['id_chuyen'];
        $res = $db->query($sql);
        $data = $res->fetch();
        $data['is_duyet'] = 3;
        $exe = $db->query("UPDATE `nv4_vi_duanmoi_themmoiduan` SET is_duyet=".$data['is_duyet']." WHERE id=" . $_POST['id_chuyen']);
        if($exe){
            die('success');
        }else{
            die('error');
        }
    }
}

if($nv_Request->isset_request('delete','post,get')){
    if(!empty($_POST['id'])){
        $sql = "DELETE FROM `nv4_vi_duanmoi_themmoiduan` WHERE id=" . $_POST['id'];
        $stmt = $db->prepare($sql);
        $exe = $stmt->execute();
        if($exe){
            die('success');
        }else{
            die('error');
        }
    }
}

$page_title = $lang_module['duankhongduyet'];


$xtpl = new XTemplate('duankhongduyet.html', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);

foreach($list_du_an as $key => $value){
    $ho_va_ten = 'Không Xác Định';
    if($value['first_name'] != null) {
        $ho_va_ten = $value['first_name'];
    } else {
        if($value['userid'] == 1) {
            $ho_va_ten = "ADMIN";
        }
    }
    $value['hinh_anh'] = explode(',' , $value['hinh_anh'])[0];
    $xtpl->assign('NAME', $ho_va_ten);
    $xtpl->assign('VALUE', $value);
    $xtpl->assign('KEY', $key + 1);
    $xtpl->parse('main.list');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
