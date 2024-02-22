<?php

/**
 * NukeViet Content Management System
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 3 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

// use Com\Tecnick\Barcode\Type\Square\QrCode\Split;

if (!defined('NV_IS_FILE_ADMIN')) {
    exit('Stop!!!');
}

include('project.php');

$page_title = $lang_module['danhsach'];
global $admin_info;
$table_tag = 'nv4_vi_duanmoi_tags';

//lay du lieu du an
$sql = "SELECT `nv4_vi_duanmoi_themmoiduan`.*,nv4_users.first_name ,nv4_users.last_name, nv4_users.userid
        FROM `nv4_vi_duanmoi_themmoiduan` 
        LEFT JOIN nv4_users 
        on nv4_vi_duanmoi_themmoiduan.id_nguoi_tao = nv4_users.userid 
        WHERE nv4_vi_duanmoi_themmoiduan.is_duyet != 2  ORDER BY created_at DESC;";

$res = $db->query($sql);
$list_du_an = $res->fetchAll();

include('project.php');
//đổi trạng thái->done
if($nv_Request->isset_request('change','post,get')){
    if($_POST['id_change']){
        // $sql = "SELECT * FROM `nv4_vi_duanmoi_themmoiduan` WHERE id=" . $_POST['id_change'];
        $data =  first($_POST['id_change'] , 'id' , $table_duan);
        $data['is_open'] = $data['is_open'] == 1 ? 2 : 1;
        $res = update($data['id'] , $data , $table_duan);
        $res = false;
        nv_jsonOutput([
            'status' =>true,
        ]);
    }

}
//lấy dữ liệu để cập nhật
if($nv_Request->isset_request('update','post,get')){
    if($_POST['id']){
        $sql = "SELECT * FROM `nv4_vi_duanmoi_themmoiduan` WHERE id=" . $_POST['id'];
        $res = $db->query($sql);
        $data = $res->fetch();
        die(json_encode($data));
    }
}
//duyệt dự án
if($nv_Request->isset_request('duyet','post,get')){
    if($_POST['ma_du_an']){
        $sql = "UPDATE nv4_vi_duanmoi_themmoiduan 
        SET ma_du_an=:ma_du_an, is_duyet=:is_duyet WHERE id=" . $_POST['id_duyet'];
        $stmt = $db->prepare($sql);
        $stmt->bindParam('ma_du_an', $_POST['ma_du_an']);
        $stmt->bindValue('is_duyet', 1);
        $exe = $stmt->execute();
        if($exe){
            die('success');
        }else{
            die('error');
        } 
    }else{
        die('Cần phải nhập mã dự án để duyệt dự án');
    }
       
}
//không duyệt dự án
if($nv_Request->isset_request('khongduyet','post,get')){
    if($_POST['id_khongduyet']){
        $sql = "SELECT * FROM `nv4_vi_duanmoi_themmoiduan` WHERE id=" . $_POST['id_khongduyet'];
        $res = $db->query($sql);
        $data = $res->fetch();

        $is_khongduyet = $data['is_duyet'] == 3 ? 2 : 3;
        $exe = $db->query("UPDATE `nv4_vi_duanmoi_themmoiduan` SET is_duyet=".$is_khongduyet." WHERE id=" . $_POST['id_khongduyet']);
        if($exe){
            die('success');
        }else{
            die('error');
        }
    }

}
//un duyệt dự án
if($nv_Request->isset_request('unduyet','post,get')){
    if($_POST['id_unduyet']){
        $exe = $db->query("UPDATE `nv4_vi_duanmoi_themmoiduan` SET is_duyet= 3  WHERE id=" . $_POST['id_unduyet']);
        if($exe){
            nv_jsonOutput([
                'status' =>true,
            ]);
        }else{
            nv_jsonOutput([
                'status' =>false,
            ]);
        }
    }
}
//xóa dự án
if($nv_Request->isset_request('delete','post,get')){
    if($_POST['id_del']){
        $sql    = "DELETE FROM `nv4_vi_duanmoi_themmoiduan` WHERE id=" . $_POST['id_del'];
        $stmt   = $db->prepare($sql);
        $res    = $stmt->execute();
        if($res){
            nv_jsonOutput([
                "status" =>true,
            ]);
        }
    }

}
//xem lịch sử dự án
if($nv_Request->isset_request('history','post,get')){
    if($_POST['ma_du_an']){
        $sql = "SELECT `ma_du_an`,`email_quyen_gop`,`full_name`, SUM(`so_tien_quyen_gop`) AS TONG_TIEN FROM `nv4_vi_duanmoi_lich_su_quyen_gop` 
                WHERE `ma_du_an` =" . "'" . $_POST['ma_du_an'] . "'" . " GROUP BY `email_quyen_gop`;";
        $res = $db->query($sql);
        $list = $res->fetchAll();
        if($res){
            nv_jsonOutput([
                'list' => $list,
            ]);
        }
        // die(json_encode($list));
    }       
}
//mô tả chi tiết dự án
if($nv_Request->isset_request('motachitiet', 'post,get')){
    if($_POST['id']){
        $sql = "SELECT * FROM `nv4_vi_duanmoi_themmoiduan` WHERE id=" . $_POST['id'];
        $res = $db->query($sql);
        $data = $res->fetch();
        die(json_encode($data));
    }
}
//mô tả ngắn dự án
if($nv_Request->isset_request('motangan', 'post,get')){
    if($_POST['id']){
        $sql = "SELECT * FROM `nv4_vi_duanmoi_themmoiduan` WHERE id=" . $_POST['id'];
        $res = $db->query($sql);
        $data = $res->fetch();
        die(json_encode($data));
    }
}
//thục hiện cập nhật dự án
if($nv_Request->isset_request('acceptUpdate', 'post,get')){
    $tag = explode(',', $_POST['id_tag']);
    $list_anh = '';
    $link_luu_anh = NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_name;
    if($_POST['so_luong_anh'] > 0){
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
        $_POST['hinh_anh'] = $list_anh;
    }else{
        $_POST['hinh_anh'] = $_POST['image_link'];
    }
    

    $_POST['slug_du_an'] = createSlug($_POST['ten_du_an']);
    foreach ($duan as $key => $value) {
        $post[$value] = $_POST[$value];
    }
    $err = request($post, $arr_name);
   
    if(!empty($err)) {
        foreach($err as $key => $value){
            print_r($value . '|');
        }
        die;
    }else{
        $check = update($_POST['id'],$post, $table_duan);
        if($check){
            nv_jsonOutput([
                'status' => true,
            ]);
        }else{
            nv_jsonOutput([
                'status' => false,
            ]);
        } 
    }
}

$xtpl = new XTemplate('danhsachduan.html', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
//for dữ liệu dự án
foreach($list_du_an as $key => $value){
    $ho_va_ten = '';
    if($value['first_name'] != null) {
        $ho_va_ten = $value['first_name'];
    } else {
        if($value['userid'] == 1) {
            $ho_va_ten = "ADMIN";
        }
    }
    $xtpl->assign('NGUOI_TAO', $ho_va_ten);
    if($value['is_open'] == 1){
        $x = '<button class="change btn btn-primary" data-id="'. $value['id'].'" >Hiển Thị</button>';
        $xtpl->assign('IS_OPEN', $x);
    }else{
        $x = '<button class="change btn btn-danger" data-id="'. $value['id'].'" >Tạm tắt</button>';
        $xtpl->assign('IS_OPEN', $x);
    }

    $value['thoi_han'] = date('d/m/Y');
    if($value['is_duyet'] == 1){
        $x = '<button class="unduyet btn btn-success" data-id="'. $value['id'].'">Đã duyệt</button>';
        $xtpl->assign('IS_DUYET', $x);
    }else if($value['is_duyet'] == 3){
        $x = '<button class="duyet btn btn-primary" data-id="'. $value['id'].'" data-toggle="modal" data-target="#duyetDuan">Duyệt</button>
                <button class="khongduyet btn btn-warning" data-id="'. $value['id'].'">Không duyệt</button>';
        $xtpl->assign('IS_DUYET', $x);
    }
    $xtpl->assign('HINH_ANH', explode(',',$value['hinh_anh'])[0]);
    $xtpl->assign('VALUE', $value);
    $xtpl->assign('KEY', $key + 1);
    $xtpl->parse('main.loop');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
