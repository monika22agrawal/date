<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

// define('DEFAULT_NO_IMG', 'noimagefound.jpg');
define('THEME_BUTTON', 'btn btn-primary');
define('THEME', ''); // skin-1, skin-2, skin-
define('EDIT_ICON', '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>');
define('DELETE_ICON', '<i class="fa fa-trash-o" aria-hidden="true"></i>');
define('ACTIVE_ICON', '<i class="fa fa-check" aria-hidden="true"></i>');
define('INACTIVE_ICON', '<i class="fa fa-times" aria-hidden="true"></i>');
define('VIEW_ICON', '<i class="fa fa-eye" aria-hidden="true"></i>');

/*Database table's constants*/
define('ADMIN', 'admin');
define('EDUCATION', 'educations');
define('USERS', 'users');
define('WORKS', 'works');
define('INTERESTS', 'interests');
define('USERS_EDUCATION', 'users_education_mapping');
define('USERS_IMAGE', 'users_image');
define('USERS_INTEREST_MAPPING', 'users_interest_mapping');
define('USERS_WORK', 'users_work_mapping');
define('APPOINTMENTS', 'appointments');
define('FAVORITES','favorites');
define('LIKES','likes');
define('REQUESTS','requests');
define('FRIENDS','friends');
define('NOTIFICATIONS', 'notifications');
define('EVENTS', 'events');
define('EVENT_MEMBER', 'event_member');
define('COMPANION_MEMBER', 'companion_member');
define('PAYMENT_TRANSACTIONS', 'payment_transactions');
define('BANK_ACCOUNT_DETAILS', 'bank_account_details');
define('VISITORS', 'visitors');
define('BUSINESS', 'business');
define('OPTIONS', 'options');
define('REVIEW', 'review');
define('EVENT_IMAGE', 'event_image');
define('PROFILE_IMAGE_LIKES', 'profile_Image_likes');

//AWS BUCKET S3 user credentials and URL
define('AWS_BUCKET_NAME','apoim-development');
define('AWS_BUCKET_REGION','us-east-2');
define('AWS_BUCKET_KEY','AKIAIBGBNRBDAFRJEGNA');
define('AWS_BUCKET_SECRET','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');
define('AWS_S3_URL','https://s3.us-east-2.amazonaws.com/apoim-development/'); //S3 bucket url
define('AWS_CDN_URL', 'https://d23c2k38r6z7a4.cloudfront.net/'); //cloud front url

//Frontend assets
define('AWS_CDN_FRONT_ASSETS', AWS_CDN_URL.'frontend_asset/');
define('AWS_CDN_FRONT_CSS', AWS_CDN_FRONT_ASSETS.'css/');
define('AWS_CDN_FRONT_FONTS', AWS_CDN_FRONT_ASSETS.'fonts/');
define('AWS_CDN_FRONT_IMG', AWS_CDN_FRONT_ASSETS.'img/');
define('AWS_CDN_FRONT_JS', AWS_CDN_FRONT_ASSETS.'js/');
define('AWS_CDN_FRONT_VENDORS', AWS_CDN_FRONT_ASSETS.'vendors/');

//Backend assets
define('AWS_CDN_BACK_ASSETS', AWS_CDN_URL.'backend_asset/');       //ADMIN_THEME
define('AWS_CDN_BACK_DIST_CSS', AWS_CDN_BACK_ASSETS.'dist/css');
define('AWS_CDN_BACK_DIST_JS', AWS_CDN_BACK_ASSETS.'dist/js/');
define('AWS_CDN_BACK_DIST_IMG', AWS_CDN_BACK_ASSETS.'dist/img/');
define('AWS_CDN_BACK_BUILD', AWS_CDN_BACK_ASSETS.'build/');
define('AWS_CDN_BACK_BOOTSTRAP_CSS', AWS_CDN_BACK_ASSETS.'bootstrap/css/');
define('AWS_CDN_BACK_BOOTSTRAP_JS', AWS_CDN_BACK_ASSETS.'bootstrap/js/');
define('AWS_CDN_BACK_BOOTSTRAP_FONTS', AWS_CDN_BACK_ASSETS.'bootstrap/fonts/');
define('AWS_CDN_BACK_CUSTOM_IMG', AWS_CDN_BACK_ASSETS.'custom/images/');
define('AWS_CDN_BACK_PLUGINS', AWS_CDN_BACK_ASSETS.'plugins/');

//App assets
define('APP_FRONT_ASSETS', 'app_frontend_asset/');
define('APP_BACK_ASSETS', 'app_backend_asset/');

//Uploads
define('AWS_CDN_UPLOAD', AWS_CDN_URL.'uploads/');  // UPLOAD_FOLDER

