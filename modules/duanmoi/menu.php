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

// $sql = 'SELECT id, title, alias FROM ' . NV_PREFIXLANG . '_' . $mod_data . ' WHERE status=1 ORDER BY weight ASC';
// $result = $db->query($sql);
$arr = [
    [ 'id' => 2,
    'title' => 'Tạo dự án',
    'alias' => 'taoduan',  
    ],
    
    [ 'id' => 3,
    'title' => 'Lịch Sử Quyên Góp',
    'alias' => 'profile',  
    ],

    [ 'id' => 4,
    'title' => 'Danh sách dự án',
    'alias' => 'danhsachcacduan',  
    ],

    [ 'id' => 5,
    'title' => 'Cấu hình Card Profile',
    'alias' => 'cardinfo',  
    ],

    [ 'id' => 6,
    'title' => 'Card',
    'alias' => 'card',  
    ],
];  
foreach($arr as $key => $value){
    $array_item[$value['id']] = [
        'key' => $value['id'],
        'title' => $value['title'],
        'alias' => $value['alias'] . $global_config['rewrite_exturl']
    ];
}
    
