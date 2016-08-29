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
        $this->load->model("user_model");

        // Loading libraries
        $this->load->library('biblivirti_hash');
        $this->load->library('account_bo');
    }

    /**
     * @url: API/account/login
     * @param string uscmail
     * @param string uscsenh
     * @return JSON
     *
     * Metodo para autenticar um usuario.
     * Recebe o(s) parametro(s) <i>uscmail</i> e <i>uscsenh</i> atraves de <i>POST</i> e retorna um <i>JSON</i>
     * no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem de resposta",
     *      "response_data" : {
     *          "USNID" : "ID do usuario",
     *          "USCNOME" : "Nome do usuario",
     *          "uscmail" : "E-mail do usuario",
     *          "USCLOGN" : "Login do usuario",
     *          "USCFOTO" : "Caminho da foto do usuario",
     *          "USCSTAT" : "Status do usuario",
     *          "USDCADT" : "Data de cadastro do usuario"
     *      }
     * }
     */
    public function login() {
        $data['uscmail'] = $this->input->post('uscmail');
        $data['uscsenh'] = $this->input->post('uscsenh');

        $this->response = [];
        $this->account_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_login() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $user = $this->user_model->find_by_uscmail_and_uscsenh($data['uscmail'], $this->biblivirti_hash->make($data['uscsenh']));
            // Verifica se houve falha na execucao do model
            if (is_null($user)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Usuário não encontrado. VERIFIQUE!";
            } else {
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Usuário não encontrado. VERIFIQUE!";
                $this->response['response_data'] = $user;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/account/login/facebook
     * @param string uscfbid
     * @return JSON
     *
     * Metodo para autenticar um usuario no facebook.
     * Recebe o(s) parametro(s) <i>uscmail</i> e <i>uscsenh</i> atraves de <i>POST</i> e retorna um <i>JSON</i>
     * no seguinte formato:
     * {
     *      "request_code" : "Codigo da requsicao",
     *      "request_message" : "Mensagem da requsicao",
     *      "response_data" : {
     *          "USNID" : "ID do usuario",
     *          "USCNOME" : "Nome do usuario",
     *          "USCMAIL" : "E-mail do usuario",
     *          "USCLOGN" : "Login do usuario",
     *          "USCFOTO" : "Caminho da foto do usuario",
     *          "USCSTAT" : "Status do usuario",
     *          "USDCADT" : "Data de cadastro do usuario"
     *      }
     * }
     */
    public function login_facebook() {
        $data['uscfbid'] = $this->input->post('uscfbid');

        $this->response = [];
        $this->account_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_login_facebook() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $user = $this->user_model->find_by_uscfbid($data['uscfbid']);
            // Verifica se houve flaha na execucao do model
            if (is_null($user)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Usuário não encontrado. VERIFIQUE!";
            } else {
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Usuário não encontrado. VERIFIQUE!";
                $this->response['response_data'] = $user;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/account/register
     * @param string uscmail
     * @param string usclogn
     * @param string uscsenh
     * @param string uscsenh2
     * @return JSON
     *
     * Metodo para cadastrar um novo usuario.
     * Recebe o(s) parametro(s) <i>uscmail</i>, <i>usclogn</i>, <i>uscsenh</i> e <i>uscsenh2</i> atraves de <i>POST</i>
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta",
     *      "response_data" : {
     *          "USNID" : "ID do usuario inserido"
     *      }
     * }
     */
    public function register() {
        $data['uscmail'] = $this->input->post('uscmail');
        $data['usclogn'] = $this->input->post('usclogn');
        $data['uscsenh'] = $this->input->post('uscsenh');
        $data['uscsenh2'] = $this->input->post('uscsenh2');

        $this->response = [];
        $this->account_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_register() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->account_bo->get_errors();
        } else {
            unset($data['uscsenh2']); // Remove o campo USCSENH2 do array de dados
            $data['uscsenh'] = $this->biblivirti_hash->make($data['uscsenh']); // Gera o hash da senha
            $id = $this->user_model->save($data);
            // verifica se houve falha na execucao do model
            if (is_null($id)) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "Houve um erro ao tentar cadastrar o ususario! Tente novamente.\n";
                $response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
            } else {
                $response['response_code'] = RESPONSE_CODE_OK;
                $response['response_message'] = "Usuário cadastrado com  sucesso!";
                $response['response_data'] = ['usnid' => $id];
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: API/account/recover
     * @param string uscmail
     * @return JSON
     *
     * Metodo para recuperar o acesso um usuario.
     * Recebe o(s) parametro(s) <i>uscmail</i> atraves de <i>POST</i>
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta",
     *      "response_data" : {
     *          "USNID" : "ID do usuario",
     *          "USCNOME" : "Nome do usuario",
     *          "USCMAIL" : "E-mail do usuario",
     *          "USCLOGN" : "Login do usuario",
     *          "USCFOTO" : "Caminho da foto do usuario",
     *          "USCSTAT" : "Status do usuario",
     *          "USDCADT" : "Data de cadastro do usuario"
     *      }
     * }
     */
    public function recover() {
        $data['uscmail'] = $this->input->post('uscmail');

        $this->response = [];
        $this->account_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_recover() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->account_bo->get_errors();
        } else {
            $user = $this->user_model->find_by_uscmail($data['uscmail']);
            // verifica se houve falha na execucao do model
            if (is_null($user)) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "O e-mail informado não foi encontrado. VERIFIQUE!\n";
            } else {
                $token['rsnidus'] = $user->USNID;
                $token['rsctokn'] = $this->biblivirti_hash->token($user->USCMAIL); // Gera token de redefinicao de senha
                $id = $this->recover_model->save($token);
                // Verifica se houve falha na execucao do model
                if ($id === 0) {
                    $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $response['response_message'] = "Houve um erro ao tentar recuperar senha de acesso! Tente novamente.\n";
                    $response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                } else {
                    // Enviar o email com o link de redefinicao de senha
                    // Confirmar o envio do email com o link de redefinicao de senha
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
     *              "USNID" : "ID do usuario",
     *              "USCNOME" : "Nome do usuario",
     *              "USCMAIL" : "E-mail do usuario",
     *              "USCLOGN" : "Login do usuario",
     *              "USCFOTO" : "Caminho da foto do usuario",
     *              "USCSTAT" : "Status do usuario",
     *              "USDCADT" : "Data de cadastro do usuario"
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
            $token = $this->recover_model->find_by_rsctokn($data['rsctokn']);
            if (is_null($token)) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "TOKEN DE REDEFINIÇÃO inválido!";
            } else {
                $user = $this->user_model->find_by_usnid($token->RSNIDUS);
                $token->RSCSTAT = RSCSTAT_INATIVO;
                $token2['rsnid'] = $token->RSNID;
                $token2['rsnidus'] = $token->RSNIDUS;
                $token2['rsctokn'] = $token->RSCTOKN;
                $token2['rscstat'] = $token->RSCSTAT;
                $this->recover_model->save($token2);

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