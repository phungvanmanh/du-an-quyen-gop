<?php

/**
 * NukeViet Content Management System
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

// use Com\Tecnick\Barcode\Type\Square\QrCode\Split;

if (!defined('NV_IS_MOD_PAGE')) {
    exit('Stop!!!');
}
global $user_info;
if(count($user_info) == 0) {
    nv_redirect_location('/nukeviet/users/login/'); 
}

include 'modules/duanmoi/Model/project.php';
$table_lich_su = 'nv4_vi_duanmoi_lich_su_quyen_gop';

//lay du lieu du an
$sql = "SELECT `nv4_vi_duanmoi_themmoiduan`.*
        FROM `nv4_vi_duanmoi_themmoiduan` 
        WHERE nv4_vi_duanmoi_themmoiduan.is_duyet = 1 AND  nv4_vi_duanmoi_themmoiduan.id_nguoi_tao = ". $user_info['userid'] ." ORDER BY nv4_vi_duanmoi_themmoiduan.created_at DESC;";
$res = $db->query($sql);
$list_du_an = $res->fetchAll();

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
// Yêu cầu rút tiền
if($nv_Request->isset_request('ruttien', 'post,get')){
    $du_an = first_client($_POST['id'] , 'id' , $table_duan);
    if ($du_an) {
        // $tongTienDaQuyenGop = "SELECT SUM(nv4_vi_duanmoi_lich_su_quyen_gop.so_tien_quyen_gop) AS tong_tien FROM `nv4_vi_duanmoi_lich_su_quyen_gop` WHERE nv4_vi_duanmoi_lich_su_quyen_gop.id_du_an = ". $du_an['id'] ." AND  nv4_vi_duanmoi_lich_su_quyen_gop.so_tien_quyen_gop > 0 GROUP BY id_du_an";

        // $tongTienDaRut = "SELECT SUM(nv4_vi_duanmoi_lich_su_quyen_gop.so_tien_quyen_gop) AS tong_tien FROM `nv4_vi_duanmoi_lich_su_quyen_gop` WHERE nv4_vi_duanmoi_lich_su_quyen_gop.so_tien_quyen_gop < 0 AND nv4_vi_duanmoi_lich_su_quyen_gop.transaction_id IS NULL AND nv4_vi_duanmoi_lich_su_quyen_gop.id_du_an = ". $du_an['id'] ." GROUP BY id_nguoi_quyen_gop;";

        $post = [
            'full_name'             => $user_info['first_name'] . ' ' . $user_info['last_name'],
            'ma_du_an'              => $du_an['ma_du_an'],
            'id_du_an'              => $du_an['id'],
            'id_nguoi_quyen_gop'    => $user_info['userid'],
            'email_quyen_gop'       => $user_info['email'],
            'so_tien_quyen_gop'     => $_POST['so_tien'] * (-1),
            'so_tien_quyen_gop'     => $_POST['so_tien'] * (-1),
            'ngay_quyen_gop'        => date("Y-m-d"),
        ];

        $create = store_client($post ,$table_lich_su);
        if($create) {
            nv_jsonOutput([
                'status' => true,
                'mess'   => "Đã yêu cầu rút tiền thành công!!",
            ]);
        } else {
            nv_jsonOutput([
                'status' => false,
                'mess'   => "Đã có lỗi hệ thống!!",
            ]);
        }
    } else {
        nv_jsonOutput([
            'status' => false,
            'mess'   => "Dự án không tồn tại!!",
        ]);
    }
}
// print_r(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);die;
$xtpl = new XTemplate('danhsachcacduan.html', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
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
// print_r($contents);die;

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
