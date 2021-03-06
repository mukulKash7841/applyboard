<?php

// secret key for generating token...
define('Secret_Key','pjXTrQU0fARSqBXe_Q7p9RP1ZZ0');

// facebook auth key...
define('Facebook_Oauth_Key','HwAr2OtSxRgEEnO2-JnYjsuA3tc');

// google auth key...
define('Google_Oauth_Key','9s0oa6wq-djZGZXRzmY0xWJejOw');

// base url of all pages...
define('base_url','http://localhost/wordpress/wordpress/index.php/');


// school assets url...
define('school_asset_url',content_url('plugins/school/assets/'));

// course assets url...
define('course_asset_url',content_url('plugins/Course/assets/'));

// student assets url...
define('student_asset_url',content_url('plugins/student/assets'));

define('admin_asset_url',content_url('plugins/admin/assets/'));

define('agent_asset_url',content_url('plugins/agent/assets/'));

define('staff_asset_url',content_url('plugins/staff/assets/'));


// algorithm for generating token...
define('algo',['HS256']);

// if the token is expired...
define('Expiry_Token_Error',109);

// if the key is empty when decoding token...
define('Key_Empty',110);

// if wrong segments are passed when decoding token...
define('Wrong_Segments',111);

// if no valid algorithm passed for decoding token...
define('Invalid_Encoding',112);

// if wrong algorithm passed for decoding token...
define('Wrong_Algorithm',113);

// if signature not verified...
define('Signature_Failed',114);

// if a wrong date is found while decoding token...
define('Wrong_Date',115);

// if the server response is success...
define('Success_Code',200);

// if there is an error response...
define('Error_Code',400);

// if the user is not verified...
define('Not_Verified',209);

// if the user account is deactivated...
define('Account_Deactive',120);
