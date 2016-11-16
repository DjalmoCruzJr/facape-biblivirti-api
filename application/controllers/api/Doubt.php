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

    /**
     * @url: API/doubt/add
     * @param string JSON
     * @return JSON
     *
     * Metodo para adicionar uma duvida em um grupo.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "dvnidgr" : "ID do grupo da duvida",
     *      "dvnidus" : "ID do usuario da duvida",
     *      "dvctext" : "Texto da duvida",
     *      "dvcanex" : "Anexo da duvida",
     *      "dvlanon" : "Define se dúvida eh anonima ou nao",
     *      "contents" : [
     *          {
     *              "conid" : "ID do conteudo relacionado"
     *          },
     *      ]
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : {
     *          "dvnid" : "ID da duvida",
     *      }
     * }
     */
    public function add() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->doubt_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->doubt_bo->validate_add() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->doubt_bo->get_errors();
        } else {
            $data = $this->doubt_bo->get_data();
            $users = $this->grupo_model->find_group_users($data['dvnidgr']);

            $is_member = false;
            foreach ($users as $user) {
                if ($user->usnid == $data['dvnidus']) { // Verifica se o usuario logado eh esta na lista de usuaios do grupo
                    $is_member = true;
                    break;
                }
            }

            if ($is_member === false) { // Verifica se o usuario da requisicao eh um membro do grupo
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Erro ao adicionar dúvida ao grupo!\n";
                $this->response['response_message'] .= "Somente membros do grupo têm permissão para adicioná-las.";
            } else {
                $dvnid = $this->duvida_model->save($data);

                // Verifica se a duvida foi adicionada com sucesso
                if (is_null($dvnid)) {
                    $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                    $this->response['response_message'] = "Houve um erro ao tentar cadastrar o material! Tente novamente.\n";
                    $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                } else {
                    // Carrega os dados para p envio do email de notificacao
                    $group = $this->grupo_model->find_by_grnid($data['dvnidgr']);
                    $user = $this->usuario_model->find_by_usnid($data['dvnidus']);
                    // Seta os dados para o envio do email de notificação de novo grupo
                    $from = EMAIL_SMTP_USER;
                    $to = $user->uscmail;
                    $subject = EMAIL_SUBJECT_NEW_DOUBT;
                    $message = EMAIL_MESSAGE_NEW_DOUBT;
                    $datas = [
                        EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                        EMAIL_KEY_USCNOME => (!is_null($user->uscnome)) ? $user->uscnome : $user->usclogn,
                        EMAIL_KEY_GRCNOME => $group->grcnome,
                        EMAIL_KEY_DVNID => $dvnid,
                        EMAIL_KEY_DVCTEXT => $data['dvctext'],
                        EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                        EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                    ];

                    $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                    if ($this->biblivirti_email->send() === false) {
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_NEW_DOUBT . "!\n";
                        $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                        $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                    } else {
                        $this->response['response_code'] = RESPONSE_CODE_OK;
                        $this->response['response_message'] = "Dúvida adicionada com sucesso!";
                        $this->response['response_data'] = $dvnid;
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/doubt/edit
     * @param string JSON
     * @return JSON
     *
     * Metodo para atualizar as infomacoes de uma duvida.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid" : "ID do usuario",
     *      "dvnid" : "ID do grupo da duvida",
     *      "dvctext" : "Texto da duvida",
     *      "dvcanex" : "Anexo da duvida",
     *      "dvlanon" : "Define se dúvida eh anonima ou nao",
     *      "contents" : [
     *          {
     *              "conid" : "ID do conteudo relacionado"
     *          },
     *      ]
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : {
     *          "dvnid" : "ID da duvida",
     *      }
     * }
     */
    public function edit() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->doubt_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->doubt_bo->validate_edit() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->doubt_bo->get_errors();
        } else {
            $data = $this->doubt_bo->get_data();
            $doubt = $this->duvida_model->find_by_dvnid($data['dvnid']);
            // verifica se houve falha na execucao do model
            if (is_null($doubt)) {
                $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                $response['response_message'] = "Houve um erro ao tentar atualizar as informações da dúvida! Tente novamente.\n";
                $response['response_message'] .= "Se o erro persistir entre em contato com a equipe de suporte do Biblivirti AVAM.";
            } else {
                $admin = $this->grupo_model->find_group_admin($doubt->dvnidgr);

                // Verifica se o usuario logado nao eh admin do grupo e nao eh o usuario que adicionou a duvida
                if ($data['usnid'] != $admin->usnid && $data['usnid'] != $doubt->dvnidus) {
                    $response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                    $response['response_message'] = "Erro ao tentar editar dúvida!\n";
                    $response['response_message'] .= "Somente o usuário que adicionou a dúvida ou o administrador do grupo têm permissões para editá-la!";
                } else {
                    unset($data['usnid']); // Remove o ID do usuairo do objeto a ser gravado.
                    $this->duvida_model->update($data);

                    // Carrega os dados para o envio do email de notificacao
                    $user = $this->usuario_model->find_by_usnid($doubt->dvnidus);
                    $group = $this->grupo_model->find_by_grnid($doubt->dvnidgr);
                    // Seta os dados para o envio do email de notificação de novo grupo
                    $from = EMAIL_SMTP_USER;
                    $to = $user->uscmail;
                    $subject = EMAIL_SUBJECT_EDIT_DOUBT;
                    $message = EMAIL_MESSAGE_EDIT_DOUBT;
                    $datas = [
                        EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                        EMAIL_KEY_USCNOME => (!is_null($user->uscnome)) ? $user->uscnome : $user->usclogn,
                        EMAIL_KEY_GRCNOME => $group->grcnome,
                        EMAIL_KEY_DVNID => $data['dvnid'],
                        EMAIL_KEY_DVCTEXT => $data['dvctext'],
                        EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                        EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                    ];

                    $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                    if ($this->biblivirti_email->send() === false) {
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_EDIT_DOUBT . "!\n";
                        $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                        $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                    } else {
                        $response['response_code'] = RESPONSE_CODE_OK;
                        $response['response_message'] = "Dúvida atualizada com sucesso!";
                        $response['response_data'] = ['dvnid' => $doubt->dvnid];
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/doubt/delete
     * @param string JSON
     * @return JSON
     *
     * Metodo para excluir uma duvida.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid" : "ID da usuario",
     *      "dvnid" : "ID da duvida"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta"
     * }
     */
    public function delete() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->doubt_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->doubt_bo->validate_delete() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->doubt_bo->get_errors();
        } else {
            $data = $this->doubt_bo->get_data();
            $doubt = $this->duvida_model->find_by_dvnid($data['dvnid']);
            // verifica se houve falha na execucao do model
            if (is_null($doubt)) {
                $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                $response['response_message'] = "Houve um erro ao tentar carregar as informações da dúvida! Tente novamente.\n";
                $response['response_message'] .= "Se o erro persistir entre em contato com a equipe de suporte do Biblivirti AVAM.";
            } else {
                $admin = $this->grupo_model->find_group_admin($doubt->dvnidgr);

                // Verifica se o usuario logado nao eh admin do grupo e nao eh o usuario que adicionou a duvida
                if ($data['usnid'] != $admin->usnid && $data['usnid'] != $doubt->dvnidus) {
                    $response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                    $response['response_message'] = "Erro ao tentar excluir dúvida!\n";
                    $response['response_message'] .= "Somente o usuário que adicionou a dúvida ou o administrador do grupo têm permissões para removê-la!";
                } else {
                    // Atualiza o status da duvida para INATIVA (Exclui)
                    $dvnid = $this->duvida_model->update(['dvcstat' => DVCSTAT_INATIVO, 'dvnid' => $doubt->dvnid]);

                    // Carrega os dados para o envio do email de notificacao
                    $user = $this->usuario_model->find_by_usnid($doubt->dvnidus);
                    $group = $this->grupo_model->find_by_grnid($doubt->dvnidgr);
                    // Seta os dados para o envio do email de notificação de novo grupo
                    $from = EMAIL_SMTP_USER;
                    $to = $user->uscmail;
                    $subject = EMAIL_SUBJECT_DELETE_DOUBT;
                    $message = EMAIL_MESSAGE_DELETE_DOUBT;
                    $datas = [
                        EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                        EMAIL_KEY_USCNOME => (!is_null($user->uscnome)) ? $user->uscnome : $user->usclogn,
                        EMAIL_KEY_GRCNOME => $group->grcnome,
                        EMAIL_KEY_DVNID => $doubt->dvnid,
                        EMAIL_KEY_DVCTEXT => $doubt->dvctext,
                        EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                        EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                    ];

                    $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                    if ($this->biblivirti_email->send() === false) {
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_DELETE_DOUBT . "!\n";
                        $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                        $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                    } else {
                        $response['response_code'] = RESPONSE_CODE_OK;
                        $response['response_message'] = "Dúvida excluída com sucesso!";
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

}