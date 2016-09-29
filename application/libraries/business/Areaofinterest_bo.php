<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 28/09/2016
 *
 * Classe para aplicar as regras de negocio inerentes as operacoes do controller <i>Areaofinterest</i>.
 */
class Areaofinterest_bo {

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
        $this->CI->load->model('areainteresse_model');
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
     * Metodo para validar os dados inentes ao processo de <i>list_all</i> do controller <i>Group</i>.
     */
    public function validate_list_all() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo <i>aicdesc</i> (Descricao do area de interesse)
        if (!isset($this->data['aicdesc']) || empty(trim($this->data['aicdesc']))) {
            $this->errors['aicdesc'] = 'A DESCRIÇÃO da área de interesse é obrigatório!';
            $status = FALSE;
        } else if (strlen($this->data['aicdesc']) > AICDESC_MAX_LENGHT) {
            $this->errors['aicdesc'] = 'A DESCRIÇÃO da área de interesse deve conter no máximo ' . AICDESC_MAX_LENGHT . ' caracter(es)!';
            $status = FALSE;
        }

        return $status;
    }

    /**
     * @return bool
     *
     * Metodo para validar os dados inentes ao processo de <i>add</i> do controller <i>AreaOfInterest</i>.
     */
    public function validate_add() {
        $status = TRUE;

        // Verifica se o decode do JSON foi feito corretamente
        if (is_null($this->data)) {
            $this->errors['json_decode'] = "Não foi possível realizar o decode dos dados. JSON inválido!";
            return false;
        }

        // Validando o campo AICDESC (Descricao)
        if (!isset($this->data['aicdesc']) || empty(trim($this->data['aicdesc']))) {
            $this->errors['aicdesc'] = 'A DESCRIÇÃO é obrigatório!';
            $status = FALSE;
        } else if (strlen($this->data['aicdesc']) > AICDESC_MAX_LENGHT) {
            $this->errors['aicdesc'] = 'A DESCRIÇÃO deve conter no máximo ' . AICDESC_MAX_LENGHT . ' caracter(es)!';
            $status = FALSE;
        } else if (!is_null($this->CI->areainteresse_model->find_by_aicdesc($this->data['aicdesc'], true))) {
            $this->errors['aicdesc'] = 'Já existe uma área de interesse cadastrada com essa descrição!';
            $status = FALSE;
        }

        return $status;
    }

}