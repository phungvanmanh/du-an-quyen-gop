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

global $admin_info;
$table_lich_su = 'nv4_vi_duanmoi_lich_su_quyen_gop';
//lay du lieu du an
$sql = "SELECT nv4_vi_duanmoi_lich_su_quyen_gop.*, nv4_vi_duanmoi_themmoiduan.ten_du_an FROM `nv4_vi_duanmoi_lich_su_quyen_gop` JOIN nv4_vi_duanmoi_themmoiduan ON nv4_vi_duanmoi_themmoiduan.id = nv4_vi_duanmoi_lich_su_quyen_gop.id_du_an  WHERE nv4_vi_duanmoi_lich_su_quyen_gop.so_tien_quyen_gop < 0 AND nv4_vi_duanmoi_lich_su_quyen_gop.transaction_id IS NULL ORDER BY nv4_vi_duanmoi_lich_su_quyen_gop.created_at DESC";

// $tongTienDaQuyenGop = "SELECT SUM(nv4_vi_duanmoi_lich_su_quyen_gop.so_tien_quyen_gop) AS tong_tien FROM `nv4_vi_duanmoi_lich_su_quyen_gop` WHERE nv4_vi_duanmoi_lich_su_quyen_gop.id_du_an = ". $du_an['id'] ." AND  nv4_vi_duanmoi_lich_su_quyen_gop.so_tien_quyen_gop > 0 GROUP BY id_du_an";

// $tongTienDaRut = "SELECT SUM(nv4_vi_duanmoi_lich_su_quyen_gop.so_tien_quyen_gop) AS tong_tien FROM `nv4_vi_duanmoi_lich_su_quyen_gop` WHERE nv4_vi_duanmoi_lich_su_quyen_gop.so_tien_quyen_gop < 0 AND nv4_vi_duanmoi_lich_su_quyen_gop.transaction_id IS NULL AND nv4_vi_duanmoi_lich_su_quyen_gop.id_du_an = ". $du_an['id'] ." GROUP BY id_nguoi_quyen_gop;";

$res = $db->query($sql);
$list_yeu_cau = $res->fetchAll();
if($nv_Request->isset_request('acpDuyet', 'post,get')){
    $lich_su = first($_POST['id'] , 'id' , $table_lich_su);
    if($lich_su) {
        $sql_1 = "SELECT nv4_vi_duanmoi_lich_su_quyen_gop.id_du_an,  SUM(nv4_vi_duanmoi_lich_su_quyen_gop.so_tien_quyen_gop) AS tong_tien FROM `nv4_vi_duanmoi_lich_su_quyen_gop` WHERE nv4_vi_duanmoi_lich_su_quyen_gop.id_du_an = ". $lich_su['id_du_an'] ." AND  nv4_vi_duanmoi_lich_su_quyen_gop.so_tien_quyen_gop > 0 GROUP BY nv4_vi_duanmoi_lich_su_quyen_gop.id_du_an";
        $res_1 = $db->query($sql_1);
        $tongTienDaQuyenGop = $res_1->fetch();
        $sql_2 = "SELECT SUM(nv4_vi_duanmoi_lich_su_quyen_gop.so_tien_quyen_gop) AS tong_tien FROM `nv4_vi_duanmoi_lich_su_quyen_gop` WHERE nv4_vi_duanmoi_lich_su_quyen_gop.so_tien_quyen_gop < 0 AND nv4_vi_duanmoi_lich_su_quyen_gop.transaction_id IS NOT NULL AND nv4_vi_duanmoi_lich_su_quyen_gop.id_du_an = ". $lich_su['id_du_an'] ." GROUP BY id_nguoi_quyen_gop;";
        $res_2 = $db->query($sql_2);
        $tongTienDaRut = $res_2->fetch();
        if($tongTienDaQuyenGop['tong_tien'] > ($tongTienDaRut['tong_tien']  + $_POST['so_tien']) * (-1)) {
            $lich_su['transaction_id'] = uniqid();
            $update = update($lich_su['id'], $lich_su ,$table_lich_su);
            if($update) {
                nv_jsonOutput([
                    'status' => true,
                    'mess'   => "Phê Duyệt Thành Công",
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
                'mess'   => "Số Tiền Quyên Góp Còn Lại Không đủ để rút!!",
            ]);
        }
        // print_r($tongTienDaRut);
    }
   

}

$xtpl = new XTemplate('danhsachyeucau.html', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
//for dữ liệu dự án
foreach($list_yeu_cau as $key => $value){
    $xtpl->assign('VALUE', $value);
    $xtpl->assign('KEY', $key + 1);
    $xtpl->parse('main.loop');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
