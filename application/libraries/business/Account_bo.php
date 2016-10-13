<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 28/08/2016
 *
 * Classe para aplicar as regras de negocio inerentes as operacoes do controller <i>Account</i>.
 */
class Account_bo {

    protected $CI;

    /**
     * @var array
     *
     * Recebe os dados a serem validados.
     */
    private $data;

    /**
     * @var array
     *
     * Armazena os erros inerentes ao processo de validacao.
     */
    private $errors;

    /**
     * Account_bo constructor.
     */
    public function __construct() {
        // Loading variables
        $this->data = [];
        $this->errors = [];
        $this->CI =& get_instance();

        // Loading models
        $this->CI->load->model('usuario_model');
    }

    /**
     * @param array $data
     *
     * Metodo para setar os dados a serem validados.
     */
    public function set_data($data) {
        $this->data = $data;
    }

    /**
     * @param array $data
     *
     * Metodo para retornar os dados apos serem validados.
     */
    public function get_data() {
        return $this->data;
    }

    /**
     * @return array
     *
     * Metodo para retornar os erros inerentes ao processo de validacao.
     */
    public function get_errors() {
        return $this->errors;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>login</i> do controller <i>Account</i>.
     */
    public function validate_login() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo USCMAIL (email)
        if (!isset($this->data['uscmail']) || empty(trim($this->data['uscmail']))) {
            $this->errors['uscmail'] = 'O E-MAIL é obrigatório!';
            $status = FALSE;
        } else if (!filter_var($this->data['uscmail'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['uscmail'] = 'Informe um E-MAIL válido!';
            $status = FALSE;
        } else if (strlen($this->data['uscmail']) > USCMAIL_MAX_LENGTH) {
            $this->errors['uscmail'] = 'O E-MAIL deve conter no máximo ' . USCMAIL_MAX_LENGTH . ' caracter(es)!';
            $status = FALSE;
        }

        // Validando o campo USCSENH (senha)
        if (!isset($this->data['uscsenh']) || empty(trim($this->data['uscsenh']))) {
            $this->errors['uscsenh'] = 'O SENHA é obrigatório!';
            $status = FALSE;
        } else if (strpos($this->data['uscsenh'], ' ') > 0) {
            $this->errors['uscsenh'] = 'O SENHA não pode conter espaço(s) em branco(s)!';
            $status = FALSE;
        } else if (strlen($this->data['uscsenh']) > USCSENH_MAX_LENGTH) {
            $this->errors['uscsenh'] = 'O SENHA deve conter no máximo ' . USCSENH_MAX_LENGTH . ' caracter(es)!';
            $status = FALSE;
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>login_facebook</i> do controller <i>Account</i>.
     */
    public function validate_login_facebook() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo USCFBID (ID do Facebook)*
        if (!isset($this->data['uscfbid']) || empty(trim($this->data['uscfbid']))) {
            $this->errors['uscfbid'] = 'O FACEBOOKID é obrigatório!';
            $status = FALSE;
        } else if (strpos($this->data['uscfbid'], ' ') > 0) {
            $this->errors['uscfbid'] = 'O FACEBOOKID não pode conter espaço(s) em branco(s)!';
            $status = FALSE;
        } else if (strlen($this->data['uscfbid']) > USCFBID_MAX_LENGTH) {
            $this->errors['uscfbid'] = 'O FACEBOOKID deve conter no máximo ' . USCFBID_MAX_LENGTH . ' caracter(es)!';
            $status = FALSE;
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>register</i> do controller <i>Account</i>.
     */
    public function validate_register() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo USCMAIL (email)
        if (!isset($this->data['uscmail']) || empty(trim($this->data['uscmail']))) {
            $this->errors['uscmail'] = 'O E-MAILl é obrigatório!';
            $status = FALSE;
        } else if (!filter_var($this->data['uscmail'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['uscmail'] = 'Informe um E-MAIL válido!';
            $status = FALSE;
        } else if (strlen($this->data['uscmail']) > USCMAIL_MAX_LENGTH) {
            $this->errors['uscmail'] = 'O E-MAIL deve conter no máximo ' . USCMAIL_MAX_LENGTH . ' caracter(es)!';
            $status = FALSE;
        } else if (!is_null($this->CI->usuario_model->find_by_uscmail($this->data['uscmail']))) {
            $this->errors['uscmail'] = 'Já existe um usuário cadastrado com esse endereço de e-email!';
            $status = FALSE;
        }

        // Validando o campo USCLOGN (login)
        if (!isset($this->data['usclogn']) || empty(trim($this->data['usclogn']))) {
            $this->errors['usclogn'] = 'O LOGIN é obrigatório!';
            $status = FALSE;
        } else if (strpos($this->data['usclogn'], ' ') > 0) {
            $this->errors['usclogn'] = 'O LOGIN não pode conter espaço(s) em branco(s)!';
            $status = FALSE;
        } else if (strlen($this->data['usclogn']) > USCLOGN_MAX_LENGTH) {
            $this->errors['usclogn'] = 'O LOGIN deve conter no máximo ' . USCLOGN_MAX_LENGTH . ' caracter(es)!';
            $status = FALSE;
        } else if (!is_null($this->CI->usuario_model->find_by_usclogn($this->data['usclogn']))) {
            $this->errors['usclogn'] = 'Já existe um usuário cadastrado com esse login!';
            $status = FALSE;
        }

        // Validando o campo USCSENH (senha)
        if (!isset($this->data['uscsenh']) || empty(trim($this->data['uscsenh']))) {
            $this->errors['uscsenh'] = 'A SENHA é obrigatório!';
            $status = FALSE;
        } else if (strpos($this->data['uscsenh'], ' ') > 0) {
            $this->errors['uscsenh'] = 'A SENHA não pode conter espaço(s) em branco(s)!';
            $status = FALSE;
        } else if (strlen($this->data['uscsenh']) > USCSENH_MAX_LENGTH) {
            $this->errors['uscsenh'] = 'A SENHA deve conter no máximo ' . USCSENH_MAX_LENGTH . ' caracter(es)!';
            $status = FALSE;
        }

        // Validando o campo USCSENH2 (Confirmacao da Senha)
        if (!isset($this->data['uscsenh2']) || empty(trim($this->data['uscsenh2']))) {
            $this->errors['uscsenh2'] = 'O campo CONFIRMAR SENHA é obrigatório!';
            $status = FALSE;
        } else if (strcmp($this->data['uscsenh'], $this->data['uscsenh2']) != 0) {
            $this->errors['uscsenh2'] = 'As senhas não conferem!';
            $status = FALSE;
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>recovery</i> do controller <i>Account</i>.
     */
    public function validate_recovery() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo USCMAIL (email)
        if (!isset($this->data['uscmail']) || empty(trim($this->data['uscmail']))) {
            $this->errors['uscmail'] = 'O E-MAIL é obrigatório!';
            $status = FALSE;
        } else if (!filter_var($this->data['uscmail'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['uscmail'] = 'Informe um E-MAIL válido!';
            $status = FALSE;
        } else if (strlen($this->data['uscmail']) > USCMAIL_MAX_LENGTH) {
            $this->errors['uscmail'] = 'O E-MAIL deve conter no máximo ' . USCMAIL_MAX_LENGTH . ' caracter(es)!';
            $status = FALSE;
        }
        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>password_reset</i> do controller <i>Account</i>.
     */
    public function validate_password_reset() {
        $status = TRUE;

        // Validando o campo RSCTOKN (token)
        if (!isset($this->data['rsctokn']) || empty(trim($this->data['rsctokn']))) {
            $this->errors['rsctokn'] = 'O TOKEN DE REDEFINIÇÃO é obrigatório!';
            $status = FALSE;
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>email_confirmation</i> do controller <i>Account</i>.
     */
    public function validate_email_confirmation() {
        $status = TRUE;

        // Validando o campo CACTOKN (token)
        if (!isset($this->data['cactokn']) || empty(trim($this->data['cactokn']))) {
            $this->errors['cactokn'] = 'O TOKEN DE CONFIRMAÇÃO é obrigatório!';
            $status = FALSE;
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>password_edit</i> do controller <i>Account</i>.
     */
    public function validate_password_edit() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo USNID (ID do uisuario)*
        if (!isset($this->data['usnid']) || empty(trim($this->data['usnid']))) {
            $this->errors['usnid'] = 'O ID DO USUÁRIO é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['usnid'])) {
            $this->errors['usnid'] = 'O ID DO USUÁRIO deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->usuario_model->find_by_usnid($this->data['usnid']))) {
            $this->errors['usnid'] = 'ID DO USUÁRIO inválido!';
            $status = FALSE;
        }

        // Validando o campo USCSENH (senha)
        if (!isset($this->data['uscsenh']) || empty(trim($this->data['uscsenh']))) {
            $this->errors['uscsenh'] = 'A SENHA é obrigatório!';
            $status = FALSE;
        } else if (strpos($this->data['uscsenh'], ' ') > 0) {
            $this->errors['uscsenh'] = 'A SENHA não pode conter espaço(s) em branco(s)!';
            $status = FALSE;
        } else if (strlen($this->data['uscsenh']) > USCSENH_MAX_LENGTH) {
            $this->errors['uscsenh'] = 'A SENHA deve conter no máximo ' . USCSENH_MAX_LENGTH . ' caracter(es)!';
            $status = FALSE;
        }

        // Validando o campo USCSENH2 (Confirmacao da Senha)
        if (!isset($this->data['uscsenh2']) || empty(trim($this->data['uscsenh2']))) {
            $this->errors['uscsenh2'] = 'O campo CONFIRMAR SENHA é obrigatório!';
            $status = FALSE;
        } else if (strcmp($this->data['uscsenh'], $this->data['uscsenh2']) != 0) {
            $this->errors['uscsenh2'] = 'As senhas não conferem!';
            $status = FALSE;
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>profile</i> do controller <i>Account</i>.
     */
    public function validate_profile() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo USNID (ID do uisuario)*
        if (!isset($this->data['usnid']) || empty(trim($this->data['usnid']))) {
            $this->errors['usnid'] = 'O ID DO USUÁRIO é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['usnid'])) {
            $this->errors['usnid'] = 'O ID DO USUÁRIO deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->usuario_model->find_by_usnid($this->data['usnid']))) {
            $this->errors['usnid'] = 'ID DO USUÁRIO inválido!';
            $status = FALSE;
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>search</i> do controller <i>Account</i>.
     */
    public function validate_search() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo REFERENCE (Referenca para pesquisa)*
        if (!isset($this->data['reference']) || empty(trim($this->data['reference']))) {
            $this->errors['reference'] = 'Informe uma referência para a pesquisa!';
            $status = FALSE;
        } else if (!is_string($this->data['reference'])) {
            $this->errors['reference'] = 'A REFERÊNCIA deve ser um valor alfanumérico!';
            $status = FALSE;
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>edit_profile</i> do controller <i>Account</i>.
     */
    public function validate_profile_edit() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo USNID (ID do uisuario)*
        if (!isset($this->data['usnid']) || empty(trim($this->data['usnid']))) {
            $this->errors['usnid'] = 'O ID DO USUÁRIO é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['usnid'])) {
            $this->errors['usnid'] = 'O ID DO USUÁRIO deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->usuario_model->find_by_usnid($this->data['usnid']))) {
            $this->errors['usnid'] = 'ID DO USUÁRIO inválido!';
            $status = FALSE;
        }

        // Validando o campo USCNOME (nome)
        if (isset($this->data['uscnome'])) {
            if (empty(trim($this->data['uscnome']))) {
                $this->errors['uscnome'] = 'O NOME não pode ser vazio!';
                $status = FALSE;
            } else if (!is_string($this->data['uscnome'])) {
                $this->errors['uscnome'] = 'O NOME deve ser um valor alfanuérico!';
                $status = FALSE;
            } else if (strlen($this->data['uscnome']) > USCNOME_MAX_LENGTH) {
                $this->errors['uscnome'] = 'O NOME deve conter no máximo ' . USCNOME_MAX_LENGTH . ' caracter(es)!';
                $status = FALSE;
            }
        }

        // Validando o campo USCMAIL (email)
        if (!isset($this->data['uscmail']) || empty(trim($this->data['uscmail']))) {
            $this->errors['uscmail'] = 'O E-MAILl é obrigatório!';
            $status = FALSE;
        } else if (!filter_var($this->data['uscmail'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['uscmail'] = 'Informe um E-MAIL válido!';
            $status = FALSE;
        } else if (strlen($this->data['uscmail']) > USCMAIL_MAX_LENGTH) {
            $this->errors['uscmail'] = 'O E-MAIL deve conter no máximo ' . USCMAIL_MAX_LENGTH . ' caracter(es)!';
            $status = FALSE;
        }

        // Validando o campo USCLOGN (login)
        if (!isset($this->data['usclogn']) || empty(trim($this->data['usclogn']))) {
            $this->errors['usclogn'] = 'O LOGIN é obrigatório!';
            $status = FALSE;
        } else if (strpos($this->data['usclogn'], ' ') > 0) {
            $this->errors['usclogn'] = 'O LOGIN não pode conter espaço(s) em branco(s)!';
            $status = FALSE;
        } else if (strlen($this->data['usclogn']) > USCLOGN_MAX_LENGTH) {
            $this->errors['usclogn'] = 'O LOGIN deve conter no máximo ' . USCLOGN_MAX_LENGTH . ' caracter(es)!';
            $status = FALSE;
        }

        // Validando o campo USCSENH (senha)
        if (!isset($this->data['uscsenh']) || empty(trim($this->data['uscsenh']))) {
            $this->errors['uscsenh'] = 'A SENHA é obrigatório!';
            $status = FALSE;
        } else if (strpos($this->data['uscsenh'], ' ') > 0) {
            $this->errors['uscsenh'] = 'A SENHA não pode conter espaço(s) em branco(s)!';
            $status = FALSE;
        } else if (strlen($this->data['uscsenh']) > USCSENH_MAX_LENGTH) {
            $this->errors['uscsenh'] = 'A SENHA deve conter no máximo ' . USCSENH_MAX_LENGTH . ' caracter(es)!';
            $status = FALSE;
        }

        // Validando o campo USCSENH2 (Confirmacao da Senha)
        if (!isset($this->data['uscsenh2']) || empty(trim($this->data['uscsenh2']))) {
            $this->errors['uscsenh2'] = 'O campo CONFIRMAR SENHA é obrigatório!';
            $status = FALSE;
        } else if (strcmp($this->data['uscsenh'], $this->data['uscsenh2']) != 0) {
            $this->errors['uscsenh2'] = 'As senhas não conferem!';
            $status = FALSE;
        }

        return $status;
    }

}