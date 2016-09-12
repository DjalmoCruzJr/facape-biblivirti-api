<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 11/09/2016
 *
 * Classe para aplicar as regras de negocio inerentes as operacoes do controller <i>Content</i>.
 */
class Content_bo {

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
        $this->CI->load->model('conteudo_model');
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
     * Metodo para validar os dados inentes ao processo de <i>list_all</i> do controller <i>Content</i>.
     */
    public function validate_list_all() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo GRNID (ID do grupo)
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
 * Metodo para validar os dados inentes ao processo de <i>add</i> do controller <i>Content</i>.
 */
    public function validate_add() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo GRNID (ID do grupo)
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

        // Validando o campo COCDESC (Descricao do conteudo)
        if (!isset($this->data['cocdesc']) || empty(trim($this->data['cocdesc']))) {
            $this->errors['cocdesc'] = 'A DESCRIÇÃO do CONTEÚDO é obrigatória!';
            $status = FALSE;
        } else if (strlen($this->data['cocdesc']) > COCDESC_MAX_LENGTH) {
            $this->errors['cocdesc'] = 'A DESCRIÇÃO DO CONTEÚDO deve conter no máximo ' . COCDESC_MAX_LENGTH . ' caracter(es)!';
            $status = FALSE;
        } else if (!is_null($this->CI->conteudo_model->find_by_cocdesc($this->data['cocdesc'], true))) {
            $this->errors['cocdesc'] = 'Já existe um conteúdo cadastrado com essa descrição!';
            $status = FALSE;
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>edit</i> do controller <i>Content</i>.
     */
    public function validate_edit() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo GRNID (ID do grupo)
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

        // Validando o campo CONID (ID do conteudo)
        if (!isset($this->data['conid']) || empty(trim($this->data['conid']))) {
            $this->errors['conid'] = 'O ID DO CONTEÚDO é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['conid'])) {
            $this->errors['conid'] = 'O ID DO CONTEÚDO deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->conteudo_model->find_by_conid($this->data['conid']))) {
            $this->errors['conid'] = 'ID DO CONTEÚDO inválido!';
            $status = FALSE;
        }

        // Validando o campo COCDESC (Descricao do conteudo)
        if (!isset($this->data['cocdesc']) || empty(trim($this->data['cocdesc']))) {
            $this->errors['cocdesc'] = 'A DESCRIÇÃO do CONTEÚDO é obrigatória!';
            $status = FALSE;
        } else if (strlen($this->data['cocdesc']) > COCDESC_MAX_LENGTH) {
            $this->errors['cocdesc'] = 'A DESCRIÇÃO DO CONTEÚDO deve conter no máximo ' . COCDESC_MAX_LENGTH . ' caracter(es)!';
            $status = FALSE;
        } else if (!is_null($this->CI->conteudo_model->find_by_cocdesc($this->data['cocdesc'], true))) {
            $this->errors['cocdesc'] = 'Já existe um conteúdo cadastrado com essa descrição!';
            $status = FALSE;
        }

        return $status;
    }

}