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
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file input, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file input, use with care
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
 * FIND METHODS DEFAULT LIMIT AND OFFSET
 * ---------------------------------------
 *
 * Constantes utilizadas para definir os valor padrao de limit e offset
 * dos metodos de busca dos models
 */
define('LIMIT_DEFAULT', 1000);
define('OFFSET_DEFAULT', 0);

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
define('USCSTAT_ATIVO', 'A');
define('USCSTAT_INATIVO', 'I');

/**---------------------------------------
 * AREAINTERESSE FIELDS RULES
 * ---------------------------------------
 *
 * Constantes que definem as regras de negocio dos campos da tabela AREAINTERESSE
 *
 */
define('AICDESC_MAX_LENGHT', 50);

/**---------------------------------------
 * GRUPO FIELDS RULES
 * ---------------------------------------
 *
 * Constantes que definem as regras de negocio dos campos da tabela GRUPO
 *
 */
define('GRCNOME_MAX_LENGTH', 50);
define('GRCTIPO_ABERTO', 'A');
define('GRCTIPO_FECHADO', 'F');
define('GRCSTAT_ATIVO', 'A');
define('GRCSTAT_INATIVO', 'I');

/**---------------------------------------
 * AVALIACAO FIELDS RULES
 * ---------------------------------------
 *
 * Constantes que definem as regras de negocio dos campos da tabela AVALIACAO
 *
 */
define('AVCSTAT_INICIADA', 'I');
define('AVCSTAT_FINALIZADA', 'F');

/**---------------------------------------
 * CONFIRMAREMAIL FIELDS RULES
 * ---------------------------------------
 *
 * Constantes que definem as regras de negocio dos campos da tabela CONFIRMAREMAIL
 *
 */
define('CACSTAT_ATIVO', 'A');
define('CACSTAT_INATIVO', 'I');

/**---------------------------------------
 * RECUPERARSENHA FIELDS RULES
 * ---------------------------------------
 *
 * Constantes que definem as regras de negocio dos campos da tabela RECUPERARSENHA
 *
 */
define('RSCSTAT_ATIVO', 'A');
define('RSCSTAT_INATIVO', 'I');

/**---------------------------------------
 * MATERIAL FIELDS RULES
 * ---------------------------------------
 *
 * Constantes que definem as regras de negocio dos campos da tabela MATERIAL
 *
 */
define('MACDESC_MAX_LENGTH', 100);
define('MACURL_MAX_LENGTH', 255);
define('MACTIPO_APRESENTACAO', 'A');
define('MACTIPO_EXERCICIO', 'E');
define('MACTIPO_FORMULA', 'F');
define('MACTIPO_JOGO', 'J');
define('MACTIPO_LIVRO', 'L');
define('MACTIPO_SIMULADO', 'S');
define('MACTIPO_VIDEO', 'V');
define('MACSTAT_ATIVO', 'A');
define('MACSTAT_INATIVO', 'I');
define('MACNIVL_BASICO', 'B');
define('MACNIVL_INTERMEDIARIO', 'I');
define('MACNIVL_AVANCADO', 'A');
define('MACNIVL_PROFISSIONAL', 'P');

/**---------------------------------------
 * CONTEUDO FIELDS RULES
 * ---------------------------------------
 *
 * Constantes que definem as regras de negocio dos campos da tabela CONTEUDO
 *
 */
define('COCDESC_MAX_LENGTH', 100);

/**---------------------------------------
 * DUVIDA FIELDS RULES
 * ---------------------------------------
 *
 * Constantes que definem as regras de negocio dos campos da tabela DUVIDA
 *
 */
define('DVCSTAT_ATIVO', 'A');
define('DVCSTAT_INATIVO', 'I');

/**---------------------------------------
 * DUVIDARESPOSTA FIELDS RULES
 * ---------------------------------------
 *
 * Constantes que definem as regras de negocio dos campos da tabela DUVIDARESPOSTA
 *
 */
define('DRCSTAT_ATIVO', 'A');
define('DRCSTAT_INATIVO', 'I');

