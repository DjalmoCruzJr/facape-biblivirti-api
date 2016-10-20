<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 20/10/2016
 *
 * Controller da API para gerenciar o acesso aos dados de uma <b>Questao</b>.
 */
class Question extends CI_Controller {

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
        $this->load->model("questao_model");
        $this->load->model("grupo_model");
        $this->load->model("material_model");
        $this->load->model("alternativa_model");

        // Loading libraries
        $this->load->library('business/question_bo');
        $this->load->library('input/biblivirti_input');
    }

    /**
     * @url: API/question/list
     * @param string JSON
     * @return JSON
     *
     * Metodo para listar todas as questoes de um determinado material (Simulado).
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid" : "ID do usuario",
     *      "manid" : "ID do material (simulado)"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : [
     *          {
     *              "qenid" : "ID da questao",
     *              "qecdesc" : "Descricao da questao",
     *              "qectext" : "Texto da questao",
     *              "qecanex" : "Anexo da questao",
     *              "qedcadt" : "Data de cadastro",
     *              "qedaldt" : "Data de alteracao",
     *              "alternatives" : [
     *                  {
     *                      "alnid" : "ID da alternativa",
     *                      "alnidqe" : "ID da questao pai",
     *                      "alctext" : "Texto da alternativa",
     *                      "allcert" : "Flag que define se a alternativa eh verdadeira/false",
     *                      "aldcadt" : "Data de dadastro",
     *                      "aldaldt" : "Data de alteracao"
     *                  },
     *              ]
     *          },
     *      ]
     * }
     */
    public function list_all() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->question_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->question_bo->validate_list_all() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->question_bo->get_errors();
        } else {
            $data = $this->question_bo->get_data();
            $material = $this->material_model->find_by_manid($data['manid']);
            $users = $this->grupo_model->find_group_users($material->manidgr);

            $is_member = false;
            if (!is_null($users)) { // Percorre a lista de usuario do grupo em busca do usuario logado
                foreach ($users as $user) {
                    if ($user->usnid == $data['usnid']) {
                        $is_member = true;
                        break;
                    }
                }
            }

            if ($is_member === false) { // Verifica se o usuario logado nao eh membro do grupo
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Erro ao tentar carregar as informações das questões!\n";
                $this->response['response_message'] .= "Somente membros podem ter acesso a questões de simulados do grupo.";
            } else {
                $questions = $this->material_model->find_material_questions($material->manid);

                if (is_null($questions)) { // Verifica se as quesoes foram carregadas com sucesso
                    $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $this->response['response_message'] = "Nenhuma questão encontrada!";
                } else {
                    foreach ($questions as $question) {
                        $question->alternatives = $this->alternativa_model->find_by_alnidqe($question->qenid);
                    }
                    $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $this->response['response_message'] = "Questão(ões) carregadas com sucesso!";
                    $this->response['response_data'] = $questions;
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }


}