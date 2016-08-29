<?php defined('BASEPATH') OR exit('No direct script access allowed');

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
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

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
define('SHOW_DEBUG_BACKTRACE', TRUE);

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
define('EXIT_SUCCESS', 0); // no errors
define('EXIT_ERROR', 1); // generic error
define('EXIT_CONFIG', 3); // configuration error
define('EXIT_UNKNOWN_FILE', 4); // file not found
define('EXIT_UNKNOWN_CLASS', 5); // unknown class
define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
define('EXIT_USER_INPUT', 7); // invalid user input
define('EXIT_DATABASE', 8); // database error
define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/**---------------------------------------
 * APP HASH GENERATION KEY
 * ---------------------------------------
 *
 * Constante utilizada como chave para geracao de HASH das senha dos usuarios
 *
 */
define('BIBLIVIRTI_HASH_KEY', 'sysmob@biblivirti&');

/**---------------------------------------
 * HTTP RESPONSE CODES
 * ---------------------------------------
 *
 * Constantes utilizadas para serem retornadas como codigos de resposta
 * das requisicoes recebidas pela API
 *
 */
define('RESPONSE_CODE_OK', 200);
define('RESPONSE_CODE_BAD_REQUEST', 400);
define('RESPONSE_CODE_UNAUTHORIZED', 401);
define('RESPONSE_CODE_NOT_FOUND', 404);


/**---------------------------------------
 * USUARIO FIELDS RULES
 * ---------------------------------------
 *
 * Constantes que definem as regras de negocio dos campos da tabela USUARIO
 *
 */
define('USCNOME_MAX_LENGTH', 50);
define('USCMAIL_MAX_LENGTH', 50);
define('USCLOGN_MAX_LENGTH', 50);
define('USCSENH_MAX_LENGTH', 50);
define('USCFBID_MAX_LENGTH', 100);

/**---------------------------------------
 * GRUPO FIELDS RULES
 * ---------------------------------------
 *
 * Constantes que definem as regras de negocio dos campos da tabela GRUPO
 *
 */
define('GRCNOME_MAX_LENGTH', 50);
define('GRCTIPO_MAX_LENGTH', 1);
define('GRCTIPO_ABERTO', 'A');
define('GRCTIPO_FECHADO', 'F');

/**---------------------------------------
 * RECUPERARSENHA FIELDS RULES
 * ---------------------------------------
 *
 * Constantes que definem as regras de negocio dos campos da tabela GRUPO
 *
 */
define('RSCSTAT_ATIVO', 'A');
define('RSCSTAT_INATIVO', 'I');

/**---------------------------------------
 * UPLOAD PATHS
 * ---------------------------------------
 *
 * Constantes que definem o caminho do diretorio de armazenamento de upload de arquivos do sistema
 *
 */
define('UPLOAD_PATH', '_upload/');
define('UPLOAD_FILES_PATH', UPLOAD_PATH . 'files/');
define('UPLOAD_IMAGES_PATH', UPLOAD_PATH . 'images/');
define('UPLOAD_VIDEOS_PATH', UPLOAD_PATH . 'videos/');

/**---------------------------------------
 * ASSETS PATHS
 * ---------------------------------------
 *
 * Constantes que definem o caminho do diretorio de recursos (css, js, imagens, etc) utilizados pelo sistema.
 */
define('ASSETS_PATH', 'assets/');
define('ASSETS_CSS_PATH', ASSETS_PATH . 'css/');
define('ASSETS_JS_PATH', ASSETS_PATH . 'js/');
define('ASSETS_FONTS_PATH', ASSETS_PATH . 'fonts/');
define('ASSETS_FILES_PATH', ASSETS_PATH . 'files/');
define('ASSETS_IMAGES_PATH', ASSETS_PATH . 'images/');
define('ASSETS_MEDIAS_PATH', ASSETS_PATH . 'medias/');

/**---------------------------------------
 * INTENT CATEGORIES and ACTIONS
 * ---------------------------------------
 *
 * Constantes que definem as categorias e suas acoes para intencoes a serem enviadas para o app.
 *
 */
define('INTENT_CATEGORY_ACCOUNT', 'org.sysmob.biblivirti.intent.category.ACCOUNT');
define('INTENT_ACTION_ACCOUNT_PASSWORD_RESET', 'org.sysmob.biblivirti.intent.ACTION.ACCOUNT_PASSWORD_RESET');
