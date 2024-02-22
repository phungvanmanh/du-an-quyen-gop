<?php

/**
 * NukeViet Content Management System
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 3 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_SYSTEM')) {
    exit('Stop!!!');
}

define('NV_IS_MOD_PAGE', true);

function request($post , $array_name)
{
    $error = [];
    foreach ($post as $key => $value) {
        if(empty($value) || $value == 'undefined') {
            if(isset($array_name[$key])) {
                $error[] = $array_name[$key] . ' không được để trống!';
            }
        }
    }
    return $error;
}

function createSlug($str) {
    $str = trim(mb_strtolower($str));
    $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
    $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
    $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
    $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
    $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
    $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
    $str = preg_replace('/(đ)/', 'd', $str);
    $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
    $str = preg_replace('/([\s]+)/', '-', $str);
    return $str;
}

function update_client($id, $values, $tablename) {
    global $db;
    array_walk($values, function(&$value, $key){
        $value = "{$key} = '{$value}'";
    });
    $sUpdate = implode(", ", array_values($values));

    $sql        = "UPDATE `$tablename` SET $sUpdate WHERE id = $id";
    try {
        $db->query($sql);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function store_client($post , $databaseName) {
    global $db;
    $values = implode("', '", $post);
    $columns = implode(", ", array_keys($post));
    $sql = "INSERT INTO `$databaseName`($columns) VALUES ('$values')";
    try {
        $db->query($sql);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function first_client($value, $column , $databaseName) {
    global $db;
    $sql = "SELECT * FROM ". $databaseName ." WHERE ". $column ." = '". $value ."';";
    $data = $db->query($sql)->fetch();
    return $data;
}

function get_data_client($value, $column , $databaseName ) {
    global $db;
    $sql = "SELECT * FROM ". $databaseName ." WHERE ". $column ." = '". $value ."';";
    $data = $db->query($sql)->fetchAll();
    return $data;
}


function sendMailClient($post){
    $message = '<h1>Tài Khoản Quyên Góp</h1>
        <div class="row">
            <div class="col-md-6">
                <div class="alert alert-primary" role="alert">
                    Cảm ơn bạn đã có lòng thành quyên góp cho dự án của chúng tôi. Dưới đây là tài khoản đăng nhập để quý vị có thể theo dõi nhiều dự án khác. Xin Chân Thành Cảm Ơn!!
                </div>
                <div class="card">
                    <div class="card-header bg-primary">
                        Thông tin tài khoản
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Email:</th>
                                    <th>'. $post['email'] .'</th>
                                </tr>
                                <tr>
                                    <th>User:</th>
                                    <th>'. $post['username'] .'</th>
                                </tr>
                                <tr>
                                    <th>Mật khẩu:</th>
                                    <th>123123</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>';
        $from = [];
    nv_sendmail($from, $post['email'], 'Thông báo', $message);
}

function alert($mess , $style = 1) {
    $array = [
        'danger',
        'success'
    ];
    return '<div class="alert alert-'. $array[$style] .'" role="alert">'. $mess .'</div>';
}