//user uploads
define('AWS_CDN_USER_IMG_PATH', AWS_CDN_UPLOAD.'profile/'); //original image       // USER_IMG_PATH      // ADMIN_IMAGE
define('AWS_CDN_USER_THUMB_IMG', AWS_CDN_USER_IMG_PATH.'thumb/'); //thumb          // USER_IMG_PATH_THUMB
define('AWS_CDN_USER_MEDIUM_IMG', AWS_CDN_USER_IMG_PATH.'medium/'); //thumb          // USER_IMG_PATH_THUMB
define('AWS_CDN_USER_LARGE_IMG', AWS_CDN_USER_IMG_PATH.'large/'); //thumb          // USER_IMG_PATH_THUMB
define('AWS_CDN_USER_PLACEHOLDER_IMG', AWS_CDN_BACK_CUSTOM_IMG.'user-place.png'); //User default image    //DEFAULT_USER

//event uploads
define('AWS_CDN_EVENT_IMG_PATH', AWS_CDN_UPLOAD.'event/'); //event original image     // EVENT_IMG_PATH
define('AWS_CDN_EVENT_THUMB_IMG', AWS_CDN_EVENT_IMG_PATH.'thumb/'); //thumb
define('AWS_CDN_EVENT_MEDIUM_IMG', AWS_CDN_EVENT_IMG_PATH.'medium/'); //thumb
define('AWS_CDN_EVENT_PLACEHOLDER_IMG', AWS_CDN_FRONT_IMG.'placeholder-image.png'); //Event default image    //DEFAULT_EVENT_IMAGE

//business uploads
define('AWS_CDN_BIZ_IMG_PATH', AWS_CDN_UPLOAD.'business/'); //biz original image     // BUSINESS_IMG_PATH
define('AWS_CDN_BIZ_THUMB_IMG', AWS_CDN_BIZ_IMG_PATH.'thumb/'); //biz original image     // BUSINESS_IMG_PATH
define('AWS_CDN_BIZ_PLACEHOLDER_IMG', AWS_CDN_FRONT_IMG.'placeholder-image.png'); //biz default image    //BUSINESS_DEFAULT_IMG

//other uploads
define('AWS_CDN_IDPROOF_IMG_PATH', AWS_CDN_UPLOAD.'idProof/'); //id proof original image     // IDPROOF_IMG_PATH
define('AWS_CDN_IDPROOF_THUMB_IMG', AWS_CDN_IDPROOF_IMG_PATH.'thumb/'); //id proof original image     // IDPROOF_IMG_PATH
define('AWS_CDN_FACE_VERIFY_IMG_PATH', AWS_CDN_UPLOAD.'faceProof/'); //face verification original image     // FACEPROOF_IMG_PATH
define('TC_PDF', AWS_CDN_UPLOAD.'pdf/');

//application icons and images
define('MAP_USER', AWS_CDN_BACK_CUSTOM_IMG. 'new_map_icon.png');
define('MAP_ICON_MAIL', AWS_CDN_FRONT_IMG. 'ico_map_male.png');
define('MAP_USER_FEMAIL', AWS_CDN_FRONT_IMG. 'ico_map_female.png');
define('MAP_USER_TRANSGENDER', AWS_CDN_FRONT_IMG.'ico_map_transgender.png');

// Appointment Icons
define('App_ICON_MAIL', AWS_CDN_FRONT_IMG.'ico_current_red.png');
define('App_USER_FEMAIL', AWS_CDN_FRONT_IMG.'ico_current_purple.png');
define('App_USER_TRANSGENDER', AWS_CDN_FRONT_IMG.'ico_current_orange.png');
define('App_Meeting', AWS_CDN_FRONT_IMG.'mapicon.jpg');


/*Firebase Detail*/
define('FIREBASE_API_KEY', 'AIzaSyC55fzVWcs1yF8QvLtDkQj5ENXUvuVKTVI');
define('FIREBASE_AUTH_DOMAIN', 'apoimlocal.firebaseapp.com');
define('FIREBASE_DB_URL', 'https://apoimlocal.firebaseio.com');
define('FIREBASE_PROJECT_ID', 'apoimlocal');
define('FIREBASE_STORAGE_BUCKET', 'apoimlocal.appspot.com');
define('FIREBASE_MESSAGING_SENDER_ID', '659616662103');
/*Firebase Detail*/

/* SMTP Details*/
define('SMTP_USERNAME', 'AKIAIBA4MQVQTL2EBZIA');
define('SMTP_PASSWORD', 'BIfXZZOvRe6ibkWZYAa9Cq3bcIpbsS+JYMC2Lqk84Z/c');
define('SITE_NAME', 'APOIM');
/* SMTP Details*/

