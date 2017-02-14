<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 22/08/2016
 *
 * Controller para gerenciar acesso aos dados de <b>Grupos</b>
 */
class Group extends CI_Controller {

    /**
     * @var array
     *
     * Armazena os dados de resposta das requisicoes.
     */
    private $response;

    /**
     * Group constructor.
     */
    public function __construct() {
        parent::__construct();

        // Initing variables
        $this->response = [];

        // Loading models
        $this->load->model("grupo_model");
        $this->load->model("areainteresse_model");
        $this->load->model("usuario_model");

        // Loading libraries
        $this->load->library('business/group_bo');
        $this->load->library('input/biblivirti_input');
        $this->load->library('email/biblivirti_email');
        $this->load->library('media/biblivirti_media');
    }

    /**
     * @url: API/group/list
     * @param string JSON
     * @return JSON
     *
     * Metodo para listar todos os grupos de um determinado usuario.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid": "ID do usuario"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da requsicao",
     *      "response_message" : "Mensagem da requsicao",
     *      "response_data" : [
     *          {
     *              "grnid": "ID do grupo",
     *              "grcnome" : "Nome do grupo",
     *              "grcfoto" : "Caminho da foto do grupo",
     *              "grctipo" : "Tipo do grupo",
     *              "grdcadt" : "Data de cadastro do grupo",
     *              "areaofinterest" : {
     *                  "ainid" : "ID da area de interasse",
     *                  "aicdesc" : "Descricao da area de interesse"
     *              },
     *              "admin" : {
     *                  "usnid" : "ID do usuario",
     *                  "uscfbid" : "FacebookID do usuario",
     *                  "uscnome" : "Nome do usuario",
     *                  "uscmail" : "E-email do usuario",
     *                  "usclogn" : "Login do usuario",
     *                  "uscfoto" : "Caminho da foto do usuario",
     *                  "uscstat" : "Status do usuario",
     *                  "usdcadt" : "Data de cadastro do usuario"
     *              }
     *          },
     *      ]
     * }
     */
    public function list_all() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->group_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->group_bo->validate_list_all() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->group_bo->get_errors();
        } else {
            $data = $this->group_bo->get_data();
            $groups = $this->grupo_model->find_by_usnid($data['usnid']);
            // verifica se houve falha na execucao do model
            if (is_null($groups)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Nenhum grupo encontrado.";
            } else {
                foreach ($groups as $group) {
                    $group->areaofinterest = $this->areainteresse_model->find_by_ainid($group->grnidai);
                    unset($group->grnidai); // Remove o campo 'grnidai' do objeto de resposta
                    $group->admin = $this->grupo_model->find_group_admin($group->grnid);
                }
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Grupo(s) encontrado(s) com sucesso!";
                $this->response['response_data'] = $groups;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/group/get
     * @param int grnid
     * @return JSON
     *
     * Metodo para mostrar as informacoes de um determinado grupo.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "grnid" : "ID do grupo"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da requsicao",
     *      "response_message" : "Mensagem da requsicao",
     *      "response_data" : {
     *          "grnid": "ID do grupo",
     *          "grcnome" : "Nome do grupo",
     *          "grcfoto" : "Caminho da foto do grupo",
     *          "grctipo" : "Tipo do grupo",
     *          "grdcadt" : "Data de cadastro do grupo",
     *          "areaofinterest" : {
     *              "ainid" : "ID da area de interasse",
     *              "aicdesc" : "Descricao da area de interesse"
     *          }
     *      }
     * }
     */
    public function get() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->group_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->group_bo->validate_get() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->group_bo->get_errors();
        } else {
            $data = $this->group_bo->get_data();

            // Carrega as informacoes dados do grupo
            $group = $this->grupo_model->find_by_grnid($data['grnid']);
            $group->areaofinterest = $this->areainteresse_model->find_by_ainid($group->grnidai);
            unset($group->grnidai);

            $this->response['response_code'] = RESPONSE_CODE_OK;
            $this->response['response_message'] = "Grupo encontado com sucesso!";
            $this->response['response_data'] = $group;
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/group/add
     * @param string JSON
     * @return JSON
     *
     * Metodo para salva um novo grupo.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "grcnome": "Nome do grupo",
     *      "grcfoto": "Foto do grupo (Formato BASE64)",
     *      "grnidai": "ID da area de interesse do grupo",
     *      "usnid": "ID do usuario (admin do grupo)",
     *      "grctipo": "Tipo do grupo"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "request_code" : "Codigo da requsicao",
     *      "request_message" : "Mensagem da requsicao",
     *      "request_data" : {
     *          "grnid" : "ID do grupo"
     *      }
     * }
     */
    public function add() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->group_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->group_bo->validate_add() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->group_bo->get_errors();
        } else {
            $data = $this->group_bo->get_data();
            $user = $this->usuario_model->find_by_usnid($data['usnid']);

            // Verifica se a conta do usuario esta ativa
            if ($user->uscstat === USCSTAT_INATIVO) {
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Essa conta ainda não foi ativada!\n";
                $this->response['response_message'] .= "Acesse o link de confirmação no seu e-email para ativar sua conta.";
            } else {
                // Salva a imagem recebida no disco e devolve para o campo o caminho da imagem
                $data['grcfoto'] = $this->biblivirti_media->save_image($data['usnid'], $data['grcfoto']);

                $id = $this->grupo_model->save($data);
                // verifica se houve falha na execucao do model
                if ($id === 0) {
                    $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $this->response['response_message'] = "Houve um erro ao tentar salvar as informações do grupo! Tente novamente.\n";
                    $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                } else {


                    // Carrega os dados do usuario cadastrado
                    $user = $this->usuario_model->find_by_usnid($data['usnid']);
                    if (is_null($user)) {
                        $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                        $this->response['response_message'] = "Houve um erro ao tentar carregar as informações do usuário! Tente novamente.\n";
                        $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                    } else {
                        // Seta os dados para o envio do email de notificação de novo grupo
                        $from = EMAIL_SMTP_USER;
                        $to = $user->uscmail;
                        $subject = EMAIL_SUBJECT_NEW_GROUP;
                        $message = EMAIL_MESSAGE_NEW_GROUP;
                        $datas = [
                            EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                            EMAIL_KEY_USCNOME => (!is_null($user->uscnome)) ? $user->uscnome : $user->usclogn,
                            EMAIL_KEY_GRCNOME => $data['grcnome'],
                            EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                            EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                        ];

                        $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                        if ($this->biblivirti_email->send() === false) {
                            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                            $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_NEW_GROUP . "!\n";
                            $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                            $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                        } else {
                            $this->response['response_code'] = RESPONSE_CODE_OK;
                            $this->response['response_message'] = "Grupo cadastrado com sucesso!";
                            $this->response['response_data'] = ['grnid' => $id];
                        }
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/group/edit
     * @param string JSON
     * @return JSON
     *
     * Metodo para editar um grupo.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "grnid": "ID do grupo",
     *      "grcnome": "Nome do grupo",
     *      "grnidai": "ID da area de interesse do grupo",
     *      "usnid": "ID do usuario (admin do grupo)",
     *      "grctipo": "Tipo do grupo"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da requsicao",
     *      "response_message" : "Mensagem da requsicao",
     *      "response_data" : {
     *          "grnid" : "ID do grupo"
     *      }
     * }
     */
    public function edit() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->group_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->group_bo->validate_edit() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->group_bo->get_errors();
        } else {
            $data = $this->group_bo->get_data();
            $group = $this->grupo_model->find_by_grnid($data['grnid']);

            // verifica se houve falha na execucao do model
            if (is_null($group)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Nenhum grupo encontrado.";
            } else {
                $group->admin = $this->grupo_model->find_group_admin($group->grnid);
                if ($group->admin->usnid != $data['usnid']) {
                    $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                    $this->response['response_message'] = "Erro ao tentar editar o grupo!\n";
                    $this->response['response_message'] .= "Somente o administrador tem permissão para editá-lo!";
                } else {

                    // Atualiza os dados do grupo
                    $this->grupo_model->update($data);

                    // Seta os dados para o envio do email de notificação de novo grupo
                    $from = EMAIL_SMTP_USER;
                    $to = $group->admin->uscmail;
                    $subject = EMAIL_SUBJECT_EDIT_GROUP;
                    $message = EMAIL_MESSAGE_EDIT_GROUP;
                    $datas = [
                        EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                        EMAIL_KEY_USCNOME => (!is_null($group->admin->uscnome)) ? $group->admin->uscnome : $group->admin->usclogn,
                        EMAIL_KEY_GRCNOME => $group->grcnome,
                        EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                        EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                    ];

                    $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                    if ($this->biblivirti_email->send() === false) {
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_EDIT_GROUP . "!\n";
                        $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                        $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                    } else {
                        $this->response['response_message'] = "Grupo atualizado com sucesso!";
                        $this->response['response_data'] = ['grnid' => $data['grnid']];
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }


    /**
     * @url: API/group/delete
     * @param int grnid
     * @param int usnid
     * @return JSON
     *
     * Metodo para excluir um grupo.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "grnid" : "ID do grupo"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "request_code" : "Codigo da requsicao",
     *      "request_message" : "Mensagem da requsicao",
     * }
     */
    public function delete() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->group_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->group_bo->validate_delete() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->group_bo->get_errors();
        } else {
            $data = $this->group_bo->get_data();
            // Verifica o grupo foi encontrado
            $group = $this->grupo_model->find_by_grnid($data['grnid']);
            // Verifica se o usuario eh administrador do grupo
            // verifica se houve falha na execucao do model
            if (is_null($group)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Nenhum grupo encontrado.";
            } else {
                $group->admin = $this->grupo_model->find_group_admin($group->grnid);
                if ($group->admin->usnid != $data['usnid']) {
                    $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                    $this->response['response_message'] = "Erro ao tentar excluir o grupo!\n";
                    $this->response['response_message'] .= "Somente o administrador tem permissão para excluí-lo!";
                } else {
                    if (!$this->grupo_model->delete($data['grnid'])) { // Verifica se o grupo foi excluido com sucesso!
                        $this->response['response_code'] = RESPONSE_CODE_OK;
                        $this->response['response_message'] = "Houve um erro ao tentar excluir as informações do grupo!\nTente novamente!";
                        $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti.";
                    } else {
                        // Seta os dados para o envio do email de notificação de novo grupo
                        $from = EMAIL_SMTP_USER;
                        $to = $group->admin->uscmail;
                        $subject = EMAIL_SUBJECT_DELETE_GROUP;
                        $message = EMAIL_MESSAGE_DELETE_GROUP;
                        $datas = [
                            EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                            EMAIL_KEY_USCNOME => (!is_null($group->admin->uscnome)) ? $group->admin->uscnome : $group->admin->usclogn,
                            EMAIL_KEY_GRCNOME => $group->grcnome,
                            EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                            EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                        ];

                        $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                        if ($this->biblivirti_email->send() === false) {
                            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                            $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_DELETE_GROUP . "!\n";
                            $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                            $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                        } else {
                            $this->response['response_code'] = RESPONSE_CODE_OK;
                            $this->response['response_message'] = "Grupo excluído com sucesso!";
                        }
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/group/info
     * @param int grnid
     * @return JSON
     *
     * Metodo para mostrar as informacoes de um grupo.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "grnid" : "ID do grupo"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da requsicao",
     *      "response_message" : "Mensagem da requsicao",
     *      "response_data" : {
     *          "grnid": "ID do grupo",
     *              "grcnome" : "Nome do grupo",
     *              "grcfoto" : "Caminho da foto do grupo",
     *              "grctipo" : "Tipo do grupo",
     *              "grdcadt" : "Data de cadastro do grupo",
     *              "areaofinterest" : {
     *                  "ainid" : "ID da area de interasse",
     *                  "aicdesc" : "Descricao da area de interesse"
     *              },
     *              "admin" : {
     *                  "usnid" : "ID do usuario",
     *                  "uscfbid" : "FacebookID do usuario",
     *                  "uscnome" : "Nome do usuario",
     *                  "uscmail" : "E-email do usuario",
     *                  "usclogn" : "Login do usuario",
     *                  "uscfoto" : "Caminho da foto do usuario",
     *                  "uscstat" : "Status do usuario",
     *                  "usdcadt" : "Data de cadastro do usuario"
     *              },
     *              "users" : [
     *                  {
     *                      "usnid" : "ID do usuario",
     *                      "uscfbid" : "FacebookID do usuario",
     *                      "uscnome" : "Nome do usuario",
     *                      "uscmail" : "E-email do usuario",
     *                      "usclogn" : "Login do usuario",
     *                      "uscfoto" : "Caminho da foto do usuario",
     *                      "uscstat" : "Status do usuario",
     *                      "usdcadt" : "Data de cadastro do usuario"
     *                  },
     *              ]
     *      }
     * }
     */
    public function info() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->group_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->group_bo->validate_info() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->group_bo->get_errors();
        } else {
            $data = $this->group_bo->get_data();

            // Carrega as informacoes dados do grupo
            $group = $this->grupo_model->find_by_grnid($data['grnid']);
            $group->admin = $this->grupo_model->find_group_admin($group->grnid);
            $group->users = $this->grupo_model->find_group_users($group->grnid);

            $this->response['response_code'] = RESPONSE_CODE_OK;
            $this->response['response_message'] = "Grupo encontado com sucesso!";
            $this->response['response_data'] = $group;
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/group/search
     * @return JSON
     *
     * Metodo para buscar grupos.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "reference" : "Referencia para a pesquisa"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta",
     *      "response_data" : [
     *          {
     *              "grnid": "ID do grupo",
     *              "grcnome" : "Nome do grupo",
     *              "grcfoto" : "Caminho da foto do grupo",
     *              "grctipo" : "Tipo do grupo",
     *              "grdcadt" : "Data de cadastro do grupo",
     *              "areaofinterest" : {
     *                  "ainid" : "ID da area de interasse",
     *                  "aicdesc" : "Descricao da area de interesse"
     *              },
     *              "admin" : {
     *                  "usnid" : "ID do usuario",
     *                  "uscfbid" : "FacebookID do usuario",
     *                  "uscnome" : "Nome do usuario",
     *                  "uscmail" : "E-email do usuario",
     *                  "usclogn" : "Login do usuario",
     *                  "uscfoto" : "Caminho da foto do usuario",
     *                  "uscstat" : "Status do usuario",
     *                  "usdcadt" : "Data de cadastro do usuario"
     *              },
     *          },
     *      ]
     * }
     */
    public function search() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->group_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->group_bo->validate_search() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e / ou inválidos . VERIFIQUE!";
            $this->response['response_errors'] = $this->group_bo->get_errors();
        } else {
            $data = $this->group_bo->get_data();
            $groups = $this->grupo_model->find_by_grcnome($data['reference']);
            if (is_null($groups)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Nenhum grupo encontrado!";
            } else {
                foreach ($groups as $group) {
                    $group->areaofinterest = $this->areainteresse_model->find_by_ainid($group->grnidai);
                    $group->admin = $this->grupo_model->find_group_admin($group->grnid);
                    unset($group->grnidai); // Remove o campo GRNIDAI do objetos de resposta
                }
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Senha alterada com sucesso!";
                $this->response['response_data'] = $groups;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/group/subscribe
     * @return JSON
     *
     * Metodo para adicionar um usuario em um grupo.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "grnid": "ID do grupo",
     *      "usnid": "ID od usuario"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta"
     * }
     */
    public function subscribe() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->group_bo->set_data($data);

        // Verifica se os dados nao foram validados
        if ($this->group_bo->validate_subscribe() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e / ou inválidos . VERIFIQUE!";
            $this->response['response_errors'] = $this->group_bo->get_errors();
        } else {
            $data = $this->group_bo->get_data();
            $users = $this->grupo_model->find_group_users($data['grnid'], 0, 1000);
            $is_member = false;

            if (!is_null($users)) { // Percorre a lista de usuarios do grupo buscando o usuario a ser adicionado
                foreach ($users as $user) {
                    if ($user->usnid == $data['usnid']) {
                        $is_member = true;
                        break;
                    }
                }
            }

            if ($is_member === true) { // Verifica se o usuario ja eh membro do grupo
                $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                $this->response['response_message'] = "Este usuário já um membro do grupo!";
            } else {
                if ($this->grupo_model->add_member($data['grnid'], $data['usnid']) === false) { // Verifica se o usuario foi adicionado do grupo
                    $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $this->response['response_message'] = "Houve um erro ao tentar adicionar usuário no grupo. Tente novamente!\n";
                    $this->response['response_message'] .= "Se o erro persistir entre em contato com a equipe de suporte do Biblivirti AVAM.";
                } else {
                    // carrega os dados para o envio do email
                    $group = $this->grupo_model->find_by_grnid($data['grnid']);
                    $user = $this->usuario_model->find_by_usnid($data['usnid']);
                    // Seta os dados para o envio do email de notificação de novo grupo
                    $from = EMAIL_SMTP_USER;
                    $to = $user->uscmail;
                    $subject = EMAIL_SUBJECT_NEW_MEMBER;
                    $message = EMAIL_MESSAGE_NEW_MEMBER;
                    $datas = [
                        EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                        EMAIL_KEY_USCNOME => (!is_null($user->uscnome)) ? $user->uscnome : $user->usclogn,
                        EMAIL_KEY_GRCNOME => $group->grcnome,
                        EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                        EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                    ];

                    $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                    if ($this->biblivirti_email->send() === false) {
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_NEW_MEMBER . "!\n";
                        $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                        $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                    } else {
                        $this->response['response_code'] = RESPONSE_CODE_OK;
                        $this->response['response_message'] = "Usuário adicionado com sucesso!";
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/group/unsubscribe
     * @return JSON
     *
     * Metodo para remover um usuario em um grupo.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "grnid": "ID do grupo",
     *      "usnid": "ID do usuario admin do grupo"
     *      "usnid2": "ID do usuario a ser removido"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta"
     * }
     */
    public function unsubscribe() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->group_bo->set_data($data);

        // Verifica se os dados nao foram validados
        if ($this->group_bo->validate_unsubscribe() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e / ou inválidos . VERIFIQUE!";
            $this->response['response_errors'] = $this->group_bo->get_errors();
        } else {
            $data = $this->group_bo->get_data();
            $admin = $this->grupo_model->find_group_admin($data['grnid']);

            // Verifica se o usuario logado eh admin do grupo
            if ($admin->usnid != $data['usnid']) {
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Erro ao tentar remover usuário do grupo!\n";
                $this->response['response_message'] .= "Somente o administrador do grupo ou o próprio usuário têm permissão para removê-lo.";
            } else if ($admin->usnid == $data['usnid2']) { // Verifica se o usuario a ser removido eh o admin do grupo
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Houve um erro ao tentar remover usuário no grupo. Tente novamente!\n";
                $this->response['response_message'] .= "O administrador do grupo não pode ser removido.";
            } else {
                $users = $this->grupo_model->find_group_users($data['grnid']);

                $is_member = false;
                if (!is_null($users)) { // Percorre a lista de usuarios do grupo buscando o usuario a ser excluido
                    foreach ($users as $user) {
                        if ($user->usnid == $data['usnid2']) {
                            $is_member = true;
                            break;
                        }
                    }
                }

                if ($is_member === false) { // Verifica se o usuario a ser exlcuido nao eh membro do grupo
                    $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                    $this->response['response_message'] = "Este usuário não é um membro do grupo!";
                } else {
                    // Verifica se o usuario foi removido do grupo
                    if ($this->grupo_model->remove_member($data['grnid'], $data['usnid2']) === false) {
                        $this->response['response_message'] = "Houve um erro ao tentar remover usuário no grupo. Tente novamente!\n";
                        $this->response['response_message'] .= "Se o erro persistir entre em contato com a equipe de suporte do Biblivirti AVAM.";
                    } else {
                        // carrega os dados para o envio do email
                        $group = $this->grupo_model->find_by_grnid($data['grnid']);
                        $user = $this->usuario_model->find_by_usnid($data['usnid2']);
                        // Seta os dados para o envio do email de notificação de novo grupo
                        $from = EMAIL_SMTP_USER;
                        $to = $user->uscmail;
                        $subject = EMAIL_SUBJECT_DELETE_MEMBER;
                        $message = EMAIL_MESSAGE_DELETE_MEMBER;
                        $datas = [
                            EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                            EMAIL_KEY_USCNOME => (!is_null($user->uscnome)) ? $user->uscnome : $user->usclogn,
                            EMAIL_KEY_GRCNOME => $group->grcnome,
                            EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                            EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                        ];

                        $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                        if ($this->biblivirti_email->send() === false) {
                            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                            $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_DELETE_MEMBER . "!\n";
                            $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                            $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                        } else {
                            $this->response['response_code'] = RESPONSE_CODE_OK;
                            $this->response['response_message'] = "Usuário removido com sucesso!";
                        }
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

}