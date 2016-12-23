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
     *      "grnid" : "ID do grupo"
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
            $users = $this->grupo_model->find_group_users($data['grnid']);

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
                $doubts = $this->duvida_model->find_by_dvnidgr($data['grnid']);

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
            } else if (strval($doubt->dvcstat) == DVCSTAT_INATIVO) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "Dúvida não encontrada!";
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

    /**
     * @url: API/doubt/share
     * @param string JSON
     * @return JSON
     *
     * Metodo para compartilhar uma duvida.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid" : "ID da usuario",
     *      "grnid" : "ID do grupo",
     *      "dvnid" : "ID da duvida"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta"
     *      "response_data" : {
     *          "dvnid"  : "ID da duvida"
     *      }
     * }
     */
    public function share() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->doubt_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->doubt_bo->validate_share() === FALSE) {
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
            } else if (strval($doubt->dvcstat) == DVCSTAT_INATIVO) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "Dúvida não encontrada!";
            } else {
                $users = $this->grupo_model->find_group_users($data['grnid']);
                $is_member = false;

                foreach ($users as $user) { // Percorre a lista de usuarios grupo informado
                    // Verifica se o usuario logado nao eh membro do grupo no qual a duvida sera compartilhada
                    if ($user->usnid == $data['usnid']) {
                        $is_member = true;
                        break;
                    }
                }

                // Verifica se o usuario logado nao eh membro do grupo no qual a duvida sera compartilhada
                if ($is_member == false) {
                    $response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                    $response['response_message'] = "Erro ao tentar compartilhar a dúvida!\n";
                    $response['response_message'] .= "Somente membros do grupo têm permissão para adicionar dúvidas!";
                } else {
                    $user = $this->usuario_model->find_by_usnid($data['usnid']);
                    $group = $this->grupo_model->find_by_grnid($data['grnid']);
                    $contents = $this->duvida_model->find_doubt_contents($doubt->dvnid);

                    $data = [];
                    $data['dvnidgr'] = $group->grnid;
                    $data['dvnidus'] = $user->usnid;
                    $data['dvctext'] = $doubt->dvctext;
                    $data['dvcanex'] = $doubt->dvcanex;
                    $data['dvlanon'] = $doubt->dvlanon;
                    foreach ($contents as $content) {
                        $data['contents'][] = ['conid' => $content->conid];
                    }
                    unset($data['usnid']); // Remove o campo ID DO USUARIO do objeto a ser salvo
                    unset($data['grnid']); // Remove o campo ID DO GRUPO do objeto a ser salvo
                    unset($data['dvnid']); // Remove o campo ID DA DUVIDA do objeto a ser salvo

                    $dvnid = $this->duvida_model->save($data);

                    if (is_null($dvnid)) { // Verifica se a duvida foi salva com sucesso
                        $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $response['response_message'] = "Houve um erro ao tentar compartilhar a dúvida! Tente novamente.\n";
                        $response['response_message'] .= "Se o erro persistir entre em contato com a equipe de suporte do Biblivirti AVAM.";
                    } else {
                        // Seta os dados para o envio do email de notificação de novo grupo
                        $from = EMAIL_SMTP_USER;
                        $to = $user->uscmail;
                        $subject = EMAIL_SUBJECT_SHARE_DOUBT;
                        $message = EMAIL_MESSAGE_SHARE_DOUBT;
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
                            $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_SHARE_DOUBT . "!\n";
                            $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                            $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                        } else {
                            $response['response_code'] = RESPONSE_CODE_OK;
                            $response['response_message'] = "Dúvida compartilhada com sucesso!";
                            $response['response_data'] = ['dvnid' => $dvnid];
                        }
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/doubt/details
     * @param string JSON
     * @return JSON
     *
     * Metodo que mostra os detalhes de uma duvida.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid" : "ID da usuario",
     *      "dvnid" : "ID da duvida"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta"
     *      "response_data" : {
     *          "dvnid"  : "ID da duvida",
     *          "dvnidgr"  : "ID do grupo da duvida",
     *          "dvctext"  : "Texto da duvida",
     *          "dvcanex"  : "Anexo da duvida",
     *          "dvcstat"  : "Status da duvida",
     *          "dvlanon"  : "Define se a duvida eh anonima ounao",
     *          "dvdcadt"  : "Data de Cadastro",
     *          "dvdaldt"  : "Data de Aletracao",
     *          "dvnatddr"  : "Qtd. de respostas",
     *          "user"  : {
     *              "usnid" : "ID do usuario",
     *              "uscfbid" : "FacebookID do usuario",
     *              "uscnome" : "Nome do usuario",
     *              "uscmail" : "E-email do usuario",
     *              "usclogn" : "Login do usuario",
     *              "uscfoto" : "Caminho da foto do usuario",
     *              "uscstat" : "Status do usuario",
     *              "usdcadt" : "Data de cadastro do usuario",
     *          },
     *          "contents" : [
     *              {
     *                  "conid" : "ID do conteudo",
     *                  "cocdesc" : "Descricao do conteudo",
     *                  "codcadt" : "Data de Cadastro",
     *                  "codaldt" : "Data de Alteracao"
     *              },
     *          ]
     *      }
     * }
     */
    public function details() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->doubt_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->doubt_bo->validate_details() === FALSE) {
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
            } else if (strval($doubt->dvcstat) == DVCSTAT_INATIVO) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "Dúvida não encontrada!";
            } else {
                $users = $this->grupo_model->find_group_users($doubt->dvnidgr);
                $is_member = false;

                foreach ($users as $user) { // Percorre a lista de usuarios grupo informado
                    // Verifica se o usuario logado nao eh membro do grupo no qual a duvida foi postada
                    if ($user->usnid == $data['usnid']) {
                        $is_member = true;
                        break;
                    }
                }

                // Verifica se o usuario logado nao eh membro do grupo no qual a duvida foi postada
                if ($is_member == false) {
                    $response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                    $response['response_message'] = "Erro ao tentar carregas as informações da dúvida!\n";
                    $response['response_message'] .= "Somente membros do grupo têm permissão para visualizá-las!";
                } else {
                    $doubt->dvnqtddr = count($this->duvidaresposta_model->find_by_drniddv($doubt->dvnid)); // Carrega a qtd. de respostas da duvida
                    $doubt->user = $this->usuario_model->find_by_usnid($doubt->dvnidus); // Carrega o usuario da duvida
                    $doubt->contents = $this->duvida_model->find_doubt_contents($doubt->dvnid); // Carrega os conteudos relacionados com a duvida
                    unset($doubt->dvnidus); // Remove o campo "ID DO USUARIO" do objeto de resposta
                    unset($doubt->user->uscsenh); // Remove a senha do usuario do objeto de resposta

                    $response['response_code'] = RESPONSE_CODE_OK;
                    $response['response_message'] = "Dúvida carregada com sucesso!";
                    $response['response_data'] = $doubt;
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }


    /**
     * @url: API/doubt/search
     * @param string JSON
     * @return JSON
     *
     * Metodo para buscar duvidas.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usndi" : "ID do usuario",
     *      "grnid" : "ID do grupo",
     *      "reference" : "Referencia da pesquisa"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta",
     *      "response_data" : [
     *          {
     *              "dvnid"  : "ID da duvida",
     *              "dvnidgr"  : "ID do grupo da duvida",
     *              "dvctext"  : "Texto da duvida",
     *              "dvcanex"  : "Anexo da duvida",
     *              "dvcstat"  : "Status da duvida",
     *              "dvlanon"  : "Define se a duvida eh anonima ounao",
     *              "dvdcadt"  : "Data de Cadastro",
     *              "dvdaldt"  : "Data de Aletracao",
     *              "dvnatddr"  : "Qtd. de respostas",
     *              "user"  : {
     *                  "usnid" : "ID do usuario",
     *                  "uscfbid" : "FacebookID do usuario",
     *                  "uscnome" : "Nome do usuario",
     *                  "uscmail" : "E-email do usuario",
     *                  "usclogn" : "Login do usuario",
     *                  "uscfoto" : "Caminho da foto do usuario",
     *                  "uscstat" : "Status do usuario",
     *                  "usdcadt" : "Data de cadastro do usuario",
     *              },
     *              "contents" : [
     *                  {
     *                      "conid" : "ID do conteudo",
     *                      "cocdesc" : "Descricao do conteudo",
     *                      "codcadt" : "Data de Cadastro",
     *                      "codaldt" : "Data de Alteracao"
     *                  },
     *              ]
     *          },
     *      ]
     * }
     */
    public function search() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->doubt_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->doubt_bo->validate_search() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->doubt_bo->get_errors();
        } else {
            $data = $this->doubt_bo->get_data();

            $users = $this->grupo_model->find_group_users($data['grnid']);
            $is_member = false;

            foreach ($users as $user) { // Percorre a lista de usuarios grupo informado
                // Verifica se o usuario logado nao eh membro do grupo em questao
                if ($user->usnid == $data['usnid']) {
                    $is_member = true;
                    break;
                }
            }

            // Verifica se o usuario logado nao eh membro do grupo em questao
            if ($is_member == false) {
                $response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $response['response_message'] = "Erro ao tentar carregas as informações de dúvidas!\n";
                $response['response_message'] .= "Somente membros do grupo têm permissão para visualizá-las!";
            } else {
                $doubts = $this->duvida_model->find_by_dvctext($data['reference']);
                if (is_null($doubts)) {
                    $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $response['response_message'] = "Nenhuma dúvida encontrada.";
                } else {
                    foreach ($doubts as $doubt) { // Percorre todas as duvidas encontradas
                        $doubt->dvnqtddr = count($this->duvidaresposta_model->find_by_drniddv($doubt->dvnid)); // Carrega a qtd. de respostas da duvida
                        $doubt->user = $this->usuario_model->find_by_usnid($doubt->dvnidus); // Carrega o usuario da duvida
                        $doubt->contents = $this->duvida_model->find_doubt_contents($doubt->dvnid); // Carrega os conteudos relacionados com a duvida
                        unset($doubt->dvnidus); // Remove o campo "ID DO USUARIO" do objeto de resposta
                        unset($doubt->user->uscsenh); // Remove a senha do usuario do objeto de resposta
                    }

                    $response['response_code'] = RESPONSE_CODE_OK;
                    $response['response_message'] = "Dívida(s) encontrada(s) com sucesso!";
                    $response['response_data'] = $doubts;
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
}