<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 24/10/2016
 *
 * Controller da API para gerenciar o acesso aos dados de uma <b>Avaliacao</b>.
 */
class Test extends CI_Controller {

    /**
     * @var array
     *
     * Armazena os dados de resposta das requisicoes.
     */
    private $response;

    /**
     * Test constructor.
     */
    public function __construct() {
        parent::__construct();

        // Initializing variables
        $this->response = [];

        // Loading models
        $this->load->model("avaliacao_model");
        $this->load->model("grupo_model");
        $this->load->model("material_model");

        // Loading libraries
        $this->load->library('business/test_bo');
        $this->load->library('input/biblivirti_input');
        $this->load->library('email/biblivirti_email');
    }

    /**
     * @url: API/test/start
     * @param string JSON
     * @return JSON
     *
     * Metodo para iniciar a avaliacao de um usuario em um simulado.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "avnidus": "ID do usuario",
     *      "avnidma": "O do material"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : {
     *          "avnid" : "ID da avaliacao"
     *      }
     * }
     */
    public function start() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->test_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->test_bo->validate_start() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->test_bo->get_errors();
        } else {
            $data = $this->test_bo->get_data();
            $material = $this->material_model->find_by_manid($data['avnidma']);

            if ($material->mactipo !== MACTIPO_SIMULADO) { // Verifica se o material nao eh do tipo SIMULADO
                $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                $this->response['response_message'] = "Erro ao tentar iniciar navaliação!\n";
                $this->response['response_message'] .= "Avaliações só podem ser iniciadas a partir de materiais do tipo SIMULADO.";
            } else {
                $users = $this->grupo_model->find_group_users($material->manidgr);
                $is_member = false;
                if (!is_null($users)) { // Percorre a lista de usuario do grupo em busca do usuario logado
                    foreach ($users as $user) {
                        if ($user->usnid == $data['avnidus']) {
                            $is_member = true;
                            break;
                        }
                    }
                }

                if ($is_member === false) { // Verifica se o usuario logado nao eh membro do grupo
                    $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                    $this->response['response_message'] = "Erro ao tentar iniciar avaliação!\n";
                    $this->response['response_message'] .= "Somente membros podem ser avaliados pelos simulados do grupo.";
                } else if (!is_null($this->avaliacao_model->find_by_avnidus_and_avnidma_and_avcstat($data['avnidus'], $data['avnidma'], AVCSTAT_INICIADA))) { // Verifica se ja existe avaliacao em andamento do material informado para o usuario logado
                    $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                    $this->response['response_message'] = "Erro ao tentar iniciar avaliação!\n";
                    $this->response['response_message'] .= "Já existe uma avaliação em andamento deste simulado para este usuário.";
                } else {
                    $id = $this->avaliacao_model->save($data);

                    if (is_null($id)) { // Verifica houve erro ao gravar a avaliacao
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar iniciar avaliação! Tente novamente.\n";
                        $this->response['response_message'] .= "Se o erro persistir entre em contato com a equipe de suporte do Biblivirti AVAM.";
                    } else {
                        // Carrega os dados para o envio do email
                        $user = $this->usuario_model->find_by_usnid($data['avnidus']);
                        $group = $this->grupo_model->find_by_grnid($material->manidgr);
                        $test = $this->avaliacao_model->find_by_avnid($id);
                        // Seta os dados para o envio do email de ativação de conta
                        $from = EMAIL_SMTP_USER;
                        $to = $user->uscmail;
                        $subject = EMAIL_SUBJECT_NEW_TEST;
                        $message = EMAIL_MESSAGE_NEW_TEST;
                        $datas = [
                            EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                            EMAIL_KEY_USCNOME => (isset($user->uscnome) === true) ? $user->uscnome : $user->usclogn,
                            EMAIL_KEY_GRCNOME => $group->grcnome,
                            EMAIL_KEY_MACDESC => $material->macdesc,
                            EMAIL_KEY_AVNID => $test->avnid,
                            EMAIL_KEY_AVDINDT => date('d/m/Y H:i:s', strtotime($test->avdindt)),
                            EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                            EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                        ];

                        $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                        if ($this->biblivirti_email->send() === false) {
                            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                            $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_NEW_TEST . "! Tente novamente.\n";
                            $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                            $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                        } else {
                            $this->response['response_code'] = RESPONSE_CODE_OK;
                            $this->response['response_message'] = "Avaliação iniciada com sucesso!";
                            $this->response['response_data'] = ['avnid' => $id];
                        }
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

}