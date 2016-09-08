<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 01/09/2016
 *
 * Controller da API para gerenciar o acesso aos dados de <b>Materiais</b>.
 */
class Material extends CI_Controller {

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
        $this->load->model("material_model");

        // Loading libraries
        $this->load->library('business/material_bo');
        $this->load->library('input/biblivirti_input');
    }

    /**
     * @url: API/material/list
     * @param int grnid
     * @return JSON
     *
     * Metodo para buscar todos os materiais relacionados com um determinado grupo.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "grnid" : "ID do grupo",
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : [
     *         {    "manid" : "ID do material",
     *              "macdesc" : "Descricao do usuario",
     *              "mactipo" : "Tipo de material",
     *              "macurl" : "URL do material",
     *              "macnivl" : "Nivel do material",
     *              "macstat" : "Status do material",
     *              "madcadt" : "Data de cadastro do material",
     *              "madaldt" : "Data de atualizacao do material"
     *              "manqtdce" : "Qtd. de comentario do material"
     *              "manqtdha" : "Qtd. de vizualizacoes do material"
     *          },
     *      ]
     * }
     */
    public function list_all() {
        /*$data['grnid'] = $this->input->post('grnid');*/

        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->material_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->material_bo->validate_list_all() === false) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->material_bo->get_errors();
        } else {
            $data = $this->material_bo->get_data();
            $materials = $this->material_model->find_by_grnid($data['grnid']);
            // Verifica se houve falha na execucao do model
            if (is_null($materials)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Nenhum material encontrado.";
            } else {
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Material(ais) encontrado(s) com sucesso!";
                $this->response['response_data'] = $materials;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/material/add
     * @param string JSON
     * @return JSON
     *
     * Metodo para cadastrar um novo material.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "grnid" : "ID do grupo",
     *      "macdesc" : "Descricao do material",
     *      "mactipo" : "Tipo do material",
     *      "malanex" : "Define se o material eh um anexo",
     *      "macurl" : "URL do material",
     *      "macnivl" : "Nivel do material",
     *      "macstat" : "Status do material",
     *      "contents" : [
     *          {
     *              "conid" : "ID do conteudo relacionado"
     *          },
     *      ]
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : {
     *          "manid" : "ID do material",
     *      }
     * }
     */
    public function add() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->material_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->material_bo->validate_add() === false) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->material_bo->get_errors();
        } else {
            $data = $this->material_bo->get_data();
            var_dump($data);
            exit;
            $manid = $this->material_model->save($data);
            // Verifica se houve falha na execucao do model
            if (is_null($manid)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Houve um erro ao tentar cadastrar o material! Tente novamente.\n";
                $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
            } else {
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Material cadastrado com sucesso!";
                $this->response['response_data'] = $manid;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

}