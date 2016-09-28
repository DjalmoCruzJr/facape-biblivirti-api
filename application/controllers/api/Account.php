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

        // Loading libraries
        $this->load->library('encryption/biblivirti_hash');
        $this->load->library('business/account_bo');
        $this->load->library('input/biblivirti_input');
    }

    /**
     * @url: API/account/login
     * @param string JSON
     * @return JSON
     *
     * Metodo para autenticar um usuario.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "uscmail" : "E-mail do usuario",
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
     *          "uscmail" : "E-mail do usuario",
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
            $user = $this->usuario_model->find_by_uscmail_and_uscsenh($data['uscmail'], $this->biblivirti_hash->make($data['uscsenh']));
            // Verifica se houve falha na execucao do model
            if (is_null($user)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Nenhum usuário encontrado.";
            } else if ($user->uscstat === USCSTAT_INATIVO) {
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Essa conta ainda não foi ativada!\n";
                $this->response['response_message'] .= "Acesse o link de confirmação no seu e-mail para ativar sua conta.";
            } else {
                unset($user->uscsenh); // Remove a senha do objeto de retorno
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
     *          "uscmail" : "E-mail do usuario",
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
            $user = $this->usuario_model->find_by_uscfbid($data['uscfbid']);
            // Verifica se houve flaha na execucao do model
            if (is_null($user)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Nenhum usuário encontrado.";
            } else if ($user->uscstat === USCSTAT_INATIVO) {
                $this->response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                $this->response['response_message'] = "Essa conta ainda não foi ativada!\n";
                $this->response['response_message'] .= "Acesse o link de confirmação no seu e-mail para ativar sua conta.";
            } else {
                unset($user->uscsenh); // Remove a senha do objeto de retorno
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
     *      "uscmail": "E-mail do usuario",
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
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $data = $this->account_bo->get_data();
            unset($data['uscsenh2']); // Remove o campo USCSENH2 do array de dados
            $data['uscsenh'] = $this->biblivirti_hash->make($data['uscsenh']); // Gera o hash da senha
            $id = $this->usuario_model->save($data);
            // verifica se houve falha na execucao do model
            if (is_null($id)) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "Houve um erro ao tentar cadastrar o ususario! Tente novamente.\n";
                $response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
            } else {
                // Desabilita todos os tokens de confirmacao anteriores para o usuario em questao
                $this->recuperarsenha_model->disable_all_tokens_by_rsnidus($id);

                $token['canidus'] = $id;
                $token['cactokn'] = $this->biblivirti_hash->token($data['uscmail']);
                $token['canid'] = $this->confirmaremail_model->save($token);

                // Verifica se o token de redefinicao foi gravado corretamente
                if ($token['canid'] === 0) {
                    $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $response['response_message'] = "Houve um erro ao tentar gerar token de confirmação de e-mail! Tente novamente.\n";
                    $response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                } else {
                    // FALTA: Enviar o email com o link de redefinicao de senha

                    // FALTA: Confirmar o envio do email com o link de redefinicao de senha
                    $response['response_code'] = RESPONSE_CODE_OK;
                    $response['response_message'] = "Usuário cadastrado com  sucesso!";
                    $response['response_data'] = ['usnid' => $id];
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/account/recover
     * @param string JSON
     * @return JSON
     *
     * Metodo para recuperar o acesso um usuario.
     * Recebe como parametro um <i>JSON</i> no seguinte formato:
     * {
     *      "uscmail": "E-mail do usuario"
     * }
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta",
     *      "response_data" : {
     *          "usnid" : "ID do usuario",
     *          "uscnome" : "Nome do usuario",
     *          "uscmail" : "E-mail do usuario",
     *          "usclogn" : "Login do usuario",
     *          "uscfoto" : "Caminho da foto do usuario",
     *          "uscstat" : "Status do usuario",
     *          "tsdcadt" : "Data de cadastro do usuario",
     *          "usdaldt" : "Data de atualizacao do usuario"
     *      }
     * }
     */
    public function recover() {
        $data = $this->biblivirti_input->get_raw_input_data();

        $this->response = [];
        $this->account_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_recover() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $data = $this->account_bo->get_data();
            $user = $this->usuario_model->find_by_uscmail($data['uscmail']);
            // verifica se houve falha na execucao do model
            if (is_null($user)) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "E-mail não encontrado.";
            } else {
                // Desabilita todos os tokens de redefinicao anteriores para o usuario em questao
                $this->recuperarsenha_model->disable_all_tokens_by_rsnidus($user->usnid);

                $token['rsnidus'] = $user->usnid;
                $token['rsctokn'] = $this->biblivirti_hash->token($user->uscmail); // Gera token de redefinicao de senha
                $token['rsnid'] = $this->recuperarsenha_model->save($token);

                // Verifica se o token de redefinicacao foi gravado com sucesso
                if ($token['rsnid'] === 0) {
                    $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $response['response_message'] = "Houve um erro ao tentar recuperar senha de acesso! Tente novamente.\n";
                    $response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                } else {
                    // FALTA: Enviar o email com o link de redefinicao de senha

                    // FALTA: Confirmar o envio do email com o link de redefinicao de senha

                    unset($user->uscsenh); // Remove a senha do objeto de retorno
                    $response['response_code'] = RESPONSE_CODE_OK;
                    $response['response_message'] = "E-mail confirmado com sucesso!\n";
                    $response['response_message'] .= "Um link de redefinição de senha foi enviado para seu e-mail!";
                    $response['response_data'] = $user;
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/account/email/confirmation?cactokn=$1
     * @param string cactokn
     * @return JSON
     *
     * Metodo para confirmar o e-mail de acesso um usuario.
     * Recebe o(s) parametro(s) <i>cactokn</i> atraves de <i>GET</i>
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta",
     *      "response_data" : {
     *          "usnid" : "ID do usuario",
     *          "uscnome" : "Nome do usuario",
     *          "uscmail" : "E-mail do usuario",
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
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $data = $this->account_bo->get_data();
            $token = $this->confirmaremail_model->find_by_cactokn($data['cactokn']);
            if (is_null($token)) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "Token de confirmação inválido!";
            } else {
                $user = $this->usuario_model->find_by_usnid($token->canidus);
                $user->uscstat = USCSTAT_ATIVO; // Muda o status do usuario para ATIVO
                $user2['usnid'] = $user->usnid;
                $user2['uscstat'] = $user->uscstat;

                // Verifica o usuario foi atualizado com sucesso
                if ($this->usuario_model->save($user2) === false) {
                    $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $response['response_message'] = "Houve um erro ao tentar confirmar e-mail do usuário! Tente novamente.\n";
                    $response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                } else {
                    $token->cacstat = CACSTAT_INATIVO; // Muda o status do token para INATIVO
                    $token2['canid'] = $token->canid;
                    $token2['cacstat'] = $token->cacstat;
                    $this->confirmaremail_model->save($token2);

                    unset($user->uscsenh); // Remove a senha do obejeto de resposta

                    $response['response_code'] = RESPONSE_CODE_OK;
                    $response['response_message'] = "E-mail confirmado com cucesso!";
                    $response['response_data'] = $user;
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
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
     *              "uscmail" : "E-mail do usuario",
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
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $data = $this->account_bo->get_data();
            $token = $this->recuperarsenha_model->find_by_rsctokn($data['rsctokn']);
            if (is_null($token)) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "Token de redefinição inválido!";
            } else {
                $user = $this->usuario_model->find_by_usnid($token->rsnidus);
                unset($user->uscsenh); // Remove a senha do obeto de retorno

                // Muda o status do token
                $token->rscstat = RSCSTAT_INATIVO;
                $token2['rsnid'] = $token->rsnid;
                $token2['rsnidus'] = $token->rsnidus;
                $token2['rsctokn'] = $token->rsctokn;
                $token2['rscstat'] = $token->rscstat;
                $this->recuperarsenha_model->save($token2);

                // Prepara os dados da resposta
                $data = [];
                $data['user'] = $user;
                $data['intent_category'] = INTENT_CATEGORY_ACCOUNT;
                $data['intent_action'] = INTENT_ACTION_ACCOUNT_PASSWORD_RESET;
                $response['response_code'] = RESPONSE_CODE_OK;
                $response['response_message'] = "Redefinição de senha autorizada com cucesso!";
                $response['response_data'] = $data;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
}