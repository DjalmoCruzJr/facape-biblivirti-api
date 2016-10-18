<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 17/10/2016
 *
 * Controller da API para gerenciar o acesso aos dados de um <b>Comentario</b>.
 */
class Comment extends CI_Controller {

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
        $this->load->model("usuario_model");
        $this->load->model("grupo_model");

        // Loading libraries
        $this->load->library('business/comment_bo');
        $this->load->library('input/biblivirti_input');
        $this->load->library('email/biblivirti_email');
    }

    /**
     * @url: API/comment/add
     * @param string JSON
     * @return JSON
     *
     * Metodo para adicionar um comentario para umj material.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "cenidus" : "ID do usuario",
     *      "cenidma" : "ID do material",
     *      "cenidce" : "ID do comentario pai (nesse caso trata-se de uma RESPOSTA)",
     *      "cectext" : "Texto do comentario",
     *      "cecanex" : "Anexo do comentario",
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : {
     *          "cenid" : "ID do comentario"
     *      }
     * }
     */
    public function add() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->comment_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->comment_bo->validate_add() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->comment_bo->get_errors();
        } else {
            $data = $this->comment_bo->get_data();
            $material = $this->material_model->find_by_manid($data['cenidma']);
            $users = $this->grupo_model->find_group_users($material->manidgr);

            $is_member = false;
            if (!is_null($users)) { // Percorre a lista de usuario do grupo em busca do usuario logado
                foreach ($users as $user) {
                    if ($user->usnid == $data['cenidus']) {
                        $is_member = true;
                        break;
                    }
                }
            }

            if ($is_member === false) { // Verifica se o usuario logado nao eh membro do grupo
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Erro ao tentar enviar comentário!\n";
                $this->response['response_message'] .= "Somente membros têm permissão para comentar os materiais do grupo.";
            } else {
                $id = $this->comentario_model->save($data);

                if (is_null($id)) { // Verifica se as mensagens foram carregadas com sucesso
                    $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                    $this->response['response_message'] = "Houve um erro ao tentar enviar o comentário! Tente novamente.\n";
                    $this->response['response_message'] .= "Se o erro persistir entre em contato com a equipe de suporte do Biblivirti AVAM.";
                } else {
                    // Carrega os dados para o envio do email de notificacao
                    $user = $this->usuario_model->find_by_usnid($data['cenidus']);
                    $group = $this->grupo_model->find_by_grnid($material->manidgr);
                    // Seta os dados para o envio do email de notificação de novo comentario
                    $from = EMAIL_SMTP_USER;
                    $to = $user->uscmail;
                    $subject = EMAIL_SUBJECT_NEW_COMMENT;
                    $message = EMAIL_MESSAGE_NEW_COMMENT;
                    $datas = [
                        EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                        EMAIL_KEY_USCNOME => (!is_null($user->uscnome)) ? $user->uscnome : $user->usclogn,
                        EMAIL_KEY_MACDESC => $material->macdesc,
                        EMAIL_KEY_CECTEXT => $data['cectext'],
                        EMAIL_KEY_GRCNOME => $group->grcnome,
                        EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                        EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                    ];

                    $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                    if ($this->biblivirti_email->send() === false) {
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_NEW_COMMENT . "!\n";
                        $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                        $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                    } else {
                        $this->response['response_code'] = RESPONSE_CODE_OK;
                        $this->response['response_message'] = "Comentário adicionado com sucesso!";
                        $this->response['response_data'] = ['cenid' => $id];
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

}