/* Image Folder Path*/
//define('UPLOAD_FOLDER', 'uploads/');
//define('USER_IMG_PATH','uploads/profile/');
//define('USER_IMG_PATH_THUMB','uploads/profile/thumb/');
//define('TC_PDF', 'uploads/pdf/');
//define('BUSINESS_DEFAULT_IMG','frontend_asset/img/placeholder-image.png');
//define('BUSINESS_IMG_PATH','uploads/business/thumb/');
//define('IDPROOF_IMG_PATH','uploads/idProof/');
//define('FACEPROOF_IMG_PATH','uploads/faceProof/');
//define('EVENT_IMG_PATH','uploads/event/');
//define('DEFAULT_EVENT_IMAGE','frontend_asset/img/placeholder-image.png');

//define('DEFAULT_USER','backend_asset/custom/images/user-place.png');
//define('MAP_USER','backend_asset/custom/images/new_map_icon.png');

//define('MAP_ICON_MAIL','frontend_asset/img/ico_map_male.png');
//define('MAP_USER_FEMAIL','frontend_asset/img/ico_map_female.png');
//define('MAP_USER_TRANSGENDER','frontend_asset/img/ico_map_transgender.png');

// Appointment Icons
//define('App_ICON_MAIL','frontend_asset/img/ico_current_red.png');
//define('App_USER_FEMAIL','frontend_asset/img/ico_current_purple.png');
//define('App_USER_TRANSGENDER','frontend_asset/img/ico_current_orange.png');
//define('App_Meeting','frontend_asset/img/mapicon.jpg');


//Firebase dev API key for notifications
define('NOTIFICATION_KEY','AAAALR6zplM:APA91bGj1zisxQBRbJJVfgUNqtF12qus_dwXeEMObpOa97FwY1AwhX-kT9-1miydk2_iZfNDZ-ti77Q_70XkcP4Pq3tpAOCOY-osULZuoawj3l8Ny4ui_hHMvBdFRGVZ62039d1v9aL-hZpgbCvssPKk8AV0j4G4rQ');

//Firebase live API key for notifications
/*define('NOTIFICATION_KEY','AAAAw2tm2SA:APA91bHyNfeMWWthWwQ9AN8K21A_wqyHKpmGyUExnM8wbBsHXT6tZas8mE7Ur4ThmhgX33F_j1n2cRXcJ934bH_MNv5e0KOAY9uT-GFlwo7uaWb_DmHc_D0b3k8hbK2TEACcm1zbjU--');*/

/*Google Place Api Key*/
//define('GOOGLE_API_KEY', 'AIzaSyD75NTMwmUIl1voS3YZnfqLHTTPP6uXHV4'); // created by apoim123@gmail.com
define('GOOGLE_API_KEY', 'AIzaSyAdh4QTYJ8-ucFmkhhUD8vUZXOCHY3lyqI');   // created by apoimdat@gmail.com
define('FACEBOOK_APP_ID', '780852365605124');    // client's a/c
//define('FACEBOOK_APP_ID', '464296957352010');  // mindiii's a/c

/* Stripe Accounts Detail*/
define('STRIPE_PUBLISH_KEY', 'pk_test_QCwLHJXgVrZfIuGYFYRNY2eJ');
define('STRIPE_SECRET_KEY', 'sk_test_jVM872jPfk462GPwYDH7mr84');
define('STRIPE_PLAN_ID', 'plan_D78Fso1yxuQq2b');
define('BUSINESS_PLAN_ID', 'plan_DK92aozpdTVwkC');

/* Face++ Accounts Detail*/ 
// created by appoim123@gmail.com
/*define('FACE_API_KEY', 'vrZ0CLT0wravpitF1xmK7TeJ9s9hOXbl');         	
define('FACE_API_SECRET_KEY', '7v7DbT8dQawW4UljhwfhV5mtmmlwomE_');*/

// created by apoimdate@gmail.com
define('FACE_API_KEY', '1RdZLTBX80a1qRHsYpvDNz3XO3gP0d74');         	
define('FACE_API_SECRET_KEY', 'gUPKb9ITPGrK2AxPUfRhPsB9D7q4Ipn_');

/* APIS Status*/
define('FAIL','fail');
define('SUCCESS','success');
define('OK',200);
define('SERVER_ERROR',400);
//define('ADMIN_DEFAULT_IMAGE','backend_asset/dist/img/avatar5.png');
//define('ADMIN_IMAGE','uploads/profile/');
//define('ADMIN_THEME', 'backend_asset/');
