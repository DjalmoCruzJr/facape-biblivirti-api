<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 11/09/2016
 *
 * Controller da API para gerenciar o acesso aos dados de <b>Conteudo</b>.
 */
class Content extends CI_Controller {

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
        $this->load->model("conteudo_model");

        // Loading libraries
        $this->load->library('business/content_bo');
        $this->load->library('input/biblivirti_input');
    }

    /**
     * @url: API/content/list
     * @param string JSON
     * @return JSON
     *
     * Metodo para buscar todos os conteudos de um determinado grupo.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "grnid" : "ID do grupo"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : [
     *          {
     *              "conid" : "ID do conteudo",
     *              "cocdesc" : "Descricao do conteudo",
     *              "codcadt" : "Data de cadastro do conteudo",
     *              "codaldt" : "Data de alteracao do conteudo"
     *          },
     *      ]
     * }
     */
    public function list_all() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->content_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->content_bo->validate_list_all() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->content_bo->get_errors();
        } else {
            $data = $this->content_bo->get_data();
            $contents = $this->conteudo_model->find_by_grnid($data['grnid']);
            // Verifica se houve falha na execucao do model
            if (is_null($contents)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Nenhum conteúdo encontrado.";
            } else {
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Conteúdo(s) encontrado(s) com sucesso!";
                $this->response['response_data'] = $contents;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/content/add
     * @param string JSON
     * @return JSON
     *
     * Metodo para cadastrar um novo conteudo.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "grnid" : "ID do grupo",
     *      "cocdesc" : "Descricao do conteudo"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : {
     *          "conid" : "ID do conteudo"
     *      }
     * }
     */
    public function add() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->content_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->content_bo->validate_add() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->content_bo->get_errors();
        } else {
            $data = $this->content_bo->get_data();
            $id = $this->conteudo_model->save($data);
            // Verifica se houve falha na execucao do model
            if (is_null($id)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "Houve um erro ao tentar cadastrar o conteúdo! Tente novamente.\n";
                $response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
            } else {
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Conteúdo cadastrado com sucesso!";
                $this->response['response_data'] = ['conid' => $id];
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/content/edit
     * @param string JSON
     * @return JSON
     *
     * Metodo para editar um conteudo.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "grnid" : "ID do grupo",
     *      "conid" : "ID do conteudo",
     *      "cocdesc" : "Descricao do conteudo"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : {
     *          "conid" : "ID do conteudo"
     *      }
     * }
     */
    public function edit() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->content_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->content_bo->validate_edit() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->content_bo->get_errors();
        } else {
            $data = $this->content_bo->get_data();
            $content = $this->conteudo_model->find_by_conid($data['conid']);
            // verifica se houve falha na execucao do model
            if (is_null($content)) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "Nenhum conteúdo encontrado.";
            } else {
                $this->conteudo_model->save($data);
                $response['response_message'] = "Conteúdo atualizado com sucesso!";
                $response['response_data'] = ['conid' => $data['conid']];
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

}