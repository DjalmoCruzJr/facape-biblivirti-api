<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no input. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

/**
 * BASIC CONFIGS
 */
$route['default_controller'] = 'account';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/**
 * API ACCOUNT ROUTES
 */
$route['API/account/login']['post'] = 'api/account/login';
$route['API/account/login/facebook']['post'] = 'api/account/login_facebook';
$route['API/account/register']['post'] = 'api/account/register';
$route['API/account/recovery']['post'] = 'api/account/recovery';
$route['API/account/email/confirmation']['get'] = 'api/account/email_confirmation';
$route['API/account/password/reset']['get'] = 'api/account/password_reset';
$route['API/account/password/edit']['post'] = 'api/account/password_edit';
$route['API/account/profile']['post'] = 'api/account/profile';
$route['API/account/profile/edit']['post'] = 'api/account/profile_edit';
$route['API/account/search']['post'] = 'api/account/search';
$route['API/account/password/update']['post'] = 'api/account/password_update';

/**
 * API AREA OF INTEREST ROUTES
 */
$route['API/areaofinterest/list']['post'] = 'api/areaofinterest/list_all';
$route['API/areaofinterest/add']['post'] = 'api/areaofinterest/add';

/**
 * API GROUP ROUTES
 */
$route['API/group/list']['post'] = 'api/group/list_all';
$route['API/group/get']['post'] = 'api/group/get';
$route['API/group/add']['post'] = 'api/group/add';
$route['API/group/edit']['post'] = 'api/group/edit';
$route['API/group/delete']['post'] = 'api/group/delete';
$route['API/group/info']['post'] = 'api/group/info';
$route['API/group/search']['post'] = 'api/group/search';
$route['API/group/subscribe']['post'] = 'api/group/subscribe';
$route['API/group/unsubscribe']['post'] = 'api/group/unsubscribe';

/**
 * API MATERIAL ROUTES
 */
$route['API/material/list']['post'] = 'api/material/list_all';
$route['API/material/add']['post'] = 'api/material/add';
$route['API/material/edit']['post'] = 'api/material/edit';
$route['API/material/delete']['post'] = 'api/material/delete';
$route['API/material/search']['post'] = 'api/material/search';
$route['API/material/email']['post'] = 'api/material/email';
$route['API/material/share']['post'] = 'api/material/share';
$route['API/material/details']['post'] = 'api/material/details';

/**
 * API CONTENT ROUTES
 */
$route['API/content/list']['post'] = 'api/content/list_all';
$route['API/content/add']['post'] = 'api/content/add';
$route['API/content/edit']['post'] = 'api/content/edit';
$route['API/content/material/list']['post'] = 'api/content/material_contents_list';

/**
 * API MESSAGE ROUTES
 */
$route['API/message/list']['post'] = 'api/message/list_all';
$route['API/message/add']['post'] = 'api/message/add';

/**
 * API COMMENT ROUTES
 */
$route['API/comment/add']['post'] = 'api/comment/add';
$route['API/comment/edit']['post'] = 'api/comment/edit';
$route['API/comment/delete']['post'] = 'api/comment/delete';

/**
 * API QUESTION ROUTES
 */
$route['API/question/list']['post'] = 'api/question/list_all';
$route['API/question/add']['post'] = 'api/question/add';
$route['API/question/edit']['post'] = 'api/question/edit';
$route['API/question/delete']['post'] = 'api/question/delete';

/**
 * API ALTERNATIVE ROUTES
 */
$route['API/alternative/add']['post'] = 'api/alternative/add';
$route['API/alternative/edit']['post'] = 'api/alternative/edit';
$route['API/alternative/delete']['post'] = 'api/alternative/delete';

/**
 * API TEST ROUTES
 */
$route['API/test/start']['post'] = 'api/test/start';
$route['API/test/finalize']['post'] = 'api/test/finalize';

/**
 * API ANSWER ROUTES
 */
$route['API/answer/list']['post'] = 'api/answer/list_all';
$route['API/answer/add']['post'] = 'api/answer/add';
$route['API/answer/submit']['post'] = 'api/answer/submit';

/**
 * API DOUBT ROUTES
 */
$route['API/doubt/list']['post'] = 'api/doubt/list_all';
$route['API/doubt/add']['post'] = 'api/doubt/add';
$route['API/doubt/edit']['post'] = 'api/doubt/edit';
$route['API/doubt/delete']['post'] = 'api/doubt/delete';
$route['API/doubt/share']['post'] = 'api/doubt/share';
$route['API/doubt/details']['post'] = 'api/doubt/details';
