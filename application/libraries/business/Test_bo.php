<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 24/10/2016
 *
 * Classe para aplicar as regras de negocio inerentes as operacoes do controller <i>Test</i>.
 */
class Test_bo {

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
     * Test_bo constructor.
     */
    public function __construct() {
        // Loading variables
        $this->data = [];
        $this->errors = [];
        $this->CI = &get_instance();

        // Loading model
        $this->CI->load->model('avaliacao_model');
        $this->CI->load->model('usuario_model');
        $this->CI->load->model('material_model');
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
     * Metodo para validar os dados inentes ao processo de <i>start</i> do controller <i>Message</i>.
     */
    public function validate_start() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo <i>avnidus</i> (ID do usuario)
        if (!isset($this->data['avnidus']) || empty(trim($this->data['avnidus']))) {
            $this->errors['avnidus'] = 'O ID DO USUÁRIO é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['avnidus'])) {
            $this->errors['avnidus'] = 'O ID DO USUÁRIO deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->usuario_model->find_by_usnid($this->data['avnidus']))) {
            $this->errors['avnidus'] = 'ID DO USUÁRIO inválido!';
            $status = FALSE;
        }

        // Validando o campo <i>avnidma</i> (ID do material (simulado) )
        if (!isset($this->data['avnidma']) || empty(trim($this->data['avnidma']))) {
            $this->errors['avnidma'] = 'O ID DO MATERIAL é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['avnidma'])) {
            $this->errors['avnidma'] = 'O ID DO MATERIAL deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->material_model->find_by_manid($this->data['avnidma']))) {
            $this->errors['avnidma'] = 'ID DO MATERIAL inválido!';
            $status = FALSE;
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>finalize</i> do controller <i>Message</i>.
     */
    public function validate_finalize() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo <i>avnidus</i> (ID do usuario)
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

        // Validando o campo <i>avnid</i> (ID da avaliacao)
        if (!isset($this->data['avnid']) || empty(trim($this->data['avnid']))) {
            $this->errors['avnid'] = 'O ID DA AVALIAÇÃO é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['avnid'])) {
            $this->errors['avnid'] = 'O ID DA AVALIAÇÃO deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->avaliacao_model->find_by_avnid($this->data['avnid']))) {
            $this->errors['avnid'] = 'ID DA AVALIAÇÃO inválido!';
            $status = FALSE;
        }

        return $status;
    }

}