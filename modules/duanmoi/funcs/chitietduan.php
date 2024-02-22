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

global $user_info;

include 'modules/duanmoi/Model/card_profile.php';

$table_user = 'nv4_users';
$databaseName_lich_su = 'nv4_vi_duanmoi_lich_su_quyen_gop';


$ma_du_an = '';

$id = $nv_Request->get_int('id', 'post,get');
//lấy dữ liệu của dự án
if($nv_Request->isset_request('id', 'post,get')){
    $sql  = "SELECT * FROM nv4_vi_duanmoi_themmoiduan WHERE id=" . $id;
    $res  = $db->query($sql);
    $data = $res->fetch();
    $list_anh = explode(',', $data['hinh_anh']);
    $ma_du_an = $data['ma_du_an'];
}

if($nv_Request->isset_request('paymentId', 'post,get')){
    $client = [];
    //Check tài khoản đã tồn tại chưa
    $check_mail = first_client($_POST['email_quyen_gop'] , 'email' , $table_user);
    // Nếu chưa tồn tại thì auto tạo tài khoản và gởi mail
    if($check_mail == null){
        $user_name = substr($_POST['email_quyen_gop'] , 0 , strpos($_POST['email_quyen_gop'] , '@'));
        $md5username = nv_md5safe($user_name);
        $client['group_id']                        = 4;
        $client['username']                        = $user_name;
        $client['md5username']                     = $md5username;
        $client['password']                        = $crypt->hash_password("123123", $global_config['hashprefix']);
        $client['active']                          = 1;
        $client['first_name']                      = $_POST['full_name'];
        $client['email_verification_time']         = -1;
        $client['email']                           = $_POST['email_quyen_gop'];
        $client['in_groups']                       = 4;
        $client['birthday']                        = NV_CURRENTTIME;
        $client['question']                        = "Done";
        $exe_1 = store_client($client, $table_user);
        sendMailClient($client);
        $user_new = first_client($client['email'] , 'email' , $table_user);
        if($user_new ) {
            $user_card = first_client($user_new['userid'] , 'user_id' , $table_profile);
            if(!$user_card) {
                $card = [
                    'user_id'   =>  $user_new['userid'],
                    'email'     =>  $user_new['email'],
                    'link_card' =>  NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=card&amp;id=' . $user_info['userid'],
                ];
                $exe_2 = store_client($card, $table_profile);
            }
        }
        $mess = 'Cảm ơn bạn đã quyên góp cho dự án này<br> Vui lòng check mail của bạn để nhận lời cảm ơn từ dự án!';
    } else {
        $mess = 'Cảm ơn bạn đã quyên góp cho dự án này';
    }
    //check user đang đăng nhập để lưu id_nguoi_quyen_gop trong lịch sử
    if($user_info['userid'] == null){
        $check = first_client($_POST['email_quyen_gop'],'email',$table_user);
        if($check){
            $_POST['id_nguoi_quyen_gop'] = $check['userid'];
        }
    }else{
        $_POST['id_nguoi_quyen_gop'] = $user_info['userid'];
    }
    $_POST['ngay_quyen_gop'] = date('Y-m-d H:i:s');
    //thêm mới lịch sử
    $exe = store_client($_POST, $databaseName_lich_su);
    
    $array_success = [
        'status'    => true,
        'mess'      => alert($mess),
    ];
    nv_jsonOutput($array_success);
}

//lấy thông tin tất cả quyên góp của dự án
$table_lich_su = 'nv4_vi_duanmoi_lich_su_quyen_gop';
$sql = 'SELECT * FROM `nv4_vi_duanmoi_lich_su_quyen_gop` WHERE `id_du_an`= '. $id .' AND `so_tien_quyen_gop` > 0';
$thong_tin_quyen_gop = $db->query($sql)->fetchAll();

//lấy thông tin lịch sử của user đang đăng nhập
if($user_info['userid']){
    $sql = "SELECT `id_nguoi_quyen_gop`,`id_du_an`,`full_name`,SUM(`so_tien_quyen_gop`) AS TONG_TIEN FROM `nv4_vi_duanmoi_lich_su_quyen_gop` WHERE `id_du_an`= " . $id . " AND `id_nguoi_quyen_gop` = " . $user_info['userid'] ." GROUP BY `id_du_an`,`id_nguoi_quyen_gop`;";
    $res = $db->query($sql);
    $lich_su_quyen_gop = $res->fetch();
    if($lich_su_quyen_gop){
        $lich_su_quyen_gop['tong_tien'] =  $lich_su_quyen_gop['tong_tien']* 23000;
    }
}


$xtpl = new XTemplate('chitietduan.html', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);

$xtpl->assign('V', $data);
$xtpl->assign('LICH_SU', $lich_su_quyen_gop);


// check thằng đang đăng nhập để lấy email và name disable
if($user_info['userid']){
    $data_user = first_client($user_info['userid'],'userid',$table_user);
    $xtpl->assign('EMAIL', $data_user['email']);
    $xtpl->assign('NAME', $data_user['first_name']);
    $xtpl->assign('DISABLED', 'disabled');
}else{
    $xtpl->assign('EMAIL','');
    $xtpl->assign('NAME', '');
}

//for thông tin quyên góp
foreach($thong_tin_quyen_gop as $key => $value){
    $value['so_tien_quyen_gop'] = $value['so_tien_quyen_gop'] * 23000;
    $xtpl->assign('KEY', $key+1);
    $xtpl->assign('VALUE', $value);
    $xtpl->parse('main.list_quyen_gop');
}

// for hình ảnh slide
foreach($list_anh as $key => $value){
    if($key == 0){
        $x = 'item active';
    }else{
        $x = 'item';
    }
    $xtpl->assign('ACTIVE', $x);
    $xtpl->assign('ANH', $value);
    $xtpl->parse('main.list_anh');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
