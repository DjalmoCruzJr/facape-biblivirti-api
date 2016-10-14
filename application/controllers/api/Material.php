<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 01/09/2016
 *
 * Controller da API para gerenciar o acesso aos dados de <b>Materiais</b>.
 */
class Material extends CI_Controller {

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
        $this->load->model("comentario_model");
        $this->load->model("historicoacesso_model");

        // Loading libraries
        $this->load->library('business/material_bo');
        $this->load->library('input/biblivirti_input');
        $this->load->library('email/biblivirti_email');
    }

    /**
     * @url: API/material/list
     * @param int grnid
     * @return JSON
     *
     * Metodo para buscar todos os materiais relacionados com um determinado grupo.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "grnid" : "ID do grupo",
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : [
     *         {    "manid" : "ID do material",
     *              "macdesc" : "Descricao do usuario",
     *              "mactipo" : "Tipo de material",
     *              "macurl" : "URL do material",
     *              "macnivl" : "Nivel do material",
     *              "macstat" : "Status do material",
     *              "madcadt" : "Data de cadastro do material",
     *              "madaldt" : "Data de atualizacao do material"
     *              "manqtdce" : "Qtd. de comentario do material"
     *              "manqtdha" : "Qtd. de vizualizacoes do material"
     *          },
     *      ]
     * }
     */
    public function list_all() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->material_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->material_bo->validate_list_all() === false) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->material_bo->get_errors();
        } else {
            $data = $this->material_bo->get_data();
            $materials = $this->material_model->find_by_manidgr($data['grnid']);
            // Verifica se houve falha na execucao do model
            if (is_null($materials)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Nenhum material encontrado.";
            } else {
                foreach ($materials as $material) {
                    $material->manqtdce = count($this->comentario_model->find_by_cenidma($material->manid));
                    $material->manqtdha = $this->historicoacesso_model->count_by_hanidma($material->manid);
                }
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Material(ais) encontrado(s) com sucesso!";
                $this->response['response_data'] = $materials;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/material/add
     * @param string JSON
     * @return JSON
     *
     * Metodo para cadastrar um novo material.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid" : "ID do usuario",
     *      "grnid" : "ID do grupo",
     *      "macdesc" : "Descricao do material",
     *      "mactipo" : "Tipo do material",
     *      "malanex" : "Define se o material eh um anexo",
     *      "macurl" : "URL do material (ou nome do arquivo anexo)",
     *      "macnivl" : "Nivel do material (Somente se mactipo = S - SIMULADO)",
     *      "macstat" : "Status do material",
     *      "contents" : [
     *          {
     *              "conid" : "ID do conteudo relacionado"
     *          },
     *      ],
     *      "questions" : [ (array - Somente se mactipo = S - SIMULADO)
     *          {
     *              "qenid" : "ID da questao"
     *          },
     *      ]
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : {
     *          "manid" : "ID do material",
     *      }
     * }
     */
    public function add() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->material_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->material_bo->validate_add() === false) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->material_bo->get_errors();
        } else {
            $data = $this->material_bo->get_data();
            $admin = $this->grupo_model->find_group_admin($data['manidgr']);

            // Verifica se o usuario nao eh administrador do grupo
            if ($admin->usnid != $data['usnid']) {
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Houve um erro ao tentar cadastrar o material!\n";
                $this->response['response_message'] .= "Somente o administrador do grupo tem permissão para adicionar um material.";
            } else {
                unset($data['usnid']); // Remove o ID do usuario dos dados a serem persistidos.
                $manid = $this->material_model->save($data);

                // Verifica se houve falha na execucao do model
                if (is_null($manid)) {
                    $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $this->response['response_message'] = "Houve um erro ao tentar cadastrar o material! Tente novamente.\n";
                    $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                } else {
                    // Carrega os dados do administrador do grupo
                    $group = $this->grupo_model->find_by_grnid($data['manidgr']);
                    // Seta os dados para o envio do email de notificação de novo grupo
                    $from = EMAIL_SMTP_USER;
                    $to = $admin->uscmail;
                    $subject = EMAIL_SUBJECT_NEW_MATERIAL;
                    $message = EMAIL_MESSAGE_NEW_MATERIAL;
                    $datas = [
                        EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                        EMAIL_KEY_USCNOME => (!is_null($admin->uscnome)) ? $admin->uscnome : $admin->usclogn,
                        EMAIL_KEY_MACDESC => $data['macdesc'],
                        EMAIL_KEY_GRCNOME => $group->grcnome,
                        EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                        EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                    ];

                    $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                    if ($this->biblivirti_email->send() === false) {
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_NEW_MATERIAL . "!\n";
                        $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                        $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                    } else {
                        $this->response['response_code'] = RESPONSE_CODE_OK;
                        $this->response['response_message'] = "Material cadastrado com sucesso!";
                        $this->response['response_data'] = $manid;
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/material/edit
     * @param string JSON
     * @return JSON
     *
     * Metodo para atualizar um material.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid" : "ID do usuario",
     *      "manid" : "ID do material",
     *      "macdesc" : "Descricao do material",
     *      "mactipo" : "Tipo do material",
     *      "macurl" : "URL do material (ou nome do arquivo anexo)",
     *      "macnivl" : "Nivel do material (Somente se mactipo = S - SIMULADO)",
     *      "macstat" : "Status do material",
     *      "contents" : [
     *          {
     *              "conid" : "ID do conteudo relacionado"
     *          },
     *      ],
     *      "questions" : [ (array - Somente se mactipo = S - SIMULADO)
     *          {
     *              "qenid" : "ID da questao"
     *          },
     *      ]
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : {
     *          "manid" : "ID do material",
     *      }
     * }
     */
    public function edit() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->material_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->material_bo->validate_edit() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->material_bo->get_errors();
        } else {
            $data = $this->material_bo->get_data();
            $material = $this->material_model->find_by_manid($data['manid']);
            // verifica se houve falha na execucao do model
            if (is_null($material)) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "Nenhum material encontrado.";
            } else {
                $admin = $this->grupo_model->find_group_admin($material->manidgr);
                if ($admin->usnid != $data['usnid']) {
                    $response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                    $response['response_message'] = "Erro ao tentar editar o material!\n";
                    $response['response_message'] .= "Somente o administrador tem permissão para editá-lo!";
                } else {
                    unset($data['usnid']); // Remove o ID do usuairo do objeto a ser gravado.
                    $this->material_model->update($data);

                    // Seta os dados para o envio do email de notificação de novo grupo
                    $from = EMAIL_SMTP_USER;
                    $to = $admin->uscmail;
                    $subject = EMAIL_SUBJECT_EDIT_MATERIAL;
                    $message = EMAIL_MESSAGE_EDIT_MATERIAL;
                    $datas = [
                        EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                        EMAIL_KEY_USCNOME => (!is_null($admin->uscnome)) ? $admin->uscnome : $admin->usclogn,
                        EMAIL_KEY_MACDESC => $data['macdesc'],
                        EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                        EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                    ];

                    $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                    if ($this->biblivirti_email->send() === false) {
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_EDIT_MATERIAL . "!\n";
                        $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                        $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                    } else {
                        $response['response_message'] = "Material atualizado com sucesso!";
                        $response['response_data'] = ['manid' => $data['manid']];
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }


    /**
     * @url: API/material/delete
     * @param string JSON
     * @return JSON
     *
     * Metodo para deletar um material.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid" : "ID do usuario",
     *      "manid" : "ID do material"
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
        $this->material_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->material_bo->validate_delete() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->material_bo->get_errors();
        } else {
            $data = $this->material_bo->get_data();

            $material = $this->material_model->find_by_manid($data['manid']);
            $admin = $this->grupo_model->find_group_admin($material->manidgr);
            if ($admin->usnid != $data['usnid']) {
                $response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $response['response_message'] = "Erro ao tentar excluir o material!\n";
                $response['response_message'] .= "Somente o administrador tem permissão para excluí-lo!";
            } else {
                if (!$this->material_model->delete($data['manid'])) {
                    $response['response_code'] = RESPONSE_CODE_OK;
                    $response['response_message'] = "Houve um erro ao tentar excluir as informações do material!\nTente novamente!";
                    $response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti.";
                } else {
                    // Seta os dados para o envio do email de notificação de novo grupo
                    $from = EMAIL_SMTP_USER;
                    $to = $admin->uscmail;
                    $subject = EMAIL_SUBJECT_DELETE_MATERIAL;
                    $message = EMAIL_MESSAGE_DELETE_MATERIAL;
                    $datas = [
                        EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                        EMAIL_KEY_USCNOME => (!is_null($admin->uscnome)) ? $admin->uscnome : $admin->usclogn,
                        EMAIL_KEY_MACDESC => $data['macdesc'],
                        EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                        EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                    ];

                    $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                    if ($this->biblivirti_email->send() === false) {
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_DELETE_MATERIAL . "!\n";
                        $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                        $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                    } else {
                        $response['response_code'] = RESPONSE_CODE_OK;
                        $response['response_message'] = "Material excluído com sucesso!";
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/material/search
     * @param string JSON
     * @return JSON
     *
     * Metodo para buscar materiais.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "macdesc" : "Descricao do material",
     *      "mactipo" : "Tipo do material"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "request_code" : "Codigo da requsicao",
     *      "request_message" : "Mensagem da requsicao",
     *      "request_data" : [
     *          {
     *              "manid" : "ID do material",
     *              "macdesc" : "Descricao do usuario",
     *              "mactipo" : "Tipo de material",
     *              "macurl" : "URL do material",
     *              "macnivl" : "Nivel do material",
     *              "macstat" : "Status do material",
     *              "madcadt" : "Data de cadastro do material",
     *              "madaldt" : "Data de atualizacao do material"
     *              "manqtdce" : "Qtd. de comentarios do material"
     *              "manqtdha" : "Qtd. de vizualizacoes do material"
     *          },
     *      ]
     * }
     */
    public function search() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->material_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->material_bo->validate_search() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->material_bo->get_errors();
        } else {
            $data = $this->material_bo->get_data();

            $materials = $this->material_model->find_by_macdesc_and_mactipo($data['macdesc'], $data['mactipo']);
            if (is_null($materials)) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "Nenhum material encontrado.";
            } else {
                $response['response_code'] = RESPONSE_CODE_OK;
                $response['response_message'] = "Material(ais) encontrado(s) com sucesso!";
                $response['response_data'] = $materials;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/material/email
     * @param string JSON
     * @return JSON
     *
     * Metodo para buscar materiais.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "uscmail" : "E-mail do usuario emitente",
     *      "uscmail2" : "E-mail do usuario destinatario",
     *      "manid" : "ID do material"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "request_code" : "Codigo da requsicao",
     *      "request_message" : "Mensagem da requsicao"
     * }
     */
    public function email() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->material_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->material_bo->validate_email() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->material_bo->get_errors();
        } else {
            $data = $this->material_bo->get_data();
            $emitente = $this->usuario_model->find_by_usnid($data['usnid']);
            $destinatario = $this->usuario_model->find_by_uscmail($data['uscmail'])[0];
            $material = $this->material_model->find_by_manid($data['manid']);

            // Seta os dados para o envio do email de notificação de novo grupo
            $this->load->library('helper/material_helper');
            $from = EMAIL_SMTP_USER;
            $to = $destinatario->uscmail;
            $subject = EMAIL_SUBJECT_EMAIL_MATERIAL;
            $message = EMAIL_MESSAGE_EMAIL_MATERIAL;
            $datas = [
                EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                EMAIL_KEY_USCNOME => (!is_null($destinatario->uscnome)) ? $destinatario->uscnome : $destinatario->usclogn,
                EMAIL_KEY_EMITENTE => (!is_null($emitente->uscnome)) ? $emitente->uscnome : $emitente->usclogn,
                EMAIL_KEY_MACTIPO => $this->material_helper->get_description($material->mactipo),
                EMAIL_KEY_MACDESC => $material->macdesc,
                EMAIL_KEY_MATERIAL_LINK => base_url('API/material/details/') . base64_encode($material->manid),
                EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
            ];

            $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

            if ($this->biblivirti_email->send() === false) {
                $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_EMAIL_MATERIAL . "!\n";
                $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                $this->response['response_errors'] = $this->biblivirti_email->get_errros();
            } else {
                $response['response_code'] = RESPONSE_CODE_OK;
                $response['response_message'] = "E-mail enviado com sucesso!";
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/material/share
     * @param string JSON
     * @return JSON
     *
     * Metodo compartilhar um material de um grupo com outro.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "manid" : "ID do material",
     *      "grnid" : "ID do grupo",
     *      "usnid" : "ID do usuario"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "request_code" : "Codigo da requsicao",
     *      "request_message" : "Mensagem da requsicao"
     * }
     */
    public function share() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->material_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->material_bo->validate_share() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->material_bo->get_errors();
        } else {
            $data = $this->material_bo->get_data();
            $admin = $this->grupo_model->find_group_admin($data['grnid']);

            if ($admin->usnid != $data['usnid']) { // Verifica se o usuario não eh o administrador do grupo
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Erro ao tentar adicionar material no grupo!\n";
                $this->response['response_message'] .= "Somente o administrador do grupo pode adicionar novos materiais.";
            } else {
                $group = $this->grupo_model->find_by_grnid($data['grnid']);
                $material = $this->material_model->find_by_manid($data['manid']);
                $contents = $this->material_model->find_material_contents($material->manid);
                $questions = null;

                // Seta os dados para compartilhar o material no grupo informado
                $data = [];
                $data['manidgr'] = $group->grnid;
                $data['macdesc'] = $material->macdesc;
                $data['mactipo'] = $material->mactipo;
                $data['macurl'] = $material->macurl;
                $data['macnivl'] = $material->macnivl;
                $data['macstat'] = $material->macstat;
                foreach ($contents as $content) {
                    $data['contents'][] = ['conid' => $content->conid];
                }
                if ($material->mactipo === MACTIPO_SIMULADO) {
                    $questions = $this->material_model->find_material_questions($material->manid);
                    foreach ($questions as $question) {
                        $data['questions'][] = ['qenid' => $question->qenid];
                    }
                }

                // Salva o material no novo grupo
                $manid = $this->material_model->save($data);

                if (is_null($manid)) { // Verifica se houve ao salvar o material
                    $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $this->response['response_message'] = "Houve um erro ao tentar compartilhar o material! Tente novamente.\n";
                    $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                } else {
                    // Seta os dados para o envio do email de notificação de novo grupo
                    $from = EMAIL_SMTP_USER;
                    $to = $admin->uscmail;
                    $subject = EMAIL_SUBJECT_SHARE_MATERIAL;
                    $message = EMAIL_MESSAGE_SHARE_MATERIAL;
                    $datas = [
                        EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                        EMAIL_KEY_USCNOME => (!is_null($admin->uscnome)) ? $admin->uscnome : $admin->usclogn,
                        EMAIL_KEY_MACDESC => $data['macdesc'],
                        EMAIL_KEY_GRCNOME => $group->grcnome,
                        EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                        EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                    ];

                    $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                    if ($this->biblivirti_email->send() === false) {
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_SHARE_MATERIAL . "!\n";
                        $this->response['response_message'] .= "Informe essa ocorrência a equipe de suporte do Biblivirti!";
                        $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                    } else {
                        $this->response['response_code'] = RESPONSE_CODE_OK;
                        $this->response['response_message'] = "Material cadastrado com sucesso!";
                        $this->response['response_data'] = $manid;
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

}