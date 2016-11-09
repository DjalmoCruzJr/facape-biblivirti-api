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

            var_dump($data);
            exit;
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/comment/edit
     * @param string JSON
     * @return JSON
     *
     * Metodo para editar um comentario/resposta de um material.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "cenid" : "ID do comentario/resposta",
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
    public function edit() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->comment_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->comment_bo->validate_edit() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->comment_bo->get_errors();
        } else {
            $data = $this->comment_bo->get_data();
            $material = $this->material_model->find_by_manid($data['cenidma']);
            $users = $this->grupo_model->find_group_users($material->manidgr);

            $is_comment_user = false;
            if (!is_null($users)) { // Verifica se a lista de usuarios do grupo esta vazia
                foreach ($users as $user) { // Percorre a lista de usuario do grupo em busca do usuario logado
                    if ($user->usnid == $data['cenidus']) { // Verifica se o usuario eh o mesmo que adicionou o comentario/resposta
                        $is_comment_user = true;
                        break;
                    }
                }
            }

            // Verifica se nao foi o usuario que adicionou o comentario/resposta
            if ($is_comment_user === false) {
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Erro ao tentar editar comentário/resposta!\n";
                $this->response['response_message'] .= "Somente o membro que adicionou o(a) comentário/resposta têm permissão para editá-lo(a).";
            } else {
                $id = $this->comentario_model->update($data);

                if (is_null($id)) { // Verifica se as mensagens foram carregadas com sucesso
                    $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                    $this->response['response_message'] = "Houve um erro ao tentar editar comentário/resposta! Tente novamente.\n";
                    $this->response['response_message'] .= "Se o erro persistir entre em contato com a equipe de suporte do Biblivirti AVAM.";
                } else {
                    // Carrega os dados para o envio do email de notificacao
                    $user = $this->usuario_model->find_by_usnid($data['cenidus']);
                    $group = $this->grupo_model->find_by_grnid($material->manidgr);
                    // Seta os dados para o envio do email de notificação
                    $from = EMAIL_SMTP_USER;
                    $to = $user->uscmail;
                    $subject = (!isset($data['cenidce'])) ? EMAIL_SUBJECT_EDIT_COMMENT : EMAIL_SUBJECT_EDIT_ANSWER;
                    $message = (!isset($data['cenidce'])) ? EMAIL_MESSAGE_EDIT_COMMENT : EMAIL_MESSAGE_EDIT_ANSWER;
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
                        $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . $subject . "!\n";
                        $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                        $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                    } else {
                        $this->response['response_code'] = RESPONSE_CODE_OK;
                        $this->response['response_message'] = "Comentário/Resposta atualizado(a) com sucesso!";
                        $this->response['response_data'] = ['cenid' => $id];
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/comment/delete
     * @param string JSON
     * @return JSON
     *
     * Metodo para excluir um comentario/resposta de um material.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid" : "ID do usuario",
     *      "cenid" : "ID do comentario/resposta"
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
    public function delete() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->comment_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->comment_bo->validate_delete() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->comment_bo->get_errors();
        } else {
            $data = $this->comment_bo->get_data();
            $comment = $this->comentario_model->find_by_cenid($data['cenid']);
            $material = $this->material_model->find_by_manid($comment->cenidma);
            $admin = $this->grupo_model->find_group_admin($material->manidgr);

            // Verifica se o usuario nao eh o mesmo que adicionou o(a) comentario/resposta e nao eh administrador do grupo
            if ($comment->cenidus != $data['usnid'] && $admin->usnid != $data['usnid']) {
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Erro ao tentar excluir comentário/resposta!\n";
                $this->response['response_message'] .= "Somente o administrador do grupo ou o membro que adicionou o(a) comentário/resposta têm permissão para excluí-lo(a).";
            } else {
                $data = [];
                $data['cenid'] = $comment->cenid;
                $data['cecstat'] = CECSTAT_INATIVO;
                $id = $this->comentario_model->update($data);

                // Verifica se a alteração foi realizada com sucesso
                if (is_null($id)) {
                    $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                    $this->response['response_message'] = "Houve um erro ao tentar excluir comentário/resposta! Tente novamente.\n";
                    $this->response['response_message'] .= "Se o erro persistir entre em contato com  a equipe de suporte do Biblivirti AVAM.";
                } else {
                    // Carrega os dados para o envio do email de notificacao
                    $user = $this->usuario_model->find_by_usnid($comment->cenidus);
                    $group = $this->grupo_model->find_by_grnid($material->manidgr);
                    // Seta os dados para o envio do email
                    $from = EMAIL_SMTP_USER;
                    $to = $user->uscmail;
                    $subject = (!isset($comment->cenidce)) ? EMAIL_SUBJECT_DELETE_COMMENT : EMAIL_SUBJECT_DELETE_ANSWER;
                    $message = (!isset($comment->cenidce)) ? EMAIL_MESSAGE_DELETE_COMMENT : EMAIL_MESSAGE_DELETE_ANSWER;
                    $datas = [
                        EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                        EMAIL_KEY_USCNOME => (!is_null($user->uscnome)) ? $user->uscnome : $user->usclogn,
                        EMAIL_KEY_MACDESC => $material->macdesc,
                        EMAIL_KEY_CECTEXT => $comment->cectext,
                        EMAIL_KEY_GRCNOME => $group->grcnome,
                        EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                        EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                    ];

                    $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                    if ($this->biblivirti_email->send() === false) {
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . $subject . "!\n";
                        $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                        $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                    } else {
                        $this->response['response_code'] = RESPONSE_CODE_OK;
                        $this->response['response_message'] = "Comentário/Resposta excluído(a) com sucesso!";
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

}