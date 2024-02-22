<?php

/**
 * NukeViet Content Management System
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 3 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_MODULES')) {
    exit('Stop!!!');
}

$sql_drop_module = [];

$sql_drop_module[] = 'DROP TABLE IF EXISTS ' . $db_config['prefix'] . '_' . $lang . '_' . $module_data . '_themmoiduan;';
$sql_drop_module[] = 'DROP TABLE IF EXISTS ' . $db_config['prefix'] . '_' . $lang . '_' . $module_data . '_lich_su_quyen_gop;';
$sql_drop_module[] = 'DROP TABLE IF EXISTS ' . $db_config['prefix'] . '_' . $lang . '_' . $module_data . '_card_profiles;';

$sql_create_module = $sql_drop_module;

$sql_create_module[] = 'CREATE TABLE ' . $db_config['prefix'] . '_' . $lang . '_' . $module_data . "_themmoiduan (
 id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
 ma_du_an varchar(250) NULL,
 ten_du_an varchar(250) NOT NULL,
 slug_du_an varchar(250) NOT NULL,
 mo_ta_ngan longtext NOT NULL,
 so_tien int(11) NOT NULL,
 mo_ta_chi_tiet longtext NOT NULL,
 hinh_anh text NOT NULL,
 thoi_han date NOT NULL,
 is_open int(11) NOT NULL,
 is_duyet int(11) DEFAULT 3,
 id_nguoi_tao int(11) NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (id),
 UNIQUE KEY slug_du_an (slug_du_an)
) ENGINE=MyISAM";


$sql_create_module[] = 'CREATE TABLE ' . $db_config['prefix'] . '_' . $lang . '_' . $module_data . "_lich_su_quyen_gop (
    id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    transaction_id varchar(250) NULL,
    full_name varchar(250) NOT NULL,
    ma_du_an varchar(250) NOT NULL,
    id_du_an int(11) NOT NULL,
    id_nguoi_quyen_gop int(11) NOT NULL,
    email_quyen_gop varchar(250) NOT NULL,
    so_tien_quyen_gop double NOT NULL,
    ngay_quyen_gop datetime NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=MyISAM";

$sql_create_module[] = 'CREATE TABLE ' . $db_config['prefix'] . '_' . $lang . '_' . $module_data . "_card_profiles (
    id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    user_id varchar(250) NOT NULL,
    email varchar(250) NOT NULL,
    slogan varchar(250) NULL,
    facebook varchar(250) NULL,
    instagram varchar(250) NULL,
    link_card varchar(250) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=MyISAM";

