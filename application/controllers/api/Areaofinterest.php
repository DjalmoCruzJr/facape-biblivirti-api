<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 22/09/2016
 *
 * Controller da API para gerenciar o acesso aos dados da <b>AREAINTERESSE</b>.
 */
class Areaofinterest extends CI_Controller {

    /**
     * @var array
     *
     * Armazena os dados de resposta das requisicoes.
     */
    private $response;

    /**
     * Account constructor.
     */
    public function __construct() {
        parent::__construct();

        // Initializing variables
        $this->response = [];

        // Loading models
        $this->load->model("areainteresse_model");

        // Loading libraries
        $this->load->library('business/areaofinterest_bo');
        $this->load->library('input/biblivirti_input');
    }

    /**
     * @url: API/areaofinterest/list
     * @param string JSON
     * @return JSON
     *
     * Metodo para listar as areas de interesse.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "aicesc" : "Descricao da area de interesse"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : [
     *          {
     *              "ainid" : "ID da area de interesse",
     *              "aicdesc" : "Descricao da area de interesse",
     *              "aidcadt" : "Data de cadastro",
     *              "aidcaldt" : "Data de alteracao"
     *          },
     *      ]
     * }
     */
    public function list_all() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->areaofinterest_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->areaofinterest_bo->validate_list_all() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->areaofinterest_bo->get_errors();
        } else {
            $data = $this->areaofinterest_bo->get_data();
            $areasofinterest = $this->areainteresse_model->find_by_aicdesc($data['aicdesc']);
            // Verifica se houve falha na execucao do model
            if (is_null($areasofinterest)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Nenhum área de interesse encontrada.";
            } else {
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Área(s) de interesse encontrada(s) com sucesso!";
                $this->response['response_data'] = $areasofinterest;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

}