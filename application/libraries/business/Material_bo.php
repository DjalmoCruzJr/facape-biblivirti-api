<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 01/09/2016
 *
 * Classe para aplicar as regras de negocio inerentes as operacoes do controller <i>Material</i>.
 */
class Material_bo {

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

        // Loading models
        $this->CI->load->model('material_model');
        $this->CI->load->model('grupo_model');
        $this->CI->load->model('questao_model');
        $this->CI->load->model('conteudo_model');
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
     * Metodo para validar os dados inentes ao processo de <i>list_all</i> do controller <i>Material</i>.
     */
    public function validate_list_all() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo GRNID (ID do Grupo)
        if (!isset($this->data['grnid']) || empty(trim($this->data['grnid']))) {
            $this->errors['grnid'] = 'O ID DO GRUPO é obrigatório!';
            $status = false;
        } else if (!is_numeric($this->data['grnid'])) {
            $this->errors['grnid'] = 'O ID DO GRUPO deve ser um valor inteiro!';
            $status = false;
        } else if (is_null($this->CI->grupo_model->find_by_grnid($this->data['grnid']))) {
            $this->errors['grnid'] = 'ID DO GRUPO inválido!';
            $status = false;
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>add</i> do controller <i>Material</i>.
     */
    public function validate_add() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo GRNID (ID do Grupo)
        if (!isset($this->data['grnid']) || empty(trim($this->data['grnid']))) {
            $this->errors['grnid'] = 'O ID DO GRUPO é obrigatório!';
            $status = false;
        } else if (!is_numeric($this->data['grnid'])) {
            $this->errors['grnid'] = 'O ID DO GRUPO deve ser um valor inteiro!';
            $status = false;
        } else if (is_null($this->CI->grupo_model->find_by_grnid($this->data['grnid']))) {
            $this->errors['grnid'] = 'ID DO GRUPO inválido!';
            $status = false;
        } else {
            $this->data['manidgr'] = $this->data['grnid'];
            unset($this->data['grnid']);
        }

        // Validando o campo MACDESC (Descricao do material)
        if (!isset($this->data['macdesc']) || empty(trim($this->data['macdesc']))) {
            $this->errors['macdesc'] = 'A DESCRIÇÃO DO MATERIAL é obrigatória!';
            $status = false;
        } else if (strlen($this->data['macdesc']) > MACDESC_MAX_LENGTH) {
            $this->errors['macdesc'] = 'A DESCRIÇÃO DO MATERIAL deve conter no máximo ' . MACDESC_MAX_LENGTH . ' caracter(es)!';
            $status = false;
        }

        // Validando o campo contents (Conteudos relacionados com o material)
        if (!isset($this->data['contents']) || !is_array($this->data['contents']) || empty($this->data['contents'])) {
            $this->errors['contents'] = 'O material deve conter pelo menos 1 conteudo relacionado!';
            $status = false;
        } else {
            // Validando os conteudos relacionados do material
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

        // Validando o campo MACTIPO (Tipo de material)
        if (!isset($this->data['mactipo']) || empty(trim($this->data['mactipo']))) {
            $this->errors['mactipo'] = 'O TIPO DE MATERIAL é obrigatório!';
            $status = false;
        } else {
            // Verifica se o tipo de material foi preenchido com valores validos
            if (!is_string($this->data['mactipo']) || strlen($this->data['mactipo']) !== 1 ||
                !(strcmp($this->data['mactipo'], MACTIPO_APRESENTACAO) === 0
                    | strcmp($this->data['mactipo'], MACTIPO_EXERCICIO) === 0
                    | strcmp($this->data['mactipo'], MACTIPO_FORMULA) === 0
                    | strcmp($this->data['mactipo'], MACTIPO_JOGO) === 0
                    | strcmp($this->data['mactipo'], MACTIPO_LIVRO) === 0
                    | strcmp($this->data['mactipo'], MACTIPO_SIMULADO) === 0
                    | strcmp($this->data['mactipo'], MACTIPO_VIDEO) === 0)
            ) {
                $tipos = MACTIPO_APRESENTACAO . ',' . MACTIPO_EXERCICIO . ',' . MACTIPO_FORMULA . ',' . MACTIPO_JOGO . ',' . MACTIPO_LIVRO . ',' . MACTIPO_SIMULADO . ',' . MACTIPO_VIDEO;
                $this->errors['grctipo'] = 'O TIPO DE GRUPO deve ser um valor do tipo String (' . $tipos . ')!';
                $status = false;
            } else {
                // Verifica se o material eh um SIMULADO
                if ($this->data['mactipo'] === MACTIPO_SIMULADO) {
                    // Validando o campo MACNIVL (Nivel do Material - SIMULADO)
                    if (!isset($this->data['macnivl']) || empty($this->data['macnivl'])) {
                        $this->errors['macnivl'] = 'O NÍVEL DO MATERIAL é obrigatório!';
                        $status = false;
                    } else if (!is_string($this->data['macnivl']) || strlen($this->data['macnivl']) !== 1 ||
                        !(strcmp($this->data['macnivl'], MACNIVL_BASICO) === 0
                            | strcmp($this->data['macnivl'], MACNIVL_INTERMEDIARIO) === 0
                            | strcmp($this->data['macnivl'], MACNIVL_AVANCADO) === 0
                            | strcmp($this->data['macnivl'], MACNIVL_PROFISSIONAL) === 0
                        )
                    ) {
                        $tipos = MACNIVL_BASICO . ',' . MACNIVL_INTERMEDIARIO . ',' . MACNIVL_AVANCADO . ',' . MACNIVL_PROFISSIONAL;
                        $this->errors['macnivl'] = 'O NÍVEL DO MATERIAL deve ser um valor do tipo String (' . $tipos . ')!';
                        $status = false;
                    }

                    // Validando o campo questions (Questoes do Simulado)
                    if (!isset($this->data['questions']) || !is_array($this->data['questions']) || empty($this->data['questions'])) {
                        $this->errors['questions'] = 'Um simulado deve conter pelo menos 1 questão!';
                        $status = false;
                    } else {
                        // Validando as questoes do simulado
                        $i = 0;
                        foreach ($this->data['questions'] as $question) {
                            $i++;
                            if (!isset($question['qenid']) || empty($question['qenid'])) {
                                $this->errors['questions']['qenid' . $i] = 'O ID DA QUESTÃO é obrigatório!';
                                $status = false;
                            } else if (!is_numeric($question['qenid'])) {
                                $this->errors['questions']['qenid' . $i] = 'O ID DA QUESTÃO deve ser o um valor inteiro!';
                                $status = false;
                            } else if (is_null($this->CI->questao_model->find_by_qenid($question['qenid']))) {
                                $this->errors['questions']['qenid' . $i] = 'O ID DA QUESTÃO inválido!';
                                $status = false;
                            }

                        }
                    }

                    unset($this->data['macurl']); // Remove o campo MACURL ja que se trata de um SIMULADO
                } else { // O Material NAO EH um simulado
                    // Validando o campo MACURL (URL do material)
                    if (!isset($this->data['macurl']) || empty($this->data['macurl'])) {
                        $this->errors['macurl'] = 'A URL DO MATERIAL é obrigatória!';
                        $status = false;
                    } else {
                        //Verifica o tipo do material eh VIDEO ou JOGO
                        if ($this->data['mactipo'] === MACTIPO_VIDEO || $this->data['mactipo'] === MACTIPO_JOGO) {
                            // Validando o campo MACURL (URL do material tem que ser um LINK valido)
                            if (!filter_var($this->data['macurl'], FILTER_VALIDATE_URL)) {
                                $this->errors['macurl'] = 'Informe uma URL DO MATERIAL válida!';
                                $status = false;
                            }
                        }
                    }

                    unset($this->data['macnivl']); // Remove o campo MACNIVL ja que NAO se trata de um SIMULADO
                }
            }
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>edit</i> do controller <i>Material</i>.
     */
    public function validate_edit() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo USNID (ID do usuario)
        if (!isset($this->data['usnid']) || empty(trim($this->data['usnid']))) {
            $this->errors['usnid'] = 'O ID DO USUÁRIO é obrigatório!';
            $status = false;
        } else if (!is_numeric($this->data['usnid'])) {
            $this->errors['usnid'] = 'O ID DO USUÁRIO deve ser um valor inteiro!';
            $status = false;
        } else if (is_null($this->CI->usuario_model->find_by_usnid($this->data['usnid']))) {
            $this->errors['usnid'] = 'ID DO USUÁRIO inválido!';
            $status = false;
        }

        // Validando o campo MANID (ID do Material)
        if (!isset($this->data['manid']) || empty(trim($this->data['manid']))) {
            $this->errors['manid'] = 'O ID DO MATERIAL é obrigatório!';
            $status = false;
        } else if (!is_numeric($this->data['manid'])) {
            $this->errors['manid'] = 'O ID DO MATERIAL deve ser um valor inteiro!';
            $status = false;
        }

        // Validando o campo MACDESC (Descricao do material)
        if (!isset($this->data['macdesc']) || empty(trim($this->data['macdesc']))) {
            $this->errors['macdesc'] = 'A DESCRIÇÃO DO MATERIAL é obrigatória!';
            $status = false;
        } else if (strlen($this->data['macdesc']) > MACDESC_MAX_LENGTH) {
            $this->errors['macdesc'] = 'A DESCRIÇÃO DO MATERIAL deve conter no máximo ' . MACDESC_MAX_LENGTH . ' caracter(es)!';
            $status = false;
        }

        // Validando o campo contents (Conteudos relacionados com o material)
        if (!isset($this->data['contents']) || !is_array($this->data['contents']) || empty($this->data['contents'])) {
            $this->errors['contents'] = 'O material deve conter pelo menos 1 conteudo relacionado!';
            $status = false;
        } else {
            // Validando os conteudos relacionados do material
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

        // Validando o campo MACTIPO (Tipo de material)
        if (!isset($this->data['mactipo']) || empty(trim($this->data['mactipo']))) {
            $this->errors['mactipo'] = 'O TIPO DE MATERIAL é obrigatório!';
            $status = false;
        } else {
            // Verifica se o tipo de material foi preenchido com valores validos
            if (!is_string($this->data['mactipo']) || strlen($this->data['mactipo']) !== 1 ||
                !(strcmp($this->data['mactipo'], MACTIPO_APRESENTACAO) === 0
                    | strcmp($this->data['mactipo'], MACTIPO_EXERCICIO) === 0
                    | strcmp($this->data['mactipo'], MACTIPO_FORMULA) === 0
                    | strcmp($this->data['mactipo'], MACTIPO_JOGO) === 0
                    | strcmp($this->data['mactipo'], MACTIPO_LIVRO) === 0
                    | strcmp($this->data['mactipo'], MACTIPO_SIMULADO) === 0
                    | strcmp($this->data['mactipo'], MACTIPO_VIDEO) === 0)
            ) {
                $tipos = MACTIPO_APRESENTACAO . ',' . MACTIPO_EXERCICIO . ',' . MACTIPO_FORMULA . ',' . MACTIPO_JOGO . ',' . MACTIPO_LIVRO . ',' . MACTIPO_SIMULADO . ',' . MACTIPO_VIDEO;
                $this->errors['grctipo'] = 'O TIPO DE GRUPO deve ser um valor do tipo String (' . $tipos . ')!';
                $status = false;
            } else {
                // Verifica se o material eh um SIMULADO
                if ($this->data['mactipo'] === MACTIPO_SIMULADO) {
                    // Validando o campo MACNIVL (Nivel do Material - SIMULADO)
                    if (!isset($this->data['macnivl']) || empty($this->data['macnivl'])) {
                        $this->errors['macnivl'] = 'O NÍVEL DO MATERIAL é obrigatório!';
                        $status = false;
                    } else if (!is_string($this->data['macnivl']) || strlen($this->data['macnivl']) !== 1 ||
                        !(strcmp($this->data['macnivl'], MACNIVL_BASICO) === 0
                            | strcmp($this->data['macnivl'], MACNIVL_INTERMEDIARIO) === 0
                            | strcmp($this->data['macnivl'], MACNIVL_AVANCADO) === 0
                            | strcmp($this->data['macnivl'], MACNIVL_PROFISSIONAL) === 0
                        )
                    ) {
                        $tipos = MACNIVL_BASICO . ',' . MACNIVL_INTERMEDIARIO . ',' . MACNIVL_AVANCADO . ',' . MACNIVL_PROFISSIONAL;
                        $this->errors['macnivl'] = 'O NÍVEL DO MATERIAL deve ser um valor do tipo String (' . $tipos . ')!';
                        $status = false;
                    }

                    // Validando o campo questions (Questoes do Simulado)
                    if (!isset($this->data['questions']) || !is_array($this->data['questions']) || empty($this->data['questions'])) {
                        $this->errors['questions'] = 'Um simulado deve conter pelo menos 1 questão!';
                        $status = false;
                    } else {
                        // Validando as questoes do simulado
                        $i = 0;
                        foreach ($this->data['questions'] as $question) {
                            $i++;
                            if (!isset($question['qenid']) || empty($question['qenid'])) {
                                $this->errors['questions']['qenid' . $i] = 'O ID DA QUESTÃO é obrigatório!';
                                $status = false;
                            } else if (!is_numeric($question['qenid'])) {
                                $this->errors['questions']['qenid' . $i] = 'O ID DA QUESTÃO deve ser o um valor inteiro!';
                                $status = false;
                            } else if (is_null($this->CI->questao_model->find_by_qenid($question['qenid']))) {
                                $this->errors['questions']['qenid' . $i] = 'O ID DA QUESTÃO inválido!';
                                $status = false;
                            }

                        }
                    }

                    unset($this->data['macurl']); // Remove o campo MACURL ja que se trata de um SIMULADO
                } else { // O Material NAO EH um simulado
                    // Validando o campo MACURL (URL do material)
                    if (!isset($this->data['macurl']) || empty($this->data['macurl'])) {
                        $this->errors['macurl'] = 'A URL DO MATERIAL é obrigatória!';
                        $status = false;
                    } else {
                        //Verifica o tipo do material eh VIDEO ou JOGO
                        if ($this->data['mactipo'] === MACTIPO_VIDEO || $this->data['mactipo'] === MACTIPO_JOGO) {
                            // Validando o campo MACURL (URL do material tem que ser um LINK valido)
                            if (!filter_var($this->data['macurl'], FILTER_VALIDATE_URL)) {
                                $this->errors['macurl'] = 'Informe uma URL DO MATERIAL válida!';
                                $status = false;
                            }
                        }
                    }

                    unset($this->data['macnivl']); // Remove o campo MACNIVL ja que NAO se trata de um SIMULADO
                }
            }
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>delete</i> do controller <i>Material</i>.
     */
    public function validate_delete() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo <i>usnid</i> (ID do Usuario)
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

}