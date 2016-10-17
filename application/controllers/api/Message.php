<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 16/10/2016
 *
 * Controller da API para gerenciar o acesso aos dados de uma <b>Mensagem</b>.
 */
class Message extends CI_Controller {

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
        $this->load->model("mensagem_model");
        $this->load->model("usuario_model");
        $this->load->model("grupo_model");

        // Loading libraries
        $this->load->library('business/message_bo');
        $this->load->library('input/biblivirti_input');
        $this->load->library('email/biblivirti_email');
    }

    /**
     * @url: API/message/list
     * @param string JSON
     * @return JSON
     *
     * Metodo para listar todas as mensagens de um grupo.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid" : "ID do usuario",
     *      "grnid" : "ID do grupo"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : {
     *          "msnid" : "ID da mensagem",
     *          "msnidgr" : "ID do grupo",
     *          "msctext" : "Texto da mensagem",
     *          "mscanex" : "Anexo da mensagem",
     *          "mscstat" : "Status da mensagem",
     *          "msdcadt" : "Data de cadastro",
     *          "user" : {
     *              "usnid" : "ID do usuario",
     *              "uscfbid" : "FacebookID do usuario",
     *              "uscnome" : "Nome do usuario",
     *              "uscmail" : "E-email do usuario",
     *              "usclogn" : "Login do usuario",
     *              "uscfoto" : "Caminho da foto do usuario",
     *              "uscstat" : "Status do usuario",
     *              "tsdcadt" : "Data de cadastro do usuario",
     *              "usdaldt" : "Data de atualizacao do usuario"
     *          }
     *      }
     * }
     */
    public function list_all() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->message_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->message_bo->validate_list_all() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->message_bo->get_errors();
        } else {
            $data = $this->message_bo->get_data();
            $users = $this->grupo_model->find_group_users($data['grnid']);

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
                $this->response['response_message'] = "Erro ao tentar carregar as informações das mensagens!\n";
                $this->response['response_message'] .= "Somente membros podem ter acesso as mensagens do grupo.";
            } else {
                $messages = $this->mensagem_model->find_by_msnidgr($data['grnid']);

                if (is_null($messages)) { // Verifica se as mensagens foram carregadas com sucesso
                    $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $this->response['response_message'] = "Nenhuma mensagem encontrada!";
                } else {
                    foreach ($messages as $message) {
                        $message->user = $this->usuario_model->find_by_usnid($message->msnidus);
                        unset($message->msnidus); // Remove o campo ID DO USUAIRO da objeto de resposta
                    }

                    $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $this->response['response_message'] = "Mensagem(s) encontradas com sucesos!";
                    $this->response['response_data'] = $messages;
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/message/add
     * @param string JSON
     * @return JSON
     *
     * Metodo para enviar uma mensagem para um grupo.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "msnidgr" : "ID do grupo da mensagem",
     *      "msnidus" : "ID do usuario da mensagem",
     *      "msctext" : "Texto da mensagem",
     *      "mscanex" : "Anexo da mensagem",
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : {
     *          "msnid" : "ID da mensagem"
     *      }
     * }
     */
    public function add() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->message_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->message_bo->validate_add() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->message_bo->get_errors();
        } else {
            $data = $this->message_bo->get_data();
            $users = $this->grupo_model->find_group_users($data['msnidgr']);

            $is_member = false;
            if (!is_null($users)) { // Percorre a lista de usuario do grupo em busca do usuario logado
                foreach ($users as $user) {
                    if ($user->usnid == $data['msnidus']) {
                        $is_member = true;
                        break;
                    }
                }
            }

            if ($is_member === false) { // Verifica se o usuario logado nao eh membro do grupo
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Erro ao tentar enviar mensagem!\n";
                $this->response['response_message'] .= "Somente membros têm permissão para enviar mensagens para o grupo.";
            } else {
                $id = $this->mensagem_model->save($data);

                if (is_null($id)) { // Verifica se as mensagens foram carregadas com sucesso
                    $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                    $this->response['response_message'] = "Houve um erro ao tentar enviar a mensagem! Tente novamente.\n";
                    $this->response['response_message'] .= "Se o erro persistir entre em contato com a equipe de suporte do Biblivirti AVAM.";
                } else {
                    // Carrega os dados para o envio do email de notificacao
                    $user = $this->usuario_model->find_by_usnid($data['msnidus']);
                    $group = $this->grupo_model->find_by_grnid($data['msnidgr']);
                    // Seta os dados para o envio do email de notificação de novo grupo
                    $from = EMAIL_SMTP_USER;
                    $to = $user->uscmail;
                    $subject = EMAIL_SUBJECT_NEW_MESSAGE;
                    $message = EMAIL_MESSAGE_NEW_MESSAGE;
                    $datas = [
                        EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                        EMAIL_KEY_USCNOME => (!is_null($user->uscnome)) ? $user->uscnome : $user->usclogn,
                        EMAIL_KEY_GRCNOME => $group->grcnome,
                        EMAIL_KEY_MSCTEXT => $data['msctext'],
                        EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                        EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                    ];

                    $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                    if ($this->biblivirti_email->send() === false) {
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_NEW_MESSAGE . "!\n";
                        $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                        $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                    } else {
                        $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                        $this->response['response_message'] = "Mensagem enviada com sucesso!";
                        $this->response['response_data'] = ['msnid' => $id];
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

}