<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 22/08/2016
 *
 * Controller da API para gerenciar o acesso aos dados da <b>Conta</b>.
 */
class Account extends CI_Controller {

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
        $this->load->model("usuario_model");
        $this->load->model("recuperarsenha_model");
        $this->load->model("confirmaremail_model");
        $this->load->model("areainteresse_model");
        $this->load->model("grupo_model");

        // Loading libraries
        $this->load->library('encryption/biblivirti_hash');
        $this->load->library('business/account_bo');
        $this->load->library('input/biblivirti_input');
        $this->load->library('media/biblivirti_media');
        $this->load->library('email/biblivirti_email');
    }

    /**
     * @url: API/account/login
     * @param string JSON
     * @return JSON
     *
     * Metodo para autenticar um usuario.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "uscmail" : "E-email do usuario",
     *      "uscsenh" : "Senha do usuario"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : {
     *          "usnid" : "ID do usuario",
     *          "uscfbid" : "FacebookID do usuario",
     *          "uscnome" : "Nome do usuario",
     *          "uscmail" : "E-email do usuario",
     *          "usclogn" : "Login do usuario",
     *          "uscfoto" : "Caminho da foto do usuario",
     *          "uscstat" : "Status do usuario",
     *          "tsdcadt" : "Data de cadastro do usuario",
     *          "usdaldt" : "Data de atualizacao do usuario"
     *      }
     * }
     */
    public function login() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->account_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_login() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $data = $this->account_bo->get_data();
            $user = $this->usuario_model->find_by_uscmail_and_uscsenh($data['uscmail'], $this->biblivirti_hash->make($data['uscsenh']))[0];

            // Verifica se o usuario foi encontrado com sucesso
            if (is_null($user)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Nenhum usuário encontrado.";
            } else if ($user->uscstat === USCSTAT_INATIVO) {
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Essa conta ainda não foi ativada!\n";
                $this->response['response_message'] .= "Acesse o link de confirmação no seu e-email para ativar sua conta.";
            } else {
                unset($user->uscsenh); // Remove a senha do objeto de retorno
                $user->uscfoto = $user->uscfoto == null ? null : base_url(UPLOAD_IMAGES_PATH . $user->uscfoto);
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Usuário encontrado com sucesso!";
                $this->response['response_data'] = $user;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/account/login/facebook
     * @param string JSON
     * @return JSON
     *
     * Metodo para autenticar um usuario no facebook.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "uscfbid" : "FacebookID do usuario"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "request_code" : "Codigo da requsicao",
     *      "request_message" : "Mensagem da requsicao",
     *      "response_data" : {
     *          "usnid" : "ID do usuario",
     *          "uscfbid" : "FacebookID do usuario",
     *          "uscnome" : "Nome do usuario",
     *          "uscmail" : "E-email do usuario",
     *          "usclogn" : "Login do usuario",
     *          "uscfoto" : "Caminho da foto do usuario",
     *          "uscstat" : "Status do usuario",
     *          "tsdcadt" : "Data de cadastro do usuario",
     *          "usdaldt" : "Data de atualizacao do usuario"
     *      }
     * }
     */
    public function login_facebook() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->account_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_login_facebook() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $data = $this->account_bo->get_data();
            $user = $this->usuario_model->find_by_uscfbid($data['uscfbid'])[0];
            // Verifica se houve flaha na execucao do model
            if (is_null($user)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Nenhum usuário encontrado.";
            } else if ($user->uscstat === USCSTAT_INATIVO) {
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Essa conta ainda não foi ativada!\n";
                $this->response['response_message'] .= "Acesse o link de confirmação no seu e-email para ativar sua conta.";
            } else {
                unset($user->uscsenh); // Remove a senha do objeto de retorno
                $user->uscfoto = $user->uscfoto == null ? null : base_url(UPLOAD_IMAGES_PATH . $user->uscfoto);
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Usuário encontrado com sucesso!";
                $this->response['response_data'] = $user;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/account/register
     * @param string JSON
     * @return JSON
     *
     * Metodo para cadastrar um novo usuario.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "uscmail": "E-email do usuario",
     *      "usclogn": "Login do usuario",
     *      "uscsenh": "Senha do usuario",
     *      "uscsenh2": "Confirmacao de senha"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta",
     *      "response_data" : {
     *          "usnid" : "ID do usuario inserido"
     *      }
     * }
     */
    public function register() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->account_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_register() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $data = $this->account_bo->get_data();
            unset($data['uscsenh2']); // Remove o campo USCSENH2 do array de dados
            $data['uscsenh'] = $this->biblivirti_hash->make($data['uscsenh']); // Gera o hash da senha
            $id = $this->usuario_model->save($data);
            // verifica se houve falha na execucao do model
            if (is_null($id)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Houve um erro ao tentar cadastrar o ususario! Tente novamente.\n";
                $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
            } else {
                $token['canidus'] = $id;
                $token['cactokn'] = $this->biblivirti_hash->token($data['uscmail']);
                $token['canid'] = $this->confirmaremail_model->save($token);

                // Verifica se o token de redefinicao foi gravado corretamente
                if ($token['canid'] === 0) {
                    $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $this->response['response_message'] = "Houve um erro ao tentar gerar token de confirmação de e-email! Tente novamente.\n";
                    $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                } else {
                    // Seta os dados para o envio do email de ativação de conta
                    $from = EMAIL_SMTP_USER;
                    $to = $data['uscmail'];
                    $subject = EMAIL_SUBJECT_NEW_REGISTER;
                    $message = EMAIL_MESSAGE_NEW_REGISTER;
                    $datas = [
                        EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                        EMAIL_KEY_USCNOME => $data['usclogn'],
                        EMAIL_KEY_CACTOKN => $token['cactokn'],
                        EMAIL_KEY_CONFIRMATION_LINK => base_url('API/account/email/confirmation') . '?cactokn=' . $token['cactokn'],
                        EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                        EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                    ];

                    $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                    if ($this->biblivirti_email->send() === false) {
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_NEW_REGISTER . "! Tente novamente.\n";
                        $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                        $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                    } else {
                        $this->response['response_code'] = RESPONSE_CODE_OK;
                        $this->response['response_message'] = "Usuário cadastrado com sucesso!";
                        $this->response['response_data'] = ['usnid' => $id];
                    }
                }
            }
        }


        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/account/recovery
     * @param string JSON
     * @return JSON
     *
     * Metodo para recuperar o acesso um usuario.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "uscmail": "E - email do usuario"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta",
     *      "response_data" : {
     *          "usnid" : "ID do usuario",
     *          "uscnome" : "Nome do usuario",
     *          "uscmail" : "E - email do usuario",
     *          "usclogn" : "Login do usuario",
     *          "uscfoto" : "Caminho da foto do usuario",
     *          "uscstat" : "Status do usuario",
     *          "tsdcadt" : "Data de cadastro do usuario",
     *          "usdaldt" : "Data de atualizacao do usuario"
     *      }
     * }
     */
    public function recovery() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->account_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_recovery() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e / ou inválidos . VERIFIQUE!";
            $this->response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $data = $this->account_bo->get_data();
            $user = $this->usuario_model->find_by_uscmail($data['uscmail'])[0];

            // verifica se houve falha na execucao do model
            if (is_null($user)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "E-email não encontrado.";
            } else if ($user->uscstat === USCSTAT_INATIVO) {
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Essa conta ainda não foi ativada!\n";
                $this->response['response_message'] .= "Acesse o link de confirmação no seu e-email para ativar sua conta.";
            } else {
                // Desabilita todos os tokens de redefinicao anteriores para o usuario em questao
                $this->recuperarsenha_model->disable_all_tokens_by_rsnidus($user->usnid);

                $token['rsnidus'] = $user->usnid;
                $token['rsctokn'] = $this->biblivirti_hash->token($user->uscmail); // Gera token de redefinicao de senha
                $token['rsnid'] = $this->recuperarsenha_model->save($token);

                // Verifica se o token de redefinicacao foi gravado com sucesso
                if ($token['rsnid'] === 0) {
                    $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $this->response['response_message'] = "Houve um erro ao tentar recuperar senha de acesso!Tente novamente . \n";
                    $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                } else {
                    // Seta os dados para o envio do email de recuperação de senha
                    $from = EMAIL_SMTP_USER;
                    $to = $user->uscmail;
                    $subject = EMAIL_SUBJECT_PASSWORD_RECOVERY;
                    $message = EMAIL_MESSAGE_PASSWORD_RECOVERY;
                    $datas = [
                        EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                        EMAIL_KEY_USCNOME => (!is_null($user->uscnome)) ? $user->uscnome : $user->usclogn,
                        EMAIL_KEY_RSCTOKN => $token['rsctokn'],
                        EMAIL_KEY_RECOVERY_LINK => base_url('API/account/password/reset') . '?rsctokn=' . $token['rsctokn'],
                        EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                        EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                    ];


                    $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                    if ($this->biblivirti_email->send() == false) {
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_PASSWORD_RECOVERY . "! Tente novamente.\n";
                        $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                        $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                    } else {
                        unset($user->uscsenh); // Remove a senha do objeto de retorno
                        $user->uscfoto = $user->uscfoto == null ? null : base_url(UPLOAD_IMAGES_PATH . $user->uscfoto);
                        $this->response['response_code'] = RESPONSE_CODE_OK;
                        $this->response['response_message'] = "E-email confirmado com sucesso!\n";
                        $this->response['response_message'] .= "Um link de redefinição de senha foi enviado para seu e-email!";
                        $this->response['response_data'] = $user;
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/account/email/confirmation?cactokn=$1
     * @param string cactokn
     * @return JSON
     *
     * Metodo para confirmar o e-email de acesso um usuario.
     * Recebe o(s) parametro(s) <i>cactokn</i> atraves de <i>GET</i>
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta",
     *      "response_data" : {
     *          "usnid" : "ID do usuario",
     *          "uscnome" : "Nome do usuario",
     *          "uscmail" : "E - email do usuario",
     *          "usclogn" : "Login do usuario",
     *          "uscfoto" : "Caminho da foto do usuario",
     *          "uscstat" : "Status do usuario",
     *          "tsdcadt" : "Data de cadastro do usuario",
     *          "usdaldt" : "Data de atualizacao do usuario"
     *      }
     * }
     */
    public function email_confirmation() {
        $data['cactokn'] = $this->input->get('cactokn');

        $this->response = [];
        $this->account_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_email_confirmation() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e / ou inválidos . VERIFIQUE!";
            $this->response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $data = [];
            $data = $this->account_bo->get_data();
            $token = $this->confirmaremail_model->find_by_cactokn($data['cactokn']);
            if (is_null($token)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Token de confirmação inválido!";
            } else {
                $user = $this->usuario_model->find_by_usnid($token->canidus);
                $user->uscstat = USCSTAT_ATIVO; // Muda o status do usuario para ATIVO
                $user2['usnid'] = $user->usnid;
                $user2['uscstat'] = $user->uscstat;

                // Verifica o usuario foi atualizado com sucesso
                if ($this->usuario_model->update($user2) === false) {
                    $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $this->response['response_message'] = "Houve um erro ao tentar confirmar e-mail do usuário! Tente novamente . \n";
                    $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                } else {
                    $token->cacstat = CACSTAT_INATIVO; // Muda o status do token para INATIVO
                    $token2['canid'] = $token->canid;
                    $token2['cacstat'] = $token->cacstat;
                    $this->confirmaremail_model->update($token2);

                    // Seta os dados para o envio do email de recuperação de senha
                    $from = EMAIL_SMTP_USER;
                    $to = $user->uscmail;
                    $subject = EMAIL_SUBJECT_ACCOUNT_ACTIVATED;
                    $message = EMAIL_MESSAGE_ACCOUNT_ACTIVATED;
                    $datas = [
                        EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                        EMAIL_KEY_USCNOME => (!is_null($user->uscnome)) ? $user->uscnome : $user->usclogn,
                        EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                        EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                    ];

                    $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                    if ($this->biblivirti_email->send() === false) {
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_ACCOUNT_ACTIVATED . "! Tente novamente.\n";
                        $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                        $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                    } else {
                        unset($user->uscsenh); // Remove a senha do obejeto de resposta
                        $user->uscfoto = $user->uscfoto == null ? null : base_url(UPLOAD_IMAGES_PATH . $user->uscfoto);
                        $this->response['response_code'] = RESPONSE_CODE_OK;
                        $this->response['response_message'] = "E-mail confirmado com cucesso!";
                        $this->response['response_data'] = $user;
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/account/password/reset?rsctokn=$1
     * @param string rsctokn
     * @return JSON
     *
     * Metodo para recuperar o acesso um usuario.
     * Recebe o(s) parametro(s) <i>rsctokn</i> atraves de <i>GET</i>
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta",
     *      "response_data" : {
     *          "user"  : {
     *              "usnid" : "ID do usuario",
     *              "uscnome" : "Nome do usuario",
     *              "uscmail" : "E - email do usuario",
     *              "usclogn" : "Login do usuario",
     *              "uscfoto" : "Caminho da foto do usuario",
     *              "uscstat" : "Status do usuario",
     *              "tsdcadt" : "Data de cadastro do usuario",
     *              "usdaldt" : "Data de atualizacao do usuario"
     *          },
     *          "intent_category" : "Nome da intent category",
     *          "intent_action" : "Nome da intent action"
     *      }
     * }
     */
    public function password_reset() {
        $data['rsctokn'] = $this->input->get('rsctokn');

        $this->response = [];
        $this->account_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_password_reset() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e / ou inválidos . VERIFIQUE!";
            $this->response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $data = $this->account_bo->get_data();
            $token = $this->recuperarsenha_model->find_by_rsctokn($data['rsctokn']);
            if (is_null($token)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Token de redefinição inválido!";
            } else {
                $user = $this->usuario_model->find_by_usnid($token->rsnidus);
                unset($user->uscsenh); // Remove a senha do obeto de retorno
                $user->uscfoto = $user->uscfoto == null ? null : base_url(UPLOAD_IMAGES_PATH . $user->uscfoto);

                // Muda o status do token
                $token->rscstat = RSCSTAT_INATIVO;
                $token2['rsnid'] = $token->rsnid;
                $token2['rsnidus'] = $token->rsnidus;
                $token2['rsctokn'] = $token->rsctokn;
                $token2['rscstat'] = $token->rscstat;
                $this->recuperarsenha_model->update($token2);

                // Prepara os dados da resposta
                $data = [];
                $data['user'] = $user;
                $data['intent_category'] = INTENT_CATEGORY_ACCOUNT;
                $data['intent_action'] = INTENT_ACTION_ACCOUNT_PASSWORD_EDIT;
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Redefinição de senha autorizada com cucesso!";
                $this->response['response_data'] = $data;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/account/password/edit
     * @return JSON
     *
     * Metodo para alterar a senha de acesso um usuario.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid" : "ID do usuario",
     *      "uscsenh" : "Senha do usuario",
     *      "uscsenh2" : "Confirmacao da senha"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta",
     *      "response_data" : {
     *          "usnid" : "ID do usuario"
     *      }
     * }
     */
    public function password_edit() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->account_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_password_edit() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e / ou inválidos . VERIFIQUE!";
            $this->response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $data = $this->account_bo->get_data();
            $data['uscsenh'] = $this->biblivirti_hash->make($data['uscsenh']); // Gera o hash da senha
            unset($data['uscsenh2']); // Remove o campo USCSENH2 do array de dados

            $id = $this->usuario_model->update($data);
            // verifica se houve falha na execucao do model
            if (is_null($id)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Houve um erro ao tentar editar senha de acesso! Tente novamente.\n";
                $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
            } else {
                // Carrega os dados do usuario p/ enviar o email
                $user = $this->usuario_model->find_by_usnid($data['usnid']);
                // Seta os dados para o envio do email de ativação de conta
                $from = EMAIL_SMTP_USER;
                $to = $user->uscmail;
                $subject = EMAIL_SUBJECT_PASSWORD_CHANGED;
                $message = EMAIL_MESSAGE_PASSWORD_CHANGED;
                $datas = [
                    EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                    EMAIL_KEY_USCNOME => (!is_null($user->uscnome)) ? $user->uscnome : $user->usclogn,
                    EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                    EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                ];

                $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                if ($this->biblivirti_email->send() === false) {
                    $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                    $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_PASSWORD_CHANGED . "! Tente novamente.\n";
                    $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                    $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                } else {
                    $this->response['response_code'] = RESPONSE_CODE_OK;
                    $this->response['response_message'] = "Senha alterada com sucesso!";
                    $this->response['response_data'] = ['usnid' => $id];
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/account/search
     * @return JSON
     *
     * Metodo para buscar usuarios.
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
     *              "usnid" : "ID do usuario",
     *              "uscnome" : "Nome do usuario",
     *              "uscmail" : "E - email do usuario",
     *              "usclogn" : "Login do usuario",
     *              "uscfoto" : "Caminho da foto do usuario",
     *              "uscstat" : "Status do usuario",
     *              "tsdcadt" : "Data de cadastro do usuario",
     *              "usdaldt" : "Data de atualizacao do usuario"
     *          },
     *      ]
     *
     * }
     */
    public function search() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->account_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_search() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e / ou inválidos . VERIFIQUE!";
            $this->response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $data = $this->account_bo->get_data();
            $users = $this->usuario_model->find_by_reference($data['reference']);
            if (is_null($users)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Nenhum usuário encontrado!";
            } else {
                foreach ($users as $user) {
                    unset($user->uscsenh); // Remove o campo senha dos objetos de resposta
                    $user->uscfoto = $user->uscfoto == null ? null : base_url(UPLOAD_IMAGES_PATH . $user->uscfoto);
                }
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Usuário(s) encontrado(s) com sucesso!";
                $this->response['response_data'] = $users;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/account/profile
     * @return JSON
     *
     * Metodo para buscar as informacoes do perfil do usuario.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid" : "ID do usuario"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta",
     *      "response_data" : {
     *            "usnid" : "ID do usuario",
     *          "uscnome" : "Nome do usuario",
     *          "uscmail" : "E - email do usuario",
     *          "usclogn" : "Login do usuario",
     *          "uscfoto" : "Caminho da foto do usuario",
     *          "uscstat" : "Status do usuario",
     *          "usdcadt" : "Data de cadastro do usuario",
     *          "usdaldt" : "Data de atualizacao do usuario",
     *          "groups" : [
     *              {
     *                  "grnid": "ID do grupo",
     *                  "grcnome" : "Nome do grupo",
     *                  "grcfoto" : "Caminho da foto do grupo",
     *                  "grctipo" : "Tipo do grupo",
     *                  "grdcadt" : "Data de cadastro do grupo",
     *                  "areaofinterest" : {
     *                      "ainid" : "ID da area de interasse",
     *                      "aicdesc" : "Descricao da area de interesse"
     *                  },
     *                  "admin" : {
     *                      "usnid" : "ID do usuario",
     *                      "uscfbid" : "FacebookID do usuario",
     *                      "uscnome" : "Nome do usuario",
     *                      "uscmail" : "E-email do usuario",
     *                      "usclogn" : "Login do usuario",
     *                      "uscfoto" : "Caminho da foto do usuario",
     *                      "uscstat" : "Status do usuario",
     *                      "usdcadt" : "Data de cadastro do usuario"
     *                  },
     *                    "users" : [
     *                        {
     *                        "usnid" : "ID do usuario",
     *                        "uscfbid" : "FacebookID do usuario",
     *                        "uscnome" : "Nome do usuario",
     *                        "uscmail" : "E-email do usuario",
     *                        "usclogn" : "Login do usuario",
     *                        "uscfoto" : "Caminho da foto do usuario",
     *                        "uscstat" : "Status do usuario",
     *                        "usdcadt" : "Data de cadastro do usuario"
     *                    },
     *                    ]
     *              }
     *          ]
     *      }
     * }
     */
    public function profile() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->account_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_profile() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e / ou inválidos . VERIFIQUE!";
            $this->response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $data = $this->account_bo->get_data();
            $user = $this->usuario_model->find_by_usnid($data['usnid']);
            if (is_null($user)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Houve um erro ao tentar carregar as informações do perfil do usuário! Tente novamente.\n";
                $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
            } else {
                $groups = $this->grupo_model->find_by_usnid($data['usnid']);
                if (!is_null($groups)) {
                    foreach ($groups as $group) {
                        $group->areaofinterest = $this->areainteresse_model->find_by_ainid($group->grnidai);
                        $group->admin = $this->grupo_model->find_group_admin($group->grnid);
                        $group->users = $this->grupo_model->find_group_users($group->grnid);
                        unset($group->grnidai);
                    }
                }
                $user->groups = $groups;
                $user->uscfoto = $user->uscfoto == null ? null : base_url(UPLOAD_IMAGES_PATH . $user->uscfoto);
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Perfil encontrado com sucesso!";
                $this->response['response_data'] = $user;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/account/profile/edit
     * @param string JSON
     * @return JSON
     *
     * Metodo para editar os dados do perfil do usuario.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "usnid": "ID do usuario",
     *      "uscmail": "E-email do usuario",
     *      "usclogn": "Login do usuario",
     *      "uscsenh": "Senha do usuario",
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta",
     *      "response_data" : {
     *          "usnid" : "ID do usuario atualizado"
     *      }
     * }
     */
    public function profile_edit() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->account_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_profile_edit() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $data = $this->account_bo->get_data();
            $user = $this->usuario_model->find_by_uscmail_and_uscsenh($data['uscmail'], $this->biblivirti_hash->make($data['uscsenh']))[0];

            if (is_null($user)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Perfil do usuário não encontrado!\n";
                $this->response['response_message'] .= "Por favor, verifique seu e-mail e senha.";
            } else if ($user->uscstat === USCSTAT_INATIVO) {
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Essa conta ainda não foi ativada!\n";
                $this->response['response_message'] .= "Acesse o link de confirmação no seu e-email para ativar sua conta.";
            } else {
                // Verifica se o LOGIN passado EH DIFERENTE ao LOGIN do usuario encontrado e se ja nao esta sendo usado
                if ($data['usclogn'] != $user->usclogn && !is_null($this->CI->usuario_model->find_by_usclogn($data['usclogn']))) {
                    $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                    $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
                    $this->response['response_errors'] = ['usclogn' => 'Já existe um usuário cadastrado com este login!'];
                } else {
                    // Verifica se LOGIN passado EH IGUAL ao LOGIN so usuario encontrado
                    if ($data['usclogn'] == $user->usclogn) {
                        unset($data['usclogn']); // Remove o campo LOGIN do array de dados a ser atualizado
                    }

                    unset($data['uscsenh']); // Remove o campo senha do array de dados a ser atualizado
                    $data['usnid'] = $user->usnid; // Adiciona o campo ID do usuario no objeto de atualizacao

                    // Verifica se a imagem do grupo foi informada pelo usuario
                    if (isset($data['uscfoto'])) {
                        // Salva a imagem recebida no disco e devolve para o campo o caminho da imagem
                        $data['uscfoto'] = $this->biblivirti_media->save_image($data['usnid'], $data['uscfoto']);
                    }

                    if (isset($data['uscfoto']) && $data['uscfoto'] == null) {
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar salvar a imagem do usuário! Tente novamente.\n";
                        $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                    } else {
                        // Atualiza as informações do usuario no banco de dados
                        $id = $this->usuario_model->update($data);
                        // verifica se houve falha na execucao do model
                        if (is_null($id)) {
                            $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                            $this->response['response_message'] = "Houve um erro ao tentar atualizar os dados do ususario! Tente novamente.\n";
                            $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                        } else {
                            // Seta os dados para o envio de email.
                            $from = EMAIL_SMTP_USER;
                            $to = $data['uscmail'];
                            $subject = EMAIL_SUBJECT_EDIT_PROFILE;
                            $message = EMAIL_MESSAGE_EDIT_PROFILE;
                            $datas = [
                                EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                                EMAIL_KEY_USCNOME => (!is_null($data['uscnome'])) ? $data['uscnome'] : $data['usclogn'],
                                EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                                EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                            ];

                            $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                            if ($this->biblivirti_email->send() === false) {
                                $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                                $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_EDIT_PROFILE . "! Tente novamente.\n";
                                $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                                $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                            } else {
                                $this->response['response_code'] = RESPONSE_CODE_OK;
                                $this->response['response_message'] = "Perfil atualizado com sucesso!";
                                $this->response['response_data'] = ['usnid' => $id];
                            }
                        }
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/account/group/members/list
     * @return JSON
     *
     * Metodo para buscar os membros de um determinado grupo.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "grnid" : "ID do grupo"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta",
     *      "response_data" : [
     *          {
     *              "usnid" : "ID do usuario",
     *              "uscnome" : "Nome do usuario",
     *              "uscmail" : "E - email do usuario",
     *              "usclogn" : "Login do usuario",
     *              "uscfoto" : "Caminho da foto do usuario",
     *              "uscstat" : "Status do usuario",
     *              "tsdcadt" : "Data de cadastro do usuario",
     *              "usdaldt" : "Data de atualizacao do usuario"
     *          },
     *      ]
     */
    public function group_members_list() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->account_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_group_members_list() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $data = $this->account_bo->get_data();
            $members = $this->grupo_model->find_group_users($data['grnid']);
            // Verifica se os registros NAO foram encontrados
            if (is_null($members)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Nenhum membro encontrado.";
            } else {
                foreach ($members as $member) {
                    $member->uscfoto = $member->uscfoto == null ? null : base_url(UPLOAD_IMAGES_PATH . $member->uscfoto);
                }
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Membro(s) encontrado(s) com sucesso!";
                $this->response['response_data'] = $members;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/account/activation/resend
     * @param string JSON
     * @return JSON
     *
     * Metodo para reenviar o token de ativacao de conta.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "uscmail": "E-email do usuario"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta",
     *      "response_data" : {
     *          "usnid" : "ID do usuario inserido"
     *      }
     * }
     */
    public function activation_resend() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->account_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_activation_resend() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $data = $this->account_bo->get_data();
            $user = $this->usuario_model->find_by_uscmail($data['uscmail'])[0];
            // verifica se houve falha na execucao do model
            if (is_null($user)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Conta não encontrada!.\n";
                $this->response['response_message'] .= "Por favor, verifique se o e-mail está correto e tente novamente.";
            } else {
                $token['canidus'] = $user->usnid;
                $token['cactokn'] = $this->biblivirti_hash->token($user->uscmail);
                $token['canid'] = $this->confirmaremail_model->save($token);

                // Verifica se o token de redefinicao foi gravado corretamente
                if ($token['canid'] === 0) {
                    $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $this->response['response_message'] = "Houve um erro ao tentar gerar token de ativação de e-email! Tente novamente.\n";
                    $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                } else {

                    //Desabilita todos os outros tokens de ativacao para a conta em questao
                    $this->confirmaremail_model->disable_all_tokens_by_canidus($user->usnid);

                    // Seta os dados para o envio do email de ativação de conta
                    $from = EMAIL_SMTP_USER;
                    $to = $user->uscmail;
                    $subject = EMAIL_SUBJECT_ACCOUNT_ACTIVATION_RESEND;
                    $message = EMAIL_MESSAGE_ACCOUNT_ACTIVATION_RESEND;
                    $datas = [
                        EMAIL_KEY_EMAIL_SMTP_USER_ALIAS => EMAIL_SMTP_USER_ALIAS,
                        EMAIL_KEY_USCNOME => $user->usclogn,
                        EMAIL_KEY_CACTOKN => $token['cactokn'],
                        EMAIL_KEY_CONFIRMATION_LINK => base_url('API/account/email/confirmation') . '?cactokn=' . $token['cactokn'],
                        EMAIL_KEY_EMAIL_SMTP_USER => EMAIL_SMTP_USER,
                        EMAIL_KEY_SEDING_DATE => date('d/m/Y H:i:s')
                    ];

                    $this->biblivirti_email->set_data($from, $to, $subject, $message, $datas);

                    if ($this->biblivirti_email->send() === false) {
                        $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
                        $this->response['response_message'] = "Houve um erro ao tentar enviar e-mail de notificação de " . EMAIL_SUBJECT_NEW_REGISTER . "! Tente novamente.\n";
                        $this->response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                        $this->response['response_errors'] = $this->biblivirti_email->get_errros();
                    } else {
                        $this->response['response_code'] = RESPONSE_CODE_OK;
                        $this->response['response_message'] = "Token de ativação enviado com sucesso!";
                        $this->response['response_message'] .= "Verique sua caixa de e-mails.";
                        $this->response['response_data'] = ['usnid' => $user->usnid];
                    }
                }
            }
        }


        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }
}