/**---------------------------------------
 * COMENTARIO FIELDS RULES
 * ---------------------------------------
 *
 * Constantes que definem as regras de negocio dos campos da tabela COMENTARIO
 *
 */
define('CECSTAT_ATIVO', 'A');
define('CECSTAT_INATIVO', 'I');
define('CECANEX_MAX_LENGHT', 255);

/**---------------------------------------
 * MENSAGEM FIELDS RULES
 * ---------------------------------------
 *
 * Constantes que definem as regras de negocio dos campos da tabela MENSAGEM
 *
 */
define('MSCSTAT_ATIVO', 'A');
define('MSCSTAT_INATIVO', 'I');
define('MSCANEX_MAX_LENGHT', 255);

/**---------------------------------------
 * QUESTAO FIELDS RULES
 * ---------------------------------------
 *
 * Constantes que definem as regras de negocio dos campos da tabela QUESTAO
 *
 */
define('QECDESC_MAX_LENGHT', 50);
define('QECANEX_MAX_LENGHT', 255);

/**---------------------------------------
 * RESPOSTA FIELDS RULES
 * ---------------------------------------
 *
 * Constantes que definem as regras de negocio dos campos da tabela RESPOSTA
 *
 */
define('RECSTAT_ABERTA', 'A');
define('RECSTAT_FINALIZADA', 'F');

/**---------------------------------------
 * APP PATHS and DIR's
 * ---------------------------------------
 *
 * Constantes que definem os caminhos e diretorio de recursos (css, js, imagens, etc) utilizados pelo sistema.
 */
define('ROOT_DIR', 'c:/xampp/htdocs/projetos/sysmob/biblivirti/');

define('ASSETS_PATH', 'assets/');
define('ASSETS_CSS_PATH', ASSETS_PATH . 'css/');
define('ASSETS_FILES_PATH', ASSETS_PATH . 'files/');
define('ASSETS_FONTS_PATH', ASSETS_PATH . 'fonts/');
define('ASSETS_IMAGES_PATH', ASSETS_PATH . 'images/');
define('ASSETS_JS_PATH', ASSETS_PATH . 'js/');
define('ASSETS_MEDIAS_PATH', ASSETS_PATH . 'medias/');

define('UPLOAD_PATH', ASSETS_PATH . 'upload/');
define('UPLOAD_FILES_PATH', UPLOAD_PATH . 'files/');
define('UPLOAD_IMAGES_PATH', UPLOAD_PATH . 'images/');
define('UPLOAD_VIDEOS_PATH', UPLOAD_PATH . 'videos/');

/**---------------------------------------
 * INTENT CATEGORIES and ACTIONS
 * ---------------------------------------
 *
 * Constantes que definem as categorias e suas acoes para intencoes a serem enviadas para o app.
 *
 */
define('INTENT_CATEGORY_ACCOUNT', 'org.sysmob.biblivirti.intent.category.ACCOUNT');
define('INTENT_ACTION_ACCOUNT_PASSWORD_EDIT', 'org.sysmob.biblivirti.intent.action.ACCOUNT_PASSWORD_EDIT');


/**------------------------------------------------------------
 * EMAIl CONFIGURATION
 * ------------------------------------------------------------
 * Constantes que definem as configuracoes para envio de emails.
 *
 */
define('EMAIL_USERAGENT', 'CodeIgniter');
define('EMAIL_MAILTYPE', 'html');
define('EMAIL_PROTOCOL', 'smtp');
define('EMAIL_SMTP_HOST', 'ssl://smtp.googlemail.com');
define('EMAIL_SMTP_PORT', 465);
define('EMAIL_TIMEOUT', 20);
define('EMAIL_SMTP_USER', 'suporte.biblivirti@gmail.com');
define('EMAIL_SMTP_USER_ALIAS', 'Suporte Biblivirti AVAM');
define('EMAIL_SMTP_PASS', 'c3lzbW9iQGJpYmxpdmlydGkm'); // base64 encoded
define('EMAIL_VALIDATE', true);
define('EMAIL_WORDWRAP', true);
define('EMAIL_CHARTSET', 'utf-8');
define('EMAIL_NEWLINE', "\r\n");

