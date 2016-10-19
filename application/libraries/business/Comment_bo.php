<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 17/10/2016
 *
 * Classe para aplicar as regras de negocio inerentes as operacoes do controller <i>Comment</i>.
 */
class Comment_bo {

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
        $this->CI->load->model('comentario_model');
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
     * Metodo para validar os dados inentes ao processo de <i>add</i> do controller <i>Message</i>.
     */
    public function validate_add() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo <i>cenidus</i> (ID do usuario)
        if (!isset($this->data['cenidus']) || empty(trim($this->data['cenidus']))) {
            $this->errors['cenidus'] = 'O ID DO USUÁRIO é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['cenidus'])) {
            $this->errors['cenidus'] = 'O ID DO USUÁRIO deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->usuario_model->find_by_usnid($this->data['cenidus']))) {
            $this->errors['cenidus'] = 'ID DO USUÁRIO inválido!';
            $status = FALSE;
        }

        // Validando o campo <i>cenidma</i> (ID do material)
        if (!isset($this->data['cenidma']) || empty(trim($this->data['cenidma']))) {
            $this->errors['cenidma'] = 'O ID DO MATERIAL é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['cenidma'])) {
            $this->errors['cenidma'] = 'O ID DO MATERIAL deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->grupo_model->find_by_grnid($this->data['cenidma']))) {
            $this->errors['cenidma'] = 'ID DO MATERIAL inválido!';
            $status = FALSE;
        }

        // Validando o campo <i>cectext</i> (Texto da comentario)
        if (!isset($this->data['cectext']) || empty(trim($this->data['cectext']))) {
            $type = (!isset($this->data['cenidce'])) ? 'DO COMENTÁRIO' : 'DA RESPOSTA';
            $this->errors['cectext'] = 'O TEXTO ' . $type . ' é obrigatório!';
            $status = FALSE;
        } else if (!is_string($this->data['cectext'])) {
            $type = (!isset($this->data['cenidce'])) ? 'DO COMENTÁRIO' : 'DA RESPOSTA';
            $this->errors['cectext'] = 'O TEXTO ' . $type . ' deve ser um valor alfanumérico!';
            $status = FALSE;
        }

        // Validando o campo <i>cecanex</i> (Anexo do comentario)
        if (isset($this->data['cecanex'])) { //  Se este campo for informado, sera validado
            if (empty(trim($this->data['cecanex']))) {
                $type = (!isset($this->data['cenidce'])) ? 'DO COMENTÁRIO' : 'DA RESPOSTA';
                $this->errors['cecanex'] = 'O ANEXO ' . $type . ' é obrigatório!';
                $status = FALSE;
            } else if (!is_string($this->data['cecanex'])) {
                $type = (!isset($this->data['cenidce'])) ? 'DO COMENTÁRIO' : 'DA RESPOSTA';
                $this->errors['cecanex'] = 'O NOME ANEXO ' . $type . ' deve ser um valor alfanumérico!';
                $status = FALSE;
            }
        }

