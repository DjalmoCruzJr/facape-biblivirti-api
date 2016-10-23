<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 20/10/2016
 *
 * Classe para aplicar as regras de negocio inerentes as operacoes do controller <i>Question</i>.
 */
class Question_bo {

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
        $this->CI->load->model('questao_model');
        $this->CI->load->model('usuario_model');
        $this->CI->load->model('grupo_model');
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
     * Metodo para validar os dados inentes ao processo de <i>list_all</i> do controller <i>Question</i>.
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

        // Validando o campo <i>manid</i> (ID do material)
        if (!isset($this->data['manid']) || empty(trim($this->data['manid']))) {
            $this->errors['manid'] = 'O ID DO MATERIAL é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['manid'])) {
            $this->errors['manid'] = 'O ID DO MATERIAL deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->material_model->find_by_manid($this->data['manid']))) {
            $this->errors['manid'] = 'ID DO MATERIAL inválido!';
            $status = FALSE;
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>add</i> do controller <i>Question</i>.
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

        // Validando o campo <i>qecdesc</i> (Descricao da questao)
        if (!isset($this->data['qecdesc']) || empty(trim($this->data['qecdesc']))) {
            $this->errors['qecdesc'] = 'A DESCRICAO DA QUESTÃO é obrigatória!';
            $status = FALSE;
        } else if (!is_string($this->data['qecdesc'])) {
            $this->errors['qecdesc'] = 'A DESCRICAO DA QUESTÃO deve ser um valor alfanumérico!';
            $status = FALSE;
        } else if (strlen($this->data['qecdesc']) > QECDESC_MAX_LENGHT) {
            $this->errors['qecdesc'] = 'A DESCRICAO DA QUESTÃO deve conter no máximo ' . QECDESC_MAX_LENGHT . ' caractere(s)!';
            $status = FALSE;
        }

        // Validando o campo <i>qectext</i> (Texto da questao)
        if (!isset($this->data['qectext']) || empty(trim($this->data['qectext']))) {
            $this->errors['qectext'] = 'O TEXTO DA QUESTÃO é obrigatório!';
            $status = FALSE;
        } else if (!is_string($this->data['qectext'])) {
            $this->errors['qectext'] = 'O TEXTO DA QUESTÃO deve ser um valor alfanumérico!';
            $status = FALSE;
        }

        // Validando o campo <i>qecanex</i> (Anexo da questao)
        if (isset($this->data['qecanex'])) { // verifica se o campo foi informado
            if (!isset($this->data['qecdesc']) || empty(trim($this->data['qecanex']))) {
                $this->errors['qecanex'] = 'O ANEXO DA QUESTÃO é obrigatória!';
                $status = FALSE;
            } else if (!is_string($this->data['qecanex'])) {
                $this->errors['qecanex'] = 'O ANEXO DA QUESTÃO deve ser um valor alfanumérico!';
                $status = FALSE;
            } else if (strlen($this->data['qecanex']) > QECDESC_MAX_LENGHT) {
                $this->errors['qecanex'] = 'O ANEXO DA QUESTÃO deve conter no máximo ' . QECANEX_MAX_LENGHT . ' caractere(s)!';
                $status = FALSE;
            }
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>edit</i> do controller <i>Question</i>.
     */
    public function validate_edit() {
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

        // Validando o campo <i>qenid</i> (ID do questao)
        if (!isset($this->data['qenid']) || empty(trim($this->data['qenid']))) {
            $this->errors['qenid'] = 'O ID DA QUESTÃO é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['qenid'])) {
            $this->errors['qenid'] = 'O ID DA QUESTÃO deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->questao_model->find_by_qenid($this->data['qenid']))) {
            $this->errors['grnid'] = 'ID DA QUESTÃO inválido!';
            $status = FALSE;
        }

        // Validando o campo <i>qecdesc</i> (Descricao da questao)
        if (!isset($this->data['qecdesc']) || empty(trim($this->data['qecdesc']))) {
            $this->errors['qecdesc'] = 'A DESCRICAO DA QUESTÃO é obrigatória!';
            $status = FALSE;
        } else if (!is_string($this->data['qecdesc'])) {
            $this->errors['qecdesc'] = 'A DESCRICAO DA QUESTÃO deve ser um valor alfanumérico!';
            $status = FALSE;
        } else if (strlen($this->data['qecdesc']) > QECDESC_MAX_LENGHT) {
            $this->errors['qecdesc'] = 'A DESCRICAO DA QUESTÃO deve conter no máximo ' . QECDESC_MAX_LENGHT . ' caractere(s)!';
            $status = FALSE;
        }

        // Validando o campo <i>qectext</i> (Texto da questao)
        if (!isset($this->data['qectext']) || empty(trim($this->data['qectext']))) {
            $this->errors['qectext'] = 'O TEXTO DA QUESTÃO é obrigatório!';
            $status = FALSE;
        } else if (!is_string($this->data['qectext'])) {
            $this->errors['qectext'] = 'O TEXTO DA QUESTÃO deve ser um valor alfanumérico!';
            $status = FALSE;
        }

        // Validando o campo <i>qecanex</i> (Anexo da questao)
        if (isset($this->data['qecanex'])) { // verifica se o campo foi informado
            if (!isset($this->data['qecdesc']) || empty(trim($this->data['qecanex']))) {
                $this->errors['qecanex'] = 'O ANEXO DA QUESTÃO é obrigatória!';
                $status = FALSE;
            } else if (!is_string($this->data['qecanex'])) {
                $this->errors['qecanex'] = 'O ANEXO DA QUESTÃO deve ser um valor alfanumérico!';
                $status = FALSE;
            } else if (strlen($this->data['qecanex']) > QECDESC_MAX_LENGHT) {
                $this->errors['qecanex'] = 'O ANEXO DA QUESTÃO deve conter no máximo ' . QECANEX_MAX_LENGHT . ' caractere(s)!';
                $status = FALSE;
            }
        }

        return $status;
    }

}