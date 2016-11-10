<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 09/11/2016
 *
 * Controller da API para gerenciar o acesso aos dados de um <b>Duvida</b>.
 */
class Doubt extends CI_Controller {

    /**
     * @var array
     *
     * Armazena os dados de resposta das requisicoes.
     */
    private $response;

    /**
     * Doubt constructor.
     */
    public function __construct() {
        parent::__construct();

        // Initializing variables
        $this->response = [];

        // Loading models
        $this->load->model("duvida_model");
        $this->load->model("duvidaresposta_model");
        $this->load->model("usuario_model");
        $this->load->model("grupo_model");

        // Loading libraries
        $this->load->library('business/doubt_bo');
        $this->load->library('input/biblivirti_input');
        $this->load->library('email/biblivirti_email');
    }

    /**
     * @url: API/doubt/list
     * @param string JSON
     * @return JSON
     *
     * Metodo para listar as duvidades de um grupo.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "dvnidgr" : "ID do grupo"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : [
     *          {
     *              "dvnid" : "ID da duvida",
     *              "dvnidgr" : "ID da duvida",
     *              "dvctext" : "Texto da duvida",
     *              "dvcanex" : "Anexo da duvida",
     *              "dvcstat" : "Status da duvida",
     *              "dvlanon" : "Define se a duvida EH ou NAO anonima",
     *              "dvdcadt" : "Data de cadastro",
     *              "dvdaldt" : "Data de cadastro",
     *              "dvnqtddr" : "Qtd. respostas",
     *              "user" : {
     *                  "usnid" : "ID do usuario",
     *                  "uscfbid" : "FacebookID do usuario",
     *                  "uscnome" : "Nome do usuario",
     *                  "uscmail" : "E-email do usuario",
     *                  "usclogn" : "Login do usuario",
     *                  "uscfoto" : "Caminho da foto do usuario",
     *                  "uscstat" : "Status do usuario",
     *                  "usdcadt" : "Data de cadastro do usuario",
     *                  "usdaldt" : "Data de atualizacao do usuario"
     *              }
     *          }
     *      }
     * }
     */
    public function list_all() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->doubt_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->doubt_bo->validate_list_all() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->doubt_bo->get_errors();
        } else {
            $data = $this->doubt_bo->get_data();
            $users = $this->grupo_model->find_group_users($data['dvnidgr']);

            $is_member = false;
            foreach ($users as $user) {
                if ($user->usnid == $data['usnid']) { // Verifica se o usuario logado eh esta na lista de usuaios do grupo
                    $is_member = true;
                    break;
                }
            }

            if ($is_member === false) { // Verifica se o usuario da requisicao eh um membro do grupo
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Erro ao carregar informações de dúvidas!\n";
                $this->response['response_message'] .= "Somente membros do grupo têm permissão para vê-las.";
            } else {
                $doubts = $this->duvida_model->find_by_dvnidgr($data['dvnidgr']);

                if (is_null($doubts)) { // Verifica se as duvidas foram carregadas com sucesso
                    $this->response['response_code'] = RESPONSE_CODE_OK;
                    $this->response['response_message'] = "Nenhuma dúvida encontrada.";
                } else {

                    foreach ($doubts as $doubt) {
                        $doubt->dvnqtddr = count($this->duvidaresposta_model->find_by_drniddv($doubt->dvnid)); // Carrega a qtd de respostas da duvida
                        $doubt->user = $this->usuario_model->find_by_usnid($doubt->dvnidus); // Carrega o usuario da duvida
                        unset($doubt->dvnidus); // Remove o campo ID DO USUARIO DA DUVIDA
                        unset($doubt->user->uscsenh);// Remove o campo SENHA DO USUARIO DA DUVIDA
                    }

                    $this->response['response_code'] = RESPONSE_CODE_OK;
                    $this->response['response_message'] = "Dúvida(s) carregada(s) com sucesso!";
                    $this->response['response_data'] = $doubts;
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

}