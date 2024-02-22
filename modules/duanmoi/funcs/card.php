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

global $user_info;
if(count($user_info) == 0) {
    nv_redirect_location('/nukeviet/users/login/'); 
}

$id_user = $nv_Request->get_int('id', 'get');

$user_check = first_client($id_user , 'user_id' ,  $table_profile );
$users      = first_client($id_user , 'userid' , 'nv4_users' );

$sql = "SELECT nv4_vi_duanmoi_card_profiles.email, nv4_vi_duanmoi_card_profiles.slogan, nv4_vi_duanmoi_card_profiles.instagram ,nv4_vi_duanmoi_card_profiles.facebook , nv4_users.first_name, SUM(nv4_vi_duanmoi_lich_su_quyen_gop.so_tien_quyen_gop) AS tong_tien, COUNT(nv4_vi_duanmoi_lich_su_quyen_gop.id_du_an) AS tong_du_an
FROM nv4_vi_duanmoi_card_profiles 
JOIN nv4_users ON nv4_vi_duanmoi_card_profiles.user_id = nv4_users.userid 
JOIN nv4_vi_duanmoi_lich_su_quyen_gop ON nv4_vi_duanmoi_lich_su_quyen_gop.id_nguoi_quyen_gop = nv4_users.userid
WHERE user_id = " . $id_user . "
GROUP BY nv4_vi_duanmoi_card_profiles.email, nv4_vi_duanmoi_card_profiles.slogan, nv4_vi_duanmoi_card_profiles.instagram, nv4_users.first_name";
$res = $db->query($sql)->fetch();
$res['tong_tien'] = $res['tong_tien'] * 23000;
$card = '';
$tr = '';

if($user_check) {
    $sql = "SELECT `nv4_vi_duanmoi_lich_su_quyen_gop`.`id_du_an`,`nv4_vi_duanmoi_lich_su_quyen_gop`.`ma_du_an`, `nv4_vi_duanmoi_themmoiduan`.ten_du_an,SUM(`so_tien_quyen_gop`) AS TONG_TIEN FROM `nv4_vi_duanmoi_lich_su_quyen_gop` JOIN `nv4_vi_duanmoi_themmoiduan` ON `nv4_vi_duanmoi_lich_su_quyen_gop`.`ma_du_an` = `nv4_vi_duanmoi_themmoiduan`.`ma_du_an` WHERE `id_nguoi_quyen_gop` = " . $user_check['user_id'] ." GROUP BY `id_du_an`, ten_du_an;";
    $res_1 = $db->query($sql);
    $lich_su_quyen_gop = $res_1->fetchAll();
    $lich_su_quyen_gop['tong_tien'] = $lich_su_quyen_gop['tong_tien'] * 23000;
    
    if ($lich_su_quyen_gop) {
        foreach ($lich_su_quyen_gop as $key => $value) {
            $href = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=chitietduan&amp;id=' . $value['id_du_an'];
            // print_r($value);die;
            $tr .= ' <tr>
            <th class="align-middle text-center">' . (1) . '</th>
            <th class="align-middle text-nowrap">' . $value['ma_du_an'] . '</th>
            <th class="align-middle text-nowrap">' . $value['ten_du_an'] . '</th>
            <th class="align-middle text-nowrap">' . $value['tong_tien'] . ' $</th>
            <th class="align-middle text-center text-nowrap">
               <a href="'. $href .'"><button class="btn btn-info">Đến dự án</button></a>
            </th>
            </tr>';
        }
    }
    
    $card = '<div class="profile-card js-profile-card">
                <div class="profile-card__img">
                <img src="/nukeviet/' . $users['photo'] . '" alt="profile card">
                </div>

                <div class="profile-card__cnt js-profile-cnt">
                <div class="profile-card__name">' . $res['first_name'] . '</div>
                <div class="profile-card__txt">' . $res['slogan'] . '</div>
                <div class="profile-card-inf">
                    <div class="profile-card-inf__item">
                    <div class="profile-card-inf__title">'. $res['tong_du_an'] .'</div>
                    <div class="profile-card-inf__txt">Số dự án đã quyên góp</div>
                    </div>

                    <div class="profile-card-inf__item">
                    <div class="profile-card-inf__title">'. $res['tong_tien'] .' đ</div>
                    <div class="profile-card-inf__txt">Tổng tiền đã quyên góp</div>
                    </div>
                </div>

                <div class="profile-card-social">
                    <a href="' . $res['facebook'] . '" class="profile-card-social__item facebook" target="_blank">
                    <span class="icon-font">
                        <svg class="icon"><use xlink:href="#icon-facebook"></use></svg>
                    </span>
                    </a>

                    <a href="' . $res['instagram'] . '" class="profile-card-social__item instagram" target="_blank">
                    <span class="icon-font">
                        <svg class="icon"><use xlink:href="#icon-instagram"></use></svg>
                    </span>
                    </a>

                </div>
                </div>
                <div class="table-responsive" style="padding-left: 30px; padding-right: 30px">
                    <table class="table table-bordered" id="myTable">
                        <thead class="bg-primary">
                            <tr class="text-center text-nowrap text-white">
                                <th class="align-middle">#</th>
                                <th class="align-middle">Mã Dự Án</th>
                                <th class="align-middle">Tên Dự Án</th>
                                <th class="align-middle">Số tiền đóng góp</th>
                                <th class="align-middle">Link Dự Án</th>
                            </tr>
                        </thead>
                        <tbody>' . $tr . '</tbody>
                    </table>
                </div>
            </div>';
}