/**
 * EMAIL SUBJECTS
 */
define('EMAIL_SUBJECT_EMAIL_CONFIRMATION', '[' . EMAIL_SMTP_USER_ALIAS . '] Confirmação de E-mail');
define('EMAIL_SUBJECT_ACCOUNT_ACTIVATED', '[' . EMAIL_SMTP_USER_ALIAS . '] Ativação de Conta');
define('EMAIL_SUBJECT_NEW_REGISTER', '[' . EMAIL_SMTP_USER_ALIAS . '] Nova Conta');
define('EMAIL_SUBJECT_NEW_GROUP', '[' . EMAIL_SMTP_USER_ALIAS . '] Novo Grupo');
define('EMAIL_SUBJECT_NEW_MATERIAL', '[' . EMAIL_SMTP_USER_ALIAS . '] Novo Material');
define('EMAIL_SUBJECT_NEW_MESSAGE', '[' . EMAIL_SMTP_USER_ALIAS . '] Nova Mensagem');
define('EMAIL_SUBJECT_NEW_COMMENT', '[' . EMAIL_SMTP_USER_ALIAS . '] Novo Comentário');
define('EMAIL_SUBJECT_NEW_ANSWER', '[' . EMAIL_SMTP_USER_ALIAS . '] Nova Resposta');
define('EMAIL_SUBJECT_NEW_MEMBER', '[' . EMAIL_SMTP_USER_ALIAS . '] Novo Membro');
define('EMAIL_SUBJECT_NEW_TEST', '[' . EMAIL_SMTP_USER_ALIAS . '] Nova Avaliação');
define('EMAIL_SUBJECT_NEW_DOUBT', '[' . EMAIL_SMTP_USER_ALIAS . '] Nova Dúvida');
define('EMAIL_SUBJECT_SHARE_MATERIAL', '[' . EMAIL_SMTP_USER_ALIAS . '] Compartilhamento de Material');
define('EMAIL_SUBJECT_SHARE_DOUBT', '[' . EMAIL_SMTP_USER_ALIAS . '] Compartilhamento de Dúvida');
define('EMAIL_SUBJECT_EDIT_GROUP', '[' . EMAIL_SMTP_USER_ALIAS . '] Edição de Grupo');
define('EMAIL_SUBJECT_EDIT_MATERIAL', '[' . EMAIL_SMTP_USER_ALIAS . '] Edição de Material');
define('EMAIL_SUBJECT_EDIT_PROFILE', '[' . EMAIL_SMTP_USER_ALIAS . '] Edição de Perfil');
define('EMAIL_SUBJECT_EDIT_COMMENT', '[' . EMAIL_SMTP_USER_ALIAS . '] Edição de Comentário');
define('EMAIL_SUBJECT_EDIT_ANSWER', '[' . EMAIL_SMTP_USER_ALIAS . '] Edição de Resposta');
define('EMAIL_SUBJECT_EDIT_DOUBT', '[' . EMAIL_SMTP_USER_ALIAS . '] Edição de Dúvida');
define('EMAIL_SUBJECT_DELETE_GROUP', '[' . EMAIL_SMTP_USER_ALIAS . '] Exclusão de Grupo');
define('EMAIL_SUBJECT_DELETE_MATERIAL', '[' . EMAIL_SMTP_USER_ALIAS . '] Exclusão de Material');
define('EMAIL_SUBJECT_DELETE_COMMENT', '[' . EMAIL_SMTP_USER_ALIAS . '] Exclusão de Comentário');
define('EMAIL_SUBJECT_DELETE_ANSWER', '[' . EMAIL_SMTP_USER_ALIAS . '] Exclusão de Resposta');
define('EMAIL_SUBJECT_DELETE_MEMBER', '[' . EMAIL_SMTP_USER_ALIAS . '] Exclusão de Membro');
define('EMAIL_SUBJECT_DELETE_DOUBT', '[' . EMAIL_SMTP_USER_ALIAS . '] Exclusão de Dúvida');
define('EMAIL_SUBJECT_PASSWORD_RECOVERY', '[' . EMAIL_SMTP_USER_ALIAS . '] Recuperação de Senha');
define('EMAIL_SUBJECT_PASSWORD_CHANGED', '[' . EMAIL_SMTP_USER_ALIAS . '] Alteração de Senha');
define('EMAIL_SUBJECT_EMAIL_MATERIAL', '[' . EMAIL_SMTP_USER_ALIAS . '] Convite para Ver um Material');
define('EMAIL_SUBJECT_TEST_FINALIZED', '[' . EMAIL_SMTP_USER_ALIAS . '] Avaliação Finalizada');

