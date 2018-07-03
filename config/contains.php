<?php
/**
	* Contains define store.
	* User: son_jp
	* Date: 8/23/2017
	* Time: 11:01 AM
	*/

return [
   'RECORD_PER_PAGE' => 10, // default number of items per page
   'NUMBER_ENTRIES' => [10, 25, 50, 100],
   '_ACCESS_TOKEN' => 'ysI2uuBRFsdpBQlgzLzuVKMleaVKSBZybif9cbI9oRObrhGvuQ368uPe19cY',//token access api
   'CALLING_CODE' => [
        '+84' => 'Vietnam (+84)',
        '+81' => 'Japan (+81)',
        '+44' => 'UK (+44)'
    ],
   'TARGET_UPLOAD_DIR' => 'upload/avatar/',
   'SRC_AVATAR_DEFAULT' => 'images/avatar_default.png',
   'ID_RESOURCE' => [
       '0' => 'LDap server',
	   '1' => 'ARMS System',
	   '2' => 'Mtool',
	   '3' => 'Portal'
	],
	'RESOURCE_DATA'=> [
        'ldap_server' => 0,
        'arms_system' => 1,
        'mtool' => 2,
        'portal' => 3
    ],
	'API_RESOURCE' => [
        'project_mtool' => 'http://measurement.co-well.com.vn/api/getProject',
        'project_user_mtool' =>'http://measurement.co-well.com.vn/api/getEntryTime/',
        'member_portal' => 'http://172.16.90.21:8080/api/member.php',
        'department_portal' => 'http://172.16.90.21:8080/api/department.php'
    ],
    'ID_PROJECT_RESOURCE' => [ //SonNA
        'ARMS System' => 1,
        'Mtool' => 2
    ],
    'ID_CLIENT_RESOURCE' => [ //SonNA
        //'Other' => 0,
        'ARMS System' => 1
    ],
    'ROLE_USER' => [
        'admin' => 'Admin',
        'manager' => 'Manager'
    ],
    'ID_MTOOL_RESOURCE' => [ // HueNT
        '1' => '', // idom_backlog
        '2' => 'http://sysredmine.idc.golfdigest.co.jp/projects/', // gdo_redmine
        '3' => 'https://redmine02.co-well.jp/projects/', // redmine_02
        '4' => 'http://redmine.co-well.com.vn/redmine/projects/' // cowell_redmine
    ],
    'STANDARD_TIME' => 8 // standard time one day = 8h
];