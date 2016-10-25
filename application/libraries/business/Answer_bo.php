<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 25/10/2016
 *
 * Classe para aplicar as regras de negocio inerentes as operacoes do controller <i>Answer</i>.
 */
class Answer_bo {

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
     * Answer_bo constructor.
     */
    public function __construct() {
        // Loading variables
        $this->data = [];
        $this->errors = [];
        $this->CI = &get_instance();

        // Loading model
        $this->CI->load->model('avaliacao_model');
        $this->CI->load->model('alternativa_model');
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
     * Metodo para validar os dados inerentes ao processo de <i>list_all</i> do controller <i>Answer</i>.
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

    /**
     * @return bool
     *
     * Metodo para validar os dados inerentes ao processo de <i>add</i> do controller <i>Answer</i>.
     */
    public function validate_add() {
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

        // Validando o campo <i>renidav</i> (ID da avaliacao)
        if (!isset($this->data['renidav']) || empty(trim($this->data['renidav']))) {
            $this->errors['renidav'] = 'O ID DA AVALIAÇÃO é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['renidav'])) {
            $this->errors['renidav'] = 'O ID DA AVALIAÇÃO deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->avaliacao_model->find_by_avnid($this->data['renidav']))) {
            $this->errors['renidav'] = 'ID DA AVALIAÇÃO inválido!';
            $status = FALSE;
        }

        // Validando o campo <i>renidal</i> (ID da alternativa)
        if (!isset($this->data['renidal']) || empty(trim($this->data['renidal']))) {
            $this->errors['renidal'] = 'O ID DA ALTERNATIVA é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['renidal'])) {
            $this->errors['renidal'] = 'O ID DA ALTERNATIVA deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->alternativa_model->find_by_alnid($this->data['renidal']))) {
            $this->errors['renidal'] = 'ID DA ALTERNATIVA inválido!';
            $status = FALSE;
        }

        return $status;
    }

}