/**
 * EMAIL MESSAGES
 */
define('EMAIL_MESSAGE_EMAIL_CONFIRMATION', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><h3>Olá, {uscnome}</h3><p>Sua conta ainda não foi ativada.</p><p>Para ativá-la, você pode informar o código <strong>{cactokn}</strong> na tela de confirmação de e-mail<br>ou clicar no link abaixo:</p><p>Link de ativaçao: <a href="{confirmation_link}" target="blank">{confirmation_link}</a></p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p><body></body></html>');
define('EMAIL_MESSAGE_ACCOUNT_ACTIVATED', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Sua conta foi ativada com sucesso!</p><p>Acesse agora mesmo sua conta no Biblivirti AVAM.</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_NEW_REGISTER', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Sua conta foi criada com sucesso.</p><p>Para ativá-la, você pode informar o código <strong>{cactokn}</strong> na tela de confirmação de e-mail<br>ou clicar no link abaixo:</p><p>Link de ativação: <a href="{confirmation_link}" target="blank">{confirmation_link}</a></p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_NEW_GROUP', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Seu grupo <strong>{grcnome}</strong> foi criado com sucesso!</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_NEW_MEMBER', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Você acabou de ser adicionado(a) como membro no grupo <strong>{grcnome}</strong>.</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_NEW_MATERIAL', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Seu material <strong>{macdesc}</strong> foi adicionado no grupo <strong>{grcnome}</strong> com sucesso!</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_NEW_MESSAGE', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Sua mensagem foi envida para o grupo <strong>{grcnome}</strong> com sucesso!</p><p>Mensagem: <strong>{msctext}.</strong></p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_NEW_COMMENT', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Seu comentário foi adicionado no material <strong>{macdesc}</strong> do grupo <strong>{grcnome}</strong> com sucesso!</p><p>Comentário: <strong>{cectext}.</strong></p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_NEW_ANSWER', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Sua resposta foi adicionado no material <strong>{macdesc}</strong> do grupo <strong>{grcnome}</strong> com sucesso!</p><p>Resposta: <strong>{cectext}.</strong></p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_NEW_TEST', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Você acabou de iniciar uma avaliação do simulado <strong>{macdesc}</strong> no grupo <strong>{grcnome}</strong>.</p><p>Tente finalizá-la o mais breve possível, quanto mais rápido você terminar melhor será seu desempenho!</p><p>Número da Avaliação: <strong>{avnid}</strong><br/>Data de Ínicio: <em>{avdindt}</em></p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_NEW_DOUBT', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Sua dúvida no grupo <strong>{grcnome}</strong> foi adicionado com sucesso!</p><p>    Número da Dúvida: <strong>{dvnid}</strong><br/>    Texto: <em>{dvctext}</em><br/></p><p>Assim que sua dúvida for respondida lhe enviaremos um e-mail de notificação.</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_SHARE_MATERIAL', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Seu material <strong>{macdesc}</strong> foi compartilhado</p><p>com o grupo <strong>{grcnome}</strong> com sucesso!</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_SHARE_DOUBT', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Sua dúvida foi compartilhada no grupo <strong>{grcnome}</strong> com sucesso</p><p>Número da Dúvida: <strong>{dvnid}</strong><br/>Texto: <em>{dvctext}</em><br/></p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_EDIT_GROUP', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>As informações do grupo <strong>{grcnome}</strong> foram alteradas com sucesso!</p><p>Caso não tenha sido você que realizou esta alteração,<br>por favor, entre em contato com a equipe de suporte do Biblivirti AVAM.</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_EDIT_MATERIAL', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>As informações do material <strong>{macdesc}</strong> foram alteradas com sucesso!</p><p>Caso não tenha sido você que realizou esta alteração,<br>por favor, entre em contato com a equipe de suporte do Biblivirti AVAM.</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_EDIT_PROFILE', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>As informações do seu perfil foram atualizadas com sucesso!</p><p>Caso não tenha sido você que realizou esta alteração,<br>por favor, entre em contato com a equipe de suporte do Biblivirti AVAM.</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_EDIT_COMMENT', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Seu comentário no material <strong>{macdesc}</strong> do grupo <strong>{grcnome}</strong> foi editado com sucesso!</p><p>Novo Comentário: <strong>{cectext}.</strong></p><p>Caso não tenha sido você que realizou esta alteração,<br>por favor, entre em contato com a equipe de suporte do Biblivirti AVAM.</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_EDIT_ANSWER', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Seu resposta no material <strong>{macdesc}</strong> do grupo <strong>{grcnome}</strong> foi editada com sucesso!</p><p>Nova Resposta: <strong>{cectext}.</strong></p><p>Caso não tenha sido você que realizou esta alteração,<br>por favor, entre em contato com a equipe de suporte do Biblivirti AVAM.</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_EDIT_DOUBT', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Sua dúvida no grupo <strong>{grcnome}</strong> foi atualizada com sucesso!</p><p>Número da Dúvida: <strong>{dvnid}</strong><br/>Novo Texto: <em>{dvctext}</em><br/></p><p>Caso não tenha sido você que realizou esta alteração,<br>por favor, entre em contato com o administrador do seu grupo ou contacte a equipe de suporte do Biblivirti AVAM.</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_DELETE_GROUP', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>As informações do grupo <strong>{grcnome}</strong> foram excluidas com sucesso!</p><p>Caso não tenha sido você que realizou esta exclusão,<br>por favor, entre em contato com a equipe de suporte do Biblivirti AVAM.</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_DELETE_MATERIAL', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>As informações do material <strong>{macdesc}</strong> foram excluidas com sucesso!</p><p>Caso não tenha sido você que realizou esta exclusão,<br>por favor, entre em contato com a equipe de suporte do Biblivirti AVAM.</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_DELETE_COMMENT', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Seu comentário no material <strong>{macdesc}</strong> do grupo <strong>{grcnome}</strong> foi excluído com sucesso!</p><p>Comentário: <strong>{cectext}.</strong></p><p>Caso você não tenha conhecimento desta exclusão, por favor, contacte o administrador do grupo <br>ou entre em contato com a equipe de suporte do Biblivirti AVAM.</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_DELETE_ANSWER', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Sua resposta no material <strong>{macdesc}</strong> do grupo <strong>{grcnome}</strong> foi excluída com sucesso!</p><p>Resposta: <strong>{cectext}.</strong></p><p>Caso você não tenha conhecimento desta exclusão, por favor, contacte o administrador do grupo <br>ou entre em contato com a equipe de suporte do Biblivirti AVAM.</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_DELETE_MEMBER', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Você acabou de ser removido(a) da lista de membros do grupo <strong>{grcnome}</strong>.</p><p>Caso não tenha conhecimento desta operação, contacte o administrador do grupo ou</p><p>entre em contato com a equipe de suporte do Biblivirti AVAM.</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_DELETE_DOUBT', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Sua dúvida no grupo <strong>{grcnome}</strong> foi exclída com sucesso!</p><p>Número da Dúvida: <strong>{dvnid}</strong><br/>Texto: <em>{dvctext}</em><br/></p><p>Caso não tenha sido você que realizou esta exclusão,<br>por favor, entre em contato com o administrador do seu grupo ou contacte a equipe de suporte do Biblivirti AVAM.</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_PASSWORD_RECOVERY', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Para recuperar sua senha de acesso, você pode informar o código <strong>{rsctokn}</strong> na tela de confirmação de recuperação<br> ou clicar no link abaixo:</p><p>Link de recuperação: <a href="{recovery_link}" target="blank">{recovery_link}</a></p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_PASSWORD_CHANGED', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Sua senha foi alterada com sucesso.</p><p>Caso não tenha sido você que realizou esta alteração,<br>por favor, entre em contato com a equipe de suporte do Biblivirti AVAM.</p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');
define('EMAIL_MESSAGE_EMAIL_MATERIAL', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><h3>Olá, {uscnome}</h3><p><strong>{emitente}</strong> convidou você para ver o(a) {mactipo}: <strong>{macdesc}.</strong></p><p>Link: <a href="{material_link}" target="_blank">{material_link}</a></p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p><body></body></html>');
define('EMAIL_MESSAGE_TEST_FINALIZED', '<!doctype html><html lang="pt-br"><head><meta charset="UTF-8"><title>{email_smtp_user_alias}</title></head><body><h3>Olá, {uscnome}</h3><p>Parabéns, você acabou de finalizar uma avaliação do simulado <strong>{macdesc}</strong> no grupo <strong>{grcnome}</strong>.</p><p>Número da Avaliação: <strong>{avnid}</strong><br/>Data de Ínicio: <em>{avdindt}</em><br/>Data de Término: <em>{avdtedt}</em><br/>Qtd. de Questões: <strong>{manqtdqe}</strong><br/>Qtd. de Repostas: <strong>{avnqtdre}</strong><br/>Qtd. de Acertos: <strong>{avnqtdace}</strong><br/>Qtd. de Erros: <strong>{avnqtderr}</strong></p><p>Obrigado(a) por utilizar os serviços da nossa plataforma!</p><p>Att,<br><strong>{email_smtp_user_alias}</strong><br><em><a href="mailto:{email_smtp_user}">{email_smtp_user}</a></em><br></p><p>Enviado em: <em>{sending_date}</em></p></body></html>');

/**
 * EMAIL MESSAGES KEYS
 */
define('EMAIL_KEY_EMAIL_SMTP_USER_ALIAS', '{email_smtp_user_alias}');
define('EMAIL_KEY_EMAIL_SMTP_USER', '{email_smtp_user}');
define('EMAIL_KEY_USCNOME', '{uscnome}');
define('EMAIL_KEY_GRCNOME', '{grcnome}');
define('EMAIL_KEY_MACDESC', '{macdesc}');
define('EMAIL_KEY_MACTIPO', '{mactipo}');
define('EMAIL_KEY_MANQTDQE', '{manqtdqe}');
define('EMAIL_KEY_MSCTEXT', '{msctext}');
define('EMAIL_KEY_CECTEXT', '{cectext}');
define('EMAIL_KEY_CACTOKN', '{cactokn}');
define('EMAIL_KEY_RSCTOKN', '{rsctokn}');
define('EMAIL_KEY_AVNID', '{avnid}');
define('EMAIL_KEY_AVDINDT', '{avdindt}');
define('EMAIL_KEY_AVDTEDT', '{avdtedt}');
define('EMAIL_KEY_AVNQTDRE', '{avnqtdre}');
define('EMAIL_KEY_AVNQTDACE', '{avnqtdace}');
define('EMAIL_KEY_AVNQTDERR', '{avnqtderr}');
define('EMAIL_KEY_DVNID', '{dvnid}');
define('EMAIL_KEY_DVCTEXT', '{dvctext}');
define('EMAIL_KEY_EMITENTE', '{emitente}');
define('EMAIL_KEY_NOTIFICATION_MESSAGE', '{notification_message}');
define('EMAIL_KEY_RECOVERY_LINK', '{recovery_link}');
define('EMAIL_KEY_CONFIRMATION_LINK', '{confirmation_link}');
define('EMAIL_KEY_MATERIAL_LINK', '{material_link}');
define('EMAIL_KEY_SEDING_DATE', '{sending_date}');