$contents = '
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<style type="text/css">
    @import url("https://fonts.googleapis.com/css?family=Quicksand:400,500,700&subset=latin-ext");
    html {
    position: relative;
    overflow-x: hidden !important;
    }

    * {
    box-sizing: border-box;
    }

    body {
    font-family: "Quicksand", sans-serif;
    color: #324e63;
    }

    a, a:hover {
    text-decoration: none;
    }

    .icon {
    display: inline-block;
    width: 1em;
    height: 1em;
    stroke-width: 0;
    stroke: currentColor;
    fill: currentColor;
    }

    .wrapper {
    width: 100%;
    width: 100%;
    height: auto;
    min-height: 100vh;
    padding: 50px 20px;
    padding-top: 100px;
    display: flex;
    background-image: linear-gradient(-20deg, #ff2846 0%, #6944ff 100%);
    display: flex;
    background-image: linear-gradient(-20deg, #ff2846 0%, #6944ff 100%);
    }
    @media screen and (max-width: 768px) {
    .wrapper {
        height: auto;
        min-height: 100vh;
        padding-top: 100px;
    }
    }

    .profile-card {
    width: 100%;
    min-height: 460px;
    margin: auto;
    box-shadow: 0px 8px 60px -10px rgba(13, 28, 39, 0.6);
    background: #fff;
    border-radius: 12px;
    max-width: 700px;
    position: relative;
    }
    .profile-card.active .profile-card__cnt {
    filter: blur(6px);
    }
    .profile-card.active .profile-card-message,
    .profile-card.active .profile-card__overlay {
    opacity: 1;
    pointer-events: auto;
    transition-delay: 0.1s;
    }
    .profile-card.active .profile-card-form {
    transform: none;
    transition-delay: 0.1s;
    }
    .profile-card__img {
    width: 150px;
    height: 150px;
    margin-left: auto;
    margin-right: auto;
    transform: translateY(-50%);
    border-radius: 50%;
    overflow: hidden;
    position: relative;
    z-index: 4;
    box-shadow: 0px 5px 50px 0px #6c44fc, 0px 0px 0px 7px rgba(107, 74, 255, 0.5);
    }
    @media screen and (max-width: 576px) {
    .profile-card__img {
        width: 120px;
        height: 120px;
    }
    }
    .profile-card__img img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    }
    .profile-card__cnt {
    margin-top: -35px;
    text-align: center;
    padding: 0 20px;
    padding-bottom: 40px;
    transition: all 0.3s;
    }
    .profile-card__name {
    font-weight: 700;
    font-size: 24px;
    color: #6944ff;
    margin-bottom: 15px;
    }
    .profile-card__txt {
    font-size: 18px;
    font-weight: 500;
    color: #324e63;
    margin-bottom: 15px;
    }
    .profile-card__txt strong {
    font-weight: 700;
    }
    .profile-card-loc {
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 18px;
    font-weight: 600;
    }
    .profile-card-loc__icon {
    display: inline-flex;
    font-size: 27px;
    margin-right: 10px;
    }
    .profile-card-inf {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    align-items: flex-start;
    margin-top: 35px;
    }
    .profile-card-inf__item {
    padding: 10px 35px;
    min-width: 150px;
    }
    @media screen and (max-width: 768px) {
    .profile-card-inf__item {
        padding: 10px 20px;
        min-width: 120px;
    }
    }
    .profile-card-inf__title {
    font-weight: 700;
    font-size: 27px;
    color: #324e63;
    }
    .profile-card-inf__txt {
    font-weight: 500;
    margin-top: 7px;
    }
    .profile-card-social {
    margin-top: 25px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    }
    .profile-card-social__item {
    display: inline-flex;
    width: 55px;
    height: 55px;
    margin: 15px;
    border-radius: 50%;
    align-items: center;
    justify-content: center;
    color: #fff;
    background: #405de6;
    box-shadow: 0px 7px 30px rgba(43, 98, 169, 0.5);
    position: relative;
    font-size: 21px;
    flex-shrink: 0;
    transition: all 0.3s;
    }
    @media screen and (max-width: 768px) {
    .profile-card-social__item {
        width: 50px;
        height: 50px;
        margin: 10px;
    }
    }
    @media screen and (min-width: 768px) {
    .profile-card-social__item:hover {
        transform: scale(1.2);
    }
    }
    .profile-card-social__item.facebook {
    background: linear-gradient(45deg, #3b5998, #0078d7);
    box-shadow: 0px 4px 30px rgba(43, 98, 169, 0.5);
    }
    .profile-card-social__item.twitter {
    background: linear-gradient(45deg, #1da1f2, #0e71c8);
    box-shadow: 0px 4px 30px rgba(19, 127, 212, 0.7);
    }
    .profile-card-social__item.instagram {
    background: linear-gradient(45deg, #405de6, #5851db, #833ab4, #c13584, #e1306c, #fd1d1d);
    box-shadow: 0px 4px 30px rgba(120, 64, 190, 0.6);
    }
    .profile-card-social__item.behance {
    background: linear-gradient(45deg, #1769ff, #213fca);
    box-shadow: 0px 4px 30px rgba(27, 86, 231, 0.7);
    }
    .profile-card-social__item.github {
    background: linear-gradient(45deg, #333333, #626b73);
    box-shadow: 0px 4px 30px rgba(63, 65, 67, 0.6);
    }
    .profile-card-social__item.codepen {
    background: linear-gradient(45deg, #324e63, #414447);
    box-shadow: 0px 4px 30px rgba(55, 75, 90, 0.6);
    }
    .profile-card-social__item.link {
    background: linear-gradient(45deg, #d5135a, #f05924);
    box-shadow: 0px 4px 30px rgba(223, 45, 70, 0.6);
    }
    .profile-card-social .icon-font {
    display: inline-flex;
    }
    .profile-card-ctr {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 40px;
    }
    @media screen and (max-width: 576px) {
    .profile-card-ctr {
        flex-wrap: wrap;
    }
    }
    .profile-card__button {
    background: none;
    border: none;
    font-family: "Quicksand", sans-serif;
    font-weight: 700;
    font-size: 19px;
    margin: 15px 35px;
    padding: 15px 40px;
    min-width: 201px;
    border-radius: 50px;
    min-height: 55px;
    color: #fff;
    cursor: pointer;
    backface-visibility: hidden;
    transition: all 0.3s;
    }
    @media screen and (max-width: 768px) {
    .profile-card__button {
        min-width: 170px;
        margin: 15px 25px;
    }
    }
    @media screen and (max-width: 576px) {
    .profile-card__button {
        min-width: inherit;
        margin: 0;
        margin-bottom: 16px;
        width: 100%;
        max-width: 300px;
    }
    .profile-card__button:last-child {
        margin-bottom: 0;
    }
    }
    .profile-card__button:focus {
    outline: none !important;
    }
    @media screen and (min-width: 768px) {
    .profile-card__button:hover {
        transform: translateY(-5px);
    }
    }
    .profile-card__button:first-child {
    margin-left: 0;
    }
    .profile-card__button:last-child {
    margin-right: 0;
    }
    .profile-card__button.button--blue {
    background: linear-gradient(45deg, #1da1f2, #0e71c8);
    box-shadow: 0px 4px 30px rgba(19, 127, 212, 0.4);
    }
    .profile-card__button.button--blue:hover {
    box-shadow: 0px 7px 30px rgba(19, 127, 212, 0.75);
    }
    .profile-card__button.button--orange {
    background: linear-gradient(45deg, #d5135a, #f05924);
    box-shadow: 0px 4px 30px rgba(223, 45, 70, 0.35);
    }
    .profile-card__button.button--orange:hover {
    box-shadow: 0px 7px 30px rgba(223, 45, 70, 0.75);
    }
    .profile-card__button.button--gray {
    box-shadow: none;
    background: #dcdcdc;
    color: #142029;
    }
    .profile-card-message {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    padding-top: 130px;
    padding-bottom: 100px;
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s;
    }
    .profile-card-form {
    box-shadow: 0 4px 30px rgba(15, 22, 56, 0.35);
    width: 100%;
    margin-left: auto;
    margin-right: auto;
    height: 100%;
    background: #fff;
    border-radius: 10px;
    padding: 35px;
    transform: scale(0.8);
    position: relative;
    z-index: 3;
    transition: all 0.3s;
    }
    @media screen and (max-width: 768px) {
    .profile-card-form {
        max-width: 90%;
        height: auto;
    }
    }
    @media screen and (max-width: 576px) {
    .profile-card-form {
        padding: 20px;
    }
    }
    .profile-card-form__bottom {
    justify-content: space-between;
    display: flex;
    }
    @media screen and (max-width: 576px) {
    .profile-card-form__bottom {
        flex-wrap: wrap;
    }
    }
    .profile-card textarea {
    width: 100%;
    resize: none;
    height: 210px;
    margin-bottom: 20px;
    border: 2px solid #dcdcdc;
    border-radius: 10px;
    padding: 15px 20px;
    color: #324e63;
    font-weight: 500;
    font-family: "Quicksand", sans-serif;
    outline: none;
    transition: all 0.3s;
    }
    .profile-card textarea:focus {
    outline: none;
    border-color: #8a979e;
    }
    .profile-card__overlay {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    pointer-events: none;
    opacity: 0;
    background: rgba(22, 33, 72, 0.35);
    border-radius: 12px;
    transition: all 0.3s;
    }
</style>
<div class="wrapper">
    ' . $card . '
  
  </div>
  
  <svg hidden="hidden">
    <defs>
      <symbol id="icon-facebook" viewBox="0 0 32 32">
        <title>facebook</title>
        <path d="M19 6h5v-6h-5c-3.86 0-7 3.14-7 7v3h-4v6h4v16h6v-16h5l1-6h-6v-3c0-0.542 0.458-1 1-1z"></path>
      </symbol>
  
      <symbol id="icon-instagram" viewBox="0 0 32 32">
        <title>instagram</title>
        <path d="M16 2.881c4.275 0 4.781 0.019 6.462 0.094 1.563 0.069 2.406 0.331 2.969 0.55 0.744 0.288 1.281 0.638 1.837 1.194 0.563 0.563 0.906 1.094 1.2 1.838 0.219 0.563 0.481 1.412 0.55 2.969 0.075 1.688 0.094 2.194 0.094 6.463s-0.019 4.781-0.094 6.463c-0.069 1.563-0.331 2.406-0.55 2.969-0.288 0.744-0.637 1.281-1.194 1.837-0.563 0.563-1.094 0.906-1.837 1.2-0.563 0.219-1.413 0.481-2.969 0.55-1.688 0.075-2.194 0.094-6.463 0.094s-4.781-0.019-6.463-0.094c-1.563-0.069-2.406-0.331-2.969-0.55-0.744-0.288-1.281-0.637-1.838-1.194-0.563-0.563-0.906-1.094-1.2-1.837-0.219-0.563-0.481-1.413-0.55-2.969-0.075-1.688-0.094-2.194-0.094-6.463s0.019-4.781 0.094-6.463c0.069-1.563 0.331-2.406 0.55-2.969 0.288-0.744 0.638-1.281 1.194-1.838 0.563-0.563 1.094-0.906 1.838-1.2 0.563-0.219 1.412-0.481 2.969-0.55 1.681-0.075 2.188-0.094 6.463-0.094zM16 0c-4.344 0-4.887 0.019-6.594 0.094-1.7 0.075-2.869 0.35-3.881 0.744-1.056 0.412-1.95 0.956-2.837 1.85-0.894 0.888-1.438 1.781-1.85 2.831-0.394 1.019-0.669 2.181-0.744 3.881-0.075 1.713-0.094 2.256-0.094 6.6s0.019 4.887 0.094 6.594c0.075 1.7 0.35 2.869 0.744 3.881 0.413 1.056 0.956 1.95 1.85 2.837 0.887 0.887 1.781 1.438 2.831 1.844 1.019 0.394 2.181 0.669 3.881 0.744 1.706 0.075 2.25 0.094 6.594 0.094s4.888-0.019 6.594-0.094c1.7-0.075 2.869-0.35 3.881-0.744 1.050-0.406 1.944-0.956 2.831-1.844s1.438-1.781 1.844-2.831c0.394-1.019 0.669-2.181 0.744-3.881 0.075-1.706 0.094-2.25 0.094-6.594s-0.019-4.887-0.094-6.594c-0.075-1.7-0.35-2.869-0.744-3.881-0.394-1.063-0.938-1.956-1.831-2.844-0.887-0.887-1.781-1.438-2.831-1.844-1.019-0.394-2.181-0.669-3.881-0.744-1.712-0.081-2.256-0.1-6.6-0.1v0z"></path>
        <path d="M16 7.781c-4.537 0-8.219 3.681-8.219 8.219s3.681 8.219 8.219 8.219 8.219-3.681 8.219-8.219c0-4.537-3.681-8.219-8.219-8.219zM16 21.331c-2.944 0-5.331-2.387-5.331-5.331s2.387-5.331 5.331-5.331c2.944 0 5.331 2.387 5.331 5.331s-2.387 5.331-5.331 5.331z"></path>
        <path d="M26.462 7.456c0 1.060-0.859 1.919-1.919 1.919s-1.919-0.859-1.919-1.919c0-1.060 0.859-1.919 1.919-1.919s1.919 0.859 1.919 1.919z"></path>
      </symbol>
    </defs>
  </svg>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
  var messageBox = document.querySelector(".js-message");
  var btn = document.querySelector(".js-message-btn");
  var card = document.querySelector(".js-profile-card");
  var closeBtn = document.querySelectorAll(".js-message-close");

  btn.addEventListener("click",function (e) {
      e.preventDefault();
      card.classList.add("active");
  });

  closeBtn.forEach(function (element, index) {
     console.log(element);
      element.addEventListener("click",function (e) {
          e.preventDefault();
          card.classList.remove("active");
      });
  });
  </script>';

echo $contents;
