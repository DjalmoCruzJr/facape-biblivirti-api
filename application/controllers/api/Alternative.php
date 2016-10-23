<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 23/10/2016
 *
 * Controller da API para gerenciar o acesso aos dados de uma <b>Alternativa</b>.
 */
class Alternative extends CI_Controller {

    /**
     * @var array
     *
     * Armazena os dados de resposta das requisicoes.
     */
    private $response;

    /**
     * Alternative constructor.
     */
    public function __construct() {
        parent::__construct();

        // Initializing variables
        $this->response = [];

        // Loading models
        $this->load->model("alternativa_model");
        $this->load->model("grupo_model");

        // Loading libraries
        $this->load->library('business/alternative_bo');
        $this->load->library('input/biblivirti_input');
    }

    /**
     * @url: API/alternative/add
     * @param string JSON
     * @return JSON
     *
     * Metodo para adicionar uma alternativa a uma questao.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid" : "ID do usuario",
     *      "grnid" : "ID do grupo",
     *      "alnidqe" : "ID do questao",
     *      "alctext" : "Texto  da alternativa",
     *      "allcert" : "Define se a alternativa eh verdadeira ou falsa"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : {
     *          "alnid" : "ID da alternativa"
     *      }
     * }
     */
    public function add() {

        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->alternative_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->alternative_bo->validate_add() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->alternative_bo->get_errors();
        } else {
            $data = $this->alternative_bo->get_data();
            $admin = $this->grupo_model->find_group_admin($data['grnid']);

            if ($admin->usnid != $data['usnid']) { // Verifica se o usuario logado nao eh admin do grupo
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Erro ao tentar adicionar alternativa!\n";
                $this->response['response_message'] .= "Somente o administrador do grupo têm permissão para adicioná-las.";
            } else {
                unset($data['usnid']); // Remove o campo ID DO USUARIO do objeto a ser salvo no banco
                unset($data['grnid']); // Remove o campo ID DO GRUPO do objeto a ser salvo no banco

                $has_correct = false;

                if (boolval($data['allcert']) === true) { // Verificas se a alternativa a ser incluida eh verdadeira
                    $alternatives = $this->alternativa_model->find_by_alnidqe($data['alnidqe']);

                    foreach ($alternatives as $alternative) { // Percorre todas as alternativas vinculadas a questao informada
                        if (boolval($alternative->allcert) === true) {
                            $has_correct = true;
                            break;
                        }
                    }
                }

                if ($has_correct === true) { // Verifica se ja existe uma questao certa vinculda a questao informada
                    $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                    $this->response['response_message'] = "Erro ao tentar adicionar alternativa!\n";
                    $this->response['response_message'] .= "Já existe uma alternativa correta adicionada para esta questão.";
                } else {

                    $id = $this->alternativa_model->save($data);

                    if (is_null($id)) { // Verifica se as mensagens foram carregadas com sucesso
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar adicionar a alternativa! Tente novamente.\n";
                        $this->response['response_message'] .= "Se o erro persistir entre em contato com a equipe de suporte do Biblivirti AVAM.";
                    } else {
                        $this->response['response_code'] = RESPONSE_CODE_OK;
                        $this->response['response_message'] = "Alternativa adicionada com sucesso!";
                        $this->response['response_data'] = ['alnid' => $id];
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

}