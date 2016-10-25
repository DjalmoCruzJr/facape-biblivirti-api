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
        $this->load->model("resposta_model");
        $this->load->model("alternativa_model");
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

    /**
     * @url: API/test/finalize
     * @param string JSON
     * @return JSON
     *
     * Metodo para finalizar uma avaliacao de um usuario em um simulado.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid": "ID do usuario",
     *      "avnid": "O da avaliacao"
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
    public function finalize() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->test_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->test_bo->validate_finalize() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->test_bo->get_errors();
        } else {
            $data = $this->test_bo->get_data();
            $test = $this->avaliacao_model->find_by_avnid($data['avnid']);

            if ($test->avcstat === AVCSTAT_FINALIZADA) { // Verifica se a avaliacao ja esta finalizada
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Erro ao tentar finalizar avaliação!\n";
                $this->response['response_message'] .= "Esta avaliação já foi finalizada!";
            } else if ($test->avnidus != $data['usnid']) { // Verifica se o usuario da avaliacao eh diferente do usuario logado
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Erro ao tentar finalizar avaliação!\n";
                $this->response['response_message'] .= "Somente o usuário que inicio a avaliação tem permissão para finalizá-la.";
            } else {
                $questions = $this->material_model->find_material_questions($test->avnidma);
                $answers = $this->resposta_model->find_by_renidav($test->avnid);

                $correct_answers_count = 0;
                foreach ($answers as $answer) { // Percorre todas a resposta da avaliacao para contar a qtd de respostas certas
                    if (strval($answer->recstat) == RECSTAT_FINALIZADA) { // Verifica se a resposta ja esta finalizada (ja foi submetida)
                        $alternative = $this->alternativa_model->find_by_alnid($answer->renidal);
                        if (boolval($alternative->allcert) === true) { // Verifica se a alternativa da resposta esta correta
                            $correct_answers_count++;
                        }
                    }
                }

                // Verifica se q qtd de resposta certas eh diferente da qtd de quesoes do simulado
                if ($correct_answers_count != count($questions)) {
                    $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                    $this->response['response_message'] = "Erro ao tentar finalizar avaliação!\n";
                    $this->response['response_message'] .= "Existem questões do simulado que ainda não foram respondidas corretamente.";
                } else {
                    $data = [];
                    $data['avnid'] = $test->avnid;
                    $data['avcstat'] = AVCSTAT_FINALIZADA;

                    if ($this->avaliacao_model->update($data) === false) { // Verifica se a avaliacao nao foi finalizada com sucesso
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar finalizar avaliação! Tente novamente.\n";
                        $this->response['response_message'] .= "Se o erro persistir entre em contato com a equipe de suporte do Biblivirti AVAM.";
                    } else {
                        // Carrega os dados para o envio do email
                        $test = $this->avaliacao_model->find_by_avnid($test->avnid);
                        $user = $this->usuario_model->find_by_usnid($test->avnidus);
                        $material = $this->material_model->find_by_manid($test->avnidma);
                        $group = $this->grupo_model->find_by_grnid($material->manidgr);
                        // Seta os dados para o envio do email de ativação de conta
                        $from = EMAIL_SMTP_USER;
                        $to = $user->uscmail;
                        $subject = EMAIL_SUBJECT_TEST_FINALIZED;
                        $message = EMAIL_MESSAGE_TEST_FINALIZED;
                        $datas = [
                            EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                            EMAIL_KEY_USCNOME => (isset($user->uscnome) === true) ? $user->uscnome : $user->usclogn,
                            EMAIL_KEY_GRCNOME => $group->grcnome,
                            EMAIL_KEY_MACDESC => $material->macdesc,
                            EMAIL_KEY_AVNID => $test->avnid,
                            EMAIL_KEY_AVDINDT => date('d/m/Y H:i:s', strtotime($test->avdindt)),
                            EMAIL_KEY_AVDTEDT => date('d/m/Y H:i:s', strtotime($test->avdtedt)),
                            EMAIL_KEY_MANQTDQE => count($questions),
                            EMAIL_KEY_AVNQTDRE => count($answers),
                            EMAIL_KEY_AVNQTDACE => $correct_answers_count,
                            EMAIL_KEY_AVNQTDERR => (count($answers) - $correct_answers_count),
                            EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                            EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                        ];

                        $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                        if ($this->biblivirti_email->send() === false) {
                            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                            $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_TEST_FINALIZED . "! Tente novamente.\n";
                            $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                            $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                        } else {
                            $this->response['response_code'] = RESPONSE_CODE_OK;
                            $this->response['response_message'] = "Avaliação finalizada com sucesso!";
                            $this->response['response_data'] = ['avnid' => $test->avnid];
                        }
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }
}