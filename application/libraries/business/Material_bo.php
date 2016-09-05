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

        // Validando o campo GRNID (ID do Grupo)
        if (!isset($this->data['grnid']) || empty(trim($this->data['grnid']))) {
            $this->errors['grnid'] = 'O ID DO GRUPO é obrigatório!';
            $status = false;
        } else if (!is_numeric($this->data['grnid'])) {
            $this->errors['grnid'] = 'O ID DO GRUPO deve ser um valor inteiro!';
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
        }

        // Validando o campo MACDESC (Descricao do material)
        if (!isset($this->data['macdesc']) || empty(trim($this->data['macdesc']))) {
            $this->errors['macdesc'] = 'A DESCRIÇÃO DO MATERIAL é obrigatória!';
            $status = false;
        } else if (strlen($this->data['macdesc']) > MACDESC_MAX_LENGTH) {
            $this->errors['macdesc'] = 'A DESCRIÇÃO DO MATERIAL deve conter no máximo ' . MACDESC_MAX_LENGTH . ' caracter(es)!';
            $status = false;
        }

        // Validando o campo MACTIPO (Tipo de material)
        if (!isset($this->data['mactipo']) || empty(trim($this->data['mactipo']))) {
            $this->errors['mactipo'] = 'O TIPO DE MATERIAL é obrigatório!';
            $status = false;
        } else if (!is_string($this->data['mactipo']) || strlen($this->data['mactipo']) !== 1 ||
            !(strcmp($this->data['mactipo'], MACTIPO_APRESENTACAO) == 0 | strcmp($this->data['mactipo'], MACTIPO_EXERCICIO) == 0 |
                strcmp($this->data['mactipo'], MACTIPO_FORMULA) == 0 | strcmp($this->data['mactipo'], MACTIPO_JOGO) == 0 |
                strcmp($this->data['mactipo'], MACTIPO_LIVRO) == 0 | strcmp($this->data['mactipo'], MACTIPO_SIMULADO) == 0 |
                strcmp($this->data['mactipo'], MACTIPO_VIDEO) == 0)
        ) {
            $tipos = MACTIPO_APRESENTACAO . ',' . MACTIPO_EXERCICIO . ',' . MACTIPO_FORMULA . ',' . MACTIPO_JOGO . ',' . MACTIPO_LIVRO . ',' . MACTIPO_SIMULADO . ',' . MACTIPO_VIDEO;
            $this->errors['grctipo'] = 'O TIPO DE GRUPO deve ser um valor do tipo String (' . $tipos . ')!';
            $status = false;
        }

        // Validando o campo MALANEX (Se o material eh um anexo)
        if ($this->data['mactipo'] !== MACTIPO_SIMULADO) {
            if (!is_numeric($this->data['malanex'])) {
                $this->errors['malanex'] = 'Este campo deve conter um valor booleano (1 - TRUE, 0 - false)!';
                $status = false;
            }
            // Converte o valor string para booleano
            $this->data['malanex'] = $this->data['malanex'] === '1';
        } else {
            $this->data['malanex'] = false;
        }

        // Validando o campo MACURL (URL do material)
        if ($this->data['mactipo'] !== MACTIPO_SIMULADO) {
            if (!isset($this->data['macurl']) || empty(trim($this->data['macurl']))) {
                $this->errors['macurl'] = 'A URL DO MATERIAL é obrigatória!';
                $status = false;
            } else if ($this->data['malanex'] === false) { // verifica se o material não eh um anexo
                if (!filter_var($this->data['macurl'], FILTER_VALIDATE_URL)) {
                    $this->errors['macurl'] = 'Informe uma URL válida!';
                    $status = false;
                }
            } else if (strpos($this->data['macurl'], ' ') > 0) {
                $this->errors['macurl'] = 'A URL DO MATERIAL não pode conter espaço(s) em branco(s)!';
                $status = FALSE;
            } else if (strlen($this->data['macurl']) > MACDESC_MAX_LENGTH) {
                $this->errors['macurl'] = 'A URL DO MATERIAL deve conter no máximo ' . MACURL_MAX_LENGTH . ' caracter(es)!';
                $status = false;
            }
        } else {
            unset($this->data['macurl']);
        }

        // Validando o campo MACNIVL (Nivel de material)
        if ($this->data['mactipo'] === MACTIPO_SIMULADO) {
            if (!isset($this->data['macnivl']) || empty(trim($this->data['macnivl']))) {
                $this->errors['macnivl'] = 'O NÍVEL DE MATERIAL é obrigatório!';
                $status = false;
            } else if (!is_string($this->data['macnivl']) || strlen($this->data['macnivl']) !== 1 ||
                !(strcmp($this->data['macnivl'], MACNIVL_BASICO) == 0 | strcmp($this->data['macnivl'], MACNIVL_INTERMEDIARIO) == 0 |
                    strcmp($this->data['macnivl'], MACNIVL_AVANCADO) == 0 | strcmp($this->data['macnivl'], MACNIVL_PROFISSIONAL) == 0)
            ) {
                $tipos = MACNIVL_BASICO . ',' . MACNIVL_INTERMEDIARIO . ',' . MACNIVL_AVANCADO . ',' . MACNIVL_PROFISSIONAL;
                $this->errors['macnivl'] = 'O NÍVEL DE MATERIAL deve ser um valor do tipo String (' . $tipos . ')!';
                $status = false;
            }
        } else {
            unset($this->data['macnivl']);
        }

        // Validando o campo MACSTAT (Status de material)
        if (!isset($this->data['macstat']) || empty(trim($this->data['macstat']))) {
            $this->errors['macstat'] = 'O STATUS DE MATERIAL é obrigatório!';
            $status = false;
        } else if (!is_string($this->data['macstat']) || strlen($this->data['macstat']) !== 1 ||
            !(strcmp($this->data['macstat'], MACSTAT_ATIVO) == 0 | strcmp($this->data['macstat'], MACSTAT_INATIVO) == 0)
        ) {
            $tipos = MACSTAT_ATIVO . ',' . MACSTAT_INATIVO;
            $this->errors['macstat'] = 'O STATUS DE MATERIAL deve ser um valor do tipo String (' . $tipos . ')!';
            $status = false;
        }

        // Validando o campo contents (Conteudos relacioandos com o material)
        if ($this->data['mactipo'] !== MACTIPO_SIMULADO) {
            if (!isset($this->data['contents']) || empty($this->data['contents'])) {
                $this->errors['contents'] = 'O MATERIAL deve está relacionado com pelo menos 1 conteúdo!';
                $status = false;
            } else {
                foreach ($this->data['contents'] as $content) {
                    if (!isset($content['conid']) || empty(trim($content['conid']))) {
                        $this->errors['contents']['conid'] = 'O ID CONTEÚDO RELACIONADO é obrigatório!';
                        $status = false;
                    } else if (!is_numeric($content['conid'])) {
                        $this->errors['contents']['conid'] = 'O ID CONTEÚDO RELACIONADO deve ser um valor inteiro!';
                        $status = false;
                    }
                }
            }
        } else {
            unset($this->data['contents']);
        }

        return $status;
    }


}