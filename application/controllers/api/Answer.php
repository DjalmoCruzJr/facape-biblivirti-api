<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 25/10/2016
 *
 * Controller da API para gerenciar o acesso aos dados de uma <b>Resposta</b>.
 */
class Answer extends CI_Controller {

    /**
     * @var array
     *
     * Armazena os dados de resposta das requisicoes.
     */
    private $response;

    /**
     * Answer constructor.
     */
    public function __construct() {
        parent::__construct();

        // Initializing variables
        $this->response = [];

        // Loading models
        $this->load->model("avaliacao_model");
        $this->load->model("resposta_model");
        $this->load->model("grupo_model");
        $this->load->model("alternativa_model");
        $this->load->model("material_model");

        // Loading libraries
        $this->load->library('business/answer_bo');
        $this->load->library('input/biblivirti_input');
        $this->load->library('email/biblivirti_email');
    }

    /**
     * @url: API/answer/list
     * @param string JSON
     * @return JSON
     *
     * Metodo para listar as respostas de uma avaliacao.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid": "ID do usuario",
     *      "avnid": "ID da avaliacao"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : [
     *          {
     *              "renid" : "ID da resposta",
     *              "renidav" : "ID da avaliacao",
     *              "recstat" : "Status da resposta",
     *              "redindt" : "Data de inicio (Dt. de entrada na questao)",
     *              "redendt" : "Data de envio (Dt. de submissao da resposta)",
     *              "alternative" : {
     *                  "alnid" : "ID do alternativa",
     *                  "alnidqe" : "ID do questao da alternativa",
     *                  "alctext" : "Texto  da alternativa",
     *                  "allcert" : "Define se a alternativa eh verdadeira ou falsa",
     *                  "aldcadt" : "Data de cadastro",
     *                  "aldaldt" : "Data de alteracao"
     *              }
     *          },
     *      ]
     * }
     */
    public function list_all() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->answer_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->answer_bo->validate_list_all() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->answer_bo->get_errors();
        } else {
            $data = $this->answer_bo->get_data();
            $test = $this->avaliacao_model->find_by_avnid($data['avnid']);
            $material = null;
            $admin = null;

            if ($test->avnidus != $data['usnid']) { // Verifica se o usuario logado eh diferente do usuario da avaliacao
                $material = $this->material_model->find_by_manid($test->avnidma);
                $admin = $this->grupo_model->find_group_admin($material->manidgr);
                if ($admin->usnid != $data['usnid']) { // Verifica se o usuario logado nao eh administrador do grupo
                    $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                    $this->response['response_message'] = "Erro ao carregar informações das respostas!\n";
                    $this->response['response_message'] .= "Somente o administrador do grupo e o próprio membro da avaliação têm permissão para visualizá-las!";
                }
            } else {
                $answers = $this->resposta_model->find_by_renidav($test->avnid);

                /**
                 * VERIFICAR SE ESTE TESTE ESTA FUNCIONANDO CORRETAMENTE
                 */
                if (is_null($answers)) {  // Verifica se as respostadas nao foram carregadas com sucesso
                    $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $this->response['response_message'] = "Nenhuma resposta encontrada!";
                } else {
                    // Percorre todas as respostas carregando a alternativa de cada uma
                    foreach ($answers as $answer) {
                        $answer->alternative = $this->alternativa_model->find_by_alnid($answer->renidal);
                        unset($answer->renidal); // Remove o campo ID DA ALTERNATIVA do objeto de resposta
                    }
                    $this->response['response_code'] = RESPONSE_CODE_OK;
                    $this->response['response_message'] = "Resposta(s) encontrada(s) com sucesso!";
                    $this->response['response_data'] = $answers;
                }

            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/answer/add
     * @param string JSON
     * @return JSON
     *
     * Metodo para slavar uma respostas de uma avaliacao.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid": "ID da usuario",
     *      "renidav": "ID da avaliacao",
     *      "renidal": "ID da alternativa"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" :  {
     *          "renid" : "ID da resposta"
     *      }
     * }
     */
    public function add() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->answer_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->answer_bo->validate_add() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->answer_bo->get_errors();
        } else {
            $data = $this->answer_bo->get_data();
            $test = $this->avaliacao_model->find_by_avnid($data['renidav']);

            if (strval($test->avcstat) == AVCSTAT_FINALIZADA) { // Verifica se a avaliacao ja esta finalizada
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Erro ao tentar adicionar resposta a avaliação!\n";
                $this->response['response_message'] .= "Essa avaliação já foi finalizada!";
            } else if ($test->avnidus != $data['usnid']) {
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Erro ao tentar adicionar resposta a avaliação!\n";
                $this->response['response_message'] .= "Somente o usuário que inicio a avaliação tem permissão para adicionar respostas.";
            } else {
                unset($data['usnid']); // Remove o campo ID DO USUARIO do objeto a ser salvo no banco
                $id = $this->resposta_model->save($data);

                if (is_null($id)) { // Verifica se houve falha ao salvar a resposta
                    $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                    $this->response['response_message'] = "Houve um erro ao tentar salvar resposta! Tente novamente.\n";
                    $this->response['response_message'] .= "Se o erro persistir entre em contato com a equipe de suporte do Biblivirti AVAM.";
                } else {
                    $this->response['response_code'] = RESPONSE_CODE_OK;
                    $this->response['response_message'] = "Resposta salva com sucesso!";
                    $this->response['response_data'] = ['renid' => $id];
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }


}