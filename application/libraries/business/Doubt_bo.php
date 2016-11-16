<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 09/11/2016
 *
 * Classe para aplicar as regras de negocio inerentes as operacoes do controller <i>Doubt</i>.
 */
class Doubt_bo {

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
     * Doubt_bo constructor.
     */
    public function __construct() {
        // Loading variables
        $this->data = [];
        $this->errors = [];
        $this->CI = &get_instance();

        // Loading model
        $this->CI->load->model('usuario_model');
        $this->CI->load->model('grupo_model');
        $this->CI->load->model('duvida_model');
        $this->CI->load->model('conteudo_model');
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
     * Metodo para validar os dados inentes ao processo de <i>list_all</i> do controller <i>Doubt</i>.
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
            $this->errors['usnid'] = 'O ID DO USUÁRIO  deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->usuario_model->find_by_usnid($this->data['usnid']))) {
            $this->errors['usnid'] = 'ID DO USUÁRIO inválido!';
            $status = FALSE;
        }

        // Validando o campo <i>dvnidgr</i> (ID do grupo)
        if (!isset($this->data['dvnidgr']) || empty(trim($this->data['dvnidgr']))) {
            $this->errors['dvnidgr'] = 'O ID DO GRUPO é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['dvnidgr'])) {
            $this->errors['dvnidgr'] = 'O ID DO GRUPO  deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->grupo_model->find_by_grnid($this->data['dvnidgr']))) {
            $this->errors['dvnidgr'] = 'ID DO GRUPO inválido!';
            $status = FALSE;
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>add</i> do controller <i>Doubt</i>.
     */
    public function validate_add() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo <i>dvnidgr</i> (ID do grupo da duvida)
        if (!isset($this->data['dvnidgr']) || empty(trim($this->data['dvnidgr']))) {
            $this->errors['dvnidgr'] = 'O ID DO GRUPO é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['dvnidgr'])) {
            $this->errors['dvnidgr'] = 'O ID DO GRUPO  deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->grupo_model->find_by_grnid($this->data['dvnidgr']))) {
            $this->errors['dvnidgr'] = 'ID DO GRUPO inválido!';
            $status = FALSE;
        }

        // Validando o campo <i>dvnidus</i> (ID do usuario da duvida)
        if (!isset($this->data['dvnidus']) || empty(trim($this->data['dvnidus']))) {
            $this->errors['dvnidus'] = 'O ID DO USUÁRIO DA DÚVIDA é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['dvnidus'])) {
            $this->errors['dvnidus'] = 'O ID DO USUÁRIO DA DÚVIDA deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->usuario_model->find_by_usnid($this->data['dvnidus']))) {
            $this->errors['dvnidus'] = 'ID DO USUÁRIO DA DÚVIDA inválido!';
            $status = FALSE;
        }

        // Validando o campo <i>dvctext</i> (Texto da duvida)
        if (!isset($this->data['dvctext']) || empty(trim($this->data['dvctext']))) {
            $this->errors['dvctext'] = 'O TEXTO DA DÚVIDA  é obrigatório!';
            $status = FALSE;
        } else if (!is_string($this->data['dvctext'])) {
            $this->errors['dvctext'] = 'O TEXTO DA DÚVIDA deve ser um valor alfanumérico!';
            $status = FALSE;
        }

        // Validando o campo <i>dvcanex</i> (Anexo da duvida)
        if (isset($this->data['dvcanex'])) {
            if (empty(trim($this->data['dvcanex']))) {
                $this->errors['dvcanex'] = 'O ANEXO DA DÚVIDA  é obrigatório!';
                $status = FALSE;
            }
        }

        // Validando o campo <i>dvlanon</i> (Define se a duvida eh anonima ou nao)
        if (isset($this->data['dvlanon'])) {
            if (empty(trim($this->data['dvlanon']))) {
                $this->errors['dvlanon'] = 'É obrigatório informar se a dúvida é anônima ou não!';
                $status = FALSE;
            } else if (!is_bool($this->data['dvlanon'])) {
                $this->errors['dvlanon'] = 'Informe um valor booleano (True ou False)!';
                $status = FALSE;
            }
        }

        // Validando o campo contents (Conteudos relacionados com a duvida)
        if (!isset($this->data['contents']) || !is_array($this->data['contents']) || empty($this->data['contents'])) {
            $this->errors['contents'] = 'A dúvida deve conter pelo menos 1 conteudo relacionado!';
            $status = false;
        } else {
            // Validando os conteudos relacionados da duvida
            $i = 0;
            foreach ($this->data['contents'] as $content) {
                $i++;
                if (!isset($content['conid']) || empty($content['conid'])) {
                    $this->errors['contents']['conid' . $i] = 'O ID DO CONTEÚDO é obrigatório!';
                    $status = false;
                } else if (!is_numeric($content['conid'])) {
                    $this->errors['contents']['conid' . $i] = 'O ID DO CONTEÚDO deve ser o um valor inteiro!';
                    $status = false;
                } else if (is_null($this->CI->conteudo_model->find_by_conid($content['conid']))) {
                    $this->errors['contents']['conid' . $i] = 'ID DO CONTEÚDO  inválido!';
                    $status = false;
                }
            }
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>edit</i> do controller <i>Doubt</i>.
     */
    public function validate_edit() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo <i>usnid</i> (ID da usuario)
        if (!isset($this->data['usnid']) || empty(trim($this->data['usnid']))) {
            $this->errors['usnid'] = 'O ID USUÁRIO é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['usnid'])) {
            $this->errors['usnid'] = 'O ID USUÁRIO deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->usuario_model->find_by_usnid($this->data['usnid']))) {
            $this->errors['usnid'] = 'ID USUÁRIO inválido!';
            $status = FALSE;
        }

        // Validando o campo <i>dvnid</i> (ID da duvida)
        if (!isset($this->data['dvnid']) || empty(trim($this->data['dvnid']))) {
            $this->errors['dvnid'] = 'O ID DA DÚVIDA é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['dvnid'])) {
            $this->errors['dvnid'] = 'O ID DA DÚVIDA deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->duvida_model->find_by_dvnid($this->data['dvnid']))) {
            $this->errors['dvnid'] = 'ID DA DÚVIDA inválido!';
            $status = FALSE;
        }

        // Validando o campo <i>dvctext</i> (Texto da duvida)
        if (!isset($this->data['dvctext']) || empty(trim($this->data['dvctext']))) {
            $this->errors['dvctext'] = 'O TEXTO DA DÚVIDA  é obrigatório!';
            $status = FALSE;
        } else if (!is_string($this->data['dvctext'])) {
            $this->errors['dvctext'] = 'O TEXTO DA DÚVIDA deve ser um valor alfanumérico!';
            $status = FALSE;
        }

        // Validando o campo <i>dvcanex</i> (Anexo da duvida)
        if (isset($this->data['dvcanex'])) {
            if (empty(trim($this->data['dvcanex']))) {
                $this->errors['dvcanex'] = 'O ANEXO DA DÚVIDA  é obrigatório!';
                $status = FALSE;
            }
        }

        // Validando o campo <i>dvlanon</i> (Define se a duvida eh anonima ou nao)
        if (isset($this->data['dvlanon'])) {
            if (empty(trim($this->data['dvlanon']))) {
                $this->errors['dvlanon'] = 'É obrigatório informar se a dúvida é anônima ou não!';
                $status = FALSE;
            } else if (!is_bool($this->data['dvlanon'])) {
                $this->errors['dvlanon'] = 'Informe um valor booleano (True ou False)!';
                $status = FALSE;
            }
        }

        // Validando o campo contents (Conteudos relacionados com a duvida)
        if (!isset($this->data['contents']) || !is_array($this->data['contents']) || empty($this->data['contents'])) {
            $this->errors['contents'] = 'A dúvida deve conter pelo menos 1 conteudo relacionado!';
            $status = false;
        } else {
            // Validando os conteudos relacionados da duvida
            $i = 0;
            foreach ($this->data['contents'] as $content) {
                $i++;
                if (!isset($content['conid']) || empty($content['conid'])) {
                    $this->errors['contents']['conid' . $i] = 'O ID DO CONTEÚDO é obrigatório!';
                    $status = false;
                } else if (!is_numeric($content['conid'])) {
                    $this->errors['contents']['conid' . $i] = 'O ID DO CONTEÚDO deve ser o um valor inteiro!';
                    $status = false;
                } else if (is_null($this->CI->conteudo_model->find_by_conid($content['conid']))) {
                    $this->errors['contents']['conid' . $i] = 'ID DO CONTEÚDO  inválido!';
                    $status = false;
                }
            }
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>delete</i> do controller <i>Doubt</i>.
     */
    public function validate_delete() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo <i>usnid</i> (ID da usuario)
        if (!isset($this->data['usnid']) || empty(trim($this->data['usnid']))) {
            $this->errors['usnid'] = 'O ID USUÁRIO é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['usnid'])) {
            $this->errors['usnid'] = 'O ID USUÁRIO deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->usuario_model->find_by_usnid($this->data['usnid']))) {
            $this->errors['usnid'] = 'ID USUÁRIO inválido!';
            $status = FALSE;
        }

        // Validando o campo <i>dvnid</i> (ID da duvida)
        if (!isset($this->data['dvnid']) || empty(trim($this->data['dvnid']))) {
            $this->errors['dvnid'] = 'O ID DA DÚVIDA é obrigatório!';
            $status = FALSE;
        } else if (!is_numeric($this->data['dvnid'])) {
            $this->errors['dvnid'] = 'O ID DA DÚVIDA deve ser um valor inteiro!';
            $status = FALSE;
        } else if (is_null($this->CI->duvida_model->find_by_dvnid($this->data['dvnid']))) {
            $this->errors['dvnid'] = 'ID DA DÚVIDA inválido!';
            $status = FALSE;
        }

        return $status;
    }

}