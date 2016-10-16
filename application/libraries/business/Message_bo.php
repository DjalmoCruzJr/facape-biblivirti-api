<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 16/10/2016
 *
 * Classe para aplicar as regras de negocio inerentes as operacoes do controller <i>Message</i>.
 */
class Message_bo {

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
        $this->CI = &get_instance();

        // Loading model
        $this->CI->load->model('usuario_model');
        $this->CI->load->model('grupo_model');
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
     * Metodo para validar os dados inentes ao processo de <i>list_all</i> do controller <i>Message</i>.
     */
    public function validate_list_all() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo <i>usnid</i> (ID do usuario)
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

        // Validando o campo <i>grnid</i> (ID do grupo)
        if (!isset($this->data['grnid']) || empty(trim($this->data['grnid']))) {
            $this->errors['grnid'] = 'O ID DO GRUPO é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['grnid'])) {
            $this->errors['grnid'] = 'O ID DO GRUPO deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->grupo_model->find_by_grnid($this->data['grnid']))) {
            $this->errors['grnid'] = 'ID DO GRUPO inválido!';
            $status = FALSE;
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>add</i> do controller <i>Message</i>.
     */
    public function validate_add() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo <i>msnidus</i> (ID do usuario da mensagem)
        if (!isset($this->data['msnidus']) || empty(trim($this->data['msnidus']))) {
            $this->errors['msnidus'] = 'O ID DO USUÁRIO é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['msnidus'])) {
            $this->errors['msnidus'] = 'O ID DO USUÁRIO deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->usuario_model->find_by_usnid($this->data['msnidus']))) {
            $this->errors['msnidus'] = 'ID DO USUÁRIO inválido!';
            $status = FALSE;
        }

        // Validando o campo <i>msnidgr</i> (ID do grupo)
        if (!isset($this->data['msnidgr']) || empty(trim($this->data['msnidgr']))) {
            $this->errors['msnidgr'] = 'O ID DO GRUPO é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['msnidgr'])) {
            $this->errors['msnidgr'] = 'O ID DO GRUPO deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->grupo_model->find_by_grnid($this->data['msnidgr']))) {
            $this->errors['msnidgr'] = 'ID DO GRUPO inválido!';
            $status = FALSE;
        }

        // Validando o campo <i>msctext</i> (Texto da mensagem)
        if (!isset($this->data['msctext']) || empty(trim($this->data['msctext']))) {
            $this->errors['msctext'] = 'O TEXTO DA MENSAGEM é obrigatório!';
            $status = FALSE;
        } else if (!is_string($this->data['msctext'])) {
            $this->errors['msnidgr'] = 'O TEXTO DA MENSAGEM deve ser um valor alfanumérico!';
            $status = FALSE;
        }

        // Validando o campo <i>mscanex</i> (Anexo da mensagem)
        if (isset($this->data['mscanex'])) { //  Se este campo for informado, sera validado
            if (empty(trim($this->data['mscanex']))) {
                $this->errors['mscanex'] = 'O ANEXO DA MENSAGEM é obrigatório!';
                $status = FALSE;
            } else if (!is_string($this->data['mscanex'])) {
                $this->errors['mscanex'] = 'O ANEXO DA MENSAGEM deve ser um valor alfanumérico!';
                $status = FALSE;
            }
        }

        return $status;
    }

}