        // Validando o campo <i>cenidce</i> (ID do comentario - neste caso trata-se de uma resposta)
        if (isset($this->data['cenidce'])) { //  Se este campo for informado, sera validado
            if (empty(trim($this->data['cenidce']))) {
                $this->errors['cenidce'] = 'O ID DO COMENTÁRIO é obrigatório!';
                $status = FALSE;
            } else if (!is_numeric($this->data['cenidce'])) {
                $this->errors['cenidce'] = 'O ID DO COMENTÁRIO deve ser um valor inteiro!';
                $status = FALSE;
            } else if (is_null($this->CI->comentario_model->find_by_cenid($this->data['cenidce']))) {
                $this->errors['cenidce'] = 'ID DO COMENTÁRIO inválido!';
                $status = FALSE;
            }
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>edit</i> do controller <i>Message</i>.
     */
    public function validate_edit() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo <i>cenid</i> (ID do comentario/resposta)
        if (!isset($this->data['cenid']) || empty(trim($this->data['cenid']))) {
            $type = (!isset($this->data['cenidce'])) ? 'DO COMENTÁRIO' : 'DA RESPOSTA';
            $this->errors['cenid'] = 'O ID ' . $type . ' é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['cenid'])) {
            $type = (!isset($this->data['cenidce'])) ? 'DO COMENTÁRIO' : 'DA RESPOSTA';
            $this->errors['cenid'] = 'O ID ' . $type . ' deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->comentario_model->find_by_cenid($this->data['cenid']))) {
            $type = (!isset($this->data['cenidce'])) ? 'DO COMENTÁRIO' : 'DA RESPOSTA';
            $this->errors['cenid'] = 'ID ' . $type . ' inválido!';
            $status = FALSE;
        }

        // Validando o campo <i>cenidus</i> (ID do usuario)
        if (!isset($this->data['cenidus']) || empty(trim($this->data['cenidus']))) {
            $this->errors['cenidus'] = 'O ID DO USUÁRIO é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['cenidus'])) {
            $this->errors['cenidus'] = 'O ID DO USUÁRIO deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->usuario_model->find_by_usnid($this->data['cenidus']))) {
            $this->errors['cenidus'] = 'ID DO USUÁRIO inválido!';
            $status = FALSE;
        }

        // Validando o campo <i>cenidma</i> (ID do material)
        if (!isset($this->data['cenidma']) || empty(trim($this->data['cenidma']))) {
            $this->errors['cenidma'] = 'O ID DO MATERIAL é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['cenidma'])) {
            $this->errors['cenidma'] = 'O ID DO MATERIAL deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->grupo_model->find_by_grnid($this->data['cenidma']))) {
            $this->errors['cenidma'] = 'ID DO MATERIAL inválido!';
            $status = FALSE;
        }

        // Validando o campo <i>cectext</i> (Texto da comentario)
        if (!isset($this->data['cectext']) || empty(trim($this->data['cectext']))) {
            $type = (!isset($this->data['cenidce'])) ? 'DO COMENTÁRIO' : 'DA RESPOSTA';
            $this->errors['cectext'] = 'O TEXTO ' . $type . ' é obrigatório!';
            $status = FALSE;
        } else if (!is_string($this->data['cectext'])) {
            $type = (!isset($this->data['cenidce'])) ? 'DO COMENTÁRIO' : 'DA RESPOSTA';
            $this->errors['cectext'] = 'O TEXTO ' . $type . ' deve ser um valor alfanumérico!';
            $status = FALSE;
        }

        // Validando o campo <i>cecanex</i> (Anexo do comentario)
        if (isset($this->data['cecanex'])) { //  Se este campo for informado, sera validado
            if (empty(trim($this->data['cecanex']))) {
                $type = (!isset($this->data['cenidce'])) ? 'DO COMENTÁRIO' : 'DA RESPOSTA';
                $this->errors['cecanex'] = 'O ANEXO ' . $type . ' é obrigatório!';
                $status = FALSE;
            } else if (!is_string($this->data['cecanex'])) {
                $type = (!isset($this->data['cenidce'])) ? 'DO COMENTÁRIO' : 'DA RESPOSTA';
                $this->errors['cecanex'] = 'O NOME ANEXO ' . $type . ' deve ser um valor alfanumérico!';
                $status = FALSE;
            }
        }

        // Validando o campo <i>cenidce</i> (ID do comentario - neste caso trata-se de uma resposta)
        if (isset($this->data['cenidce'])) { //  Se este campo for informado, sera validado
            if (empty(trim($this->data['cenidce']))) {
                $this->errors['cenidce'] = 'O ID DO COMENTÁRIO é obrigatório!';
                $status = FALSE;
            } else if (!is_numeric($this->data['cenidce'])) {
                $this->errors['cenidce'] = 'O ID DO COMENTÁRIO deve ser um valor inteiro!';
                $status = FALSE;
            } else if (is_null($this->CI->comentario_model->find_by_cenid($this->data['cenidce']))) {
                $this->errors['cenidce'] = 'ID DO COMENTÁRIO inválido!';
                $status = FALSE;
            }
        }

        return $status;
    }

}