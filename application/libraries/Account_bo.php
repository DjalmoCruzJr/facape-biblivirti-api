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
        $this->CI->load->model('user_model');
        $this->CI->load->model('recover_model');
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

        // Validando o campo USCFBID (ID do Facebook)*
        if (!isset($this->data['uscfbid']) || empty(trim($this->data['uscfbid']))) {
            $this->errors['uscfbid'] = 'O FACEBOOKID é obrigatório!';
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
        } else if (!is_null($this->CI->user_model->find_by_uscmail($this->data['uscmail']))) {
            $this->errors['uscmail'] = 'Já existe um usuário cadastrado com esse endereço de e-mail!';
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
        } else if (!is_null($this->CI->user_model->find_by_usclogn($this->data['usclogn']))) {
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
     * Metodo para validar os dados inentes ao processo de <i>recover</i> do controller <i>Account</i>.
     */
    public function validate_recover() {
        $status = TRUE;

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


}