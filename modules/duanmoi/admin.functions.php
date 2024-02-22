<?php

/**
 * NukeViet Content Management System
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 3 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN')) {
    exit('Stop!!!');
}

$allow_func = [
    'main',
    'danhsach',
    'duankhongduyet',
    'danhsachclient',
    'danhsachyeucau',
];

define('NV_IS_FILE_ADMIN', true);

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

function update($id, $values, $tablename) {
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

function store($post , $databaseName) {
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

function first($value, $column , $databaseName) {
    global $db;
    $sql = "SELECT * FROM ". $databaseName ." WHERE ". $column ." = '". $value ."';";
    $data = $db->query($sql)->fetch();
    return $data;
}


function logs($data) {
    $tablename = 'nv4_vi_duanmoi_logs';
    store($data , $tablename);
}

