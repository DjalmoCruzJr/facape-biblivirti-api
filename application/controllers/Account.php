<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 28/08/2016
 *
 * Controller para gerenciar acesso aos dados de <b>Acesso</b>
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

        // Initing variables
        $this->response = [];

        // Loading models
        $this->load->model("user_model");

        // Loading libraries
        $this->load->library('biblivirti_hash');
        $this->load->library('account_bo');
    }

    /**
     * @url: api/account/login
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
     *      "user" : {
     *          "usnid" : "ID do usuario",
     *          "uscnome" : "Nome do usuario",
     *          "uscmail" : "E-mail do usuario",
     *          "usclogn" : "Login do usuario",
     *          "uscfoto" : "Caminho da foto do usuario",
     *          "uscstat" : "Status do usuario",
     *          "usdcadt" : "Data de cadastro do usuario"
     *      }
     * }
     */
    public function login() {
        $this->response = [];
        $data = $this->input->post();
        $this->account_bo->set_data($data);

        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_login() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['errors'] = $this->account_bo->get_errors();
        } else {
            $user = $this->user_model->find_by_uscmail_and_uscsenh($data['uscmail'], $this->biblivirti_hash->make($data['uscsenh']));
            // Verifica se houve falha na execucao do model
            if (is_null($user)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Usuário não encontrado. VERIFIQUE!";
            } else {
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Usuário não encontrado. VERIFIQUE!";
                $this->response['user'] = $user;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: api/account/login/facebook
     * @param string uscfbid
     * @return JSON
     *
     * Metodo para autenticar um usuario no facebook.
     * Recebe o(s) parametro(s) <i>uscmail</i> e <i>uscsenh</i> atraves de <i>POST</i> e retorna um <i>JSON</i>
     * no seguinte formato:
     * {
     *      "request_code" : "Codigo da requsicao",
     *      "request_message" : "Mensagem da requsicao",
     *      "user" : {
     *          "usnid" : "ID do usuario",
     *          "uscnome" : "Nome do usuario",
     *          "uscmail" : "E-mail do usuario",
     *          "usclogn" : "Login do usuario",
     *          "uscfoto" : "Caminho da foto do usuario",
     *          "uscstat" : "Status do usuario",
     *          "usdcadt" : "Data de cadastro do usuario"
     *      }
     * }
     */
    public function login_facebook() {
        $this->response = [];
        $data = $this->input->post();
        $this->account_bo->set_data($data);

        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_login_facebook() === FALSE) {
            $this->response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $this->response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $this->response['errors'] = $this->account_bo->get_errors();
        } else {
            $user = $this->user_model->find_by_uscfbid($data['uscfbid']);
            // Verifica se houve flaha na execucao do model
            if (is_null($user)) {
                $this->response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $this->response['response_message'] = "Usuário não encontrado. VERIFIQUE!";
            } else {
                $this->response['response_code'] = RESPONSE_CODE_OK;
                $this->response['response_message'] = "Usuário não encontrado. VERIFIQUE!";
                $this->response['user'] = $user;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($this->response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: api/account/register
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
     *      "data" : {
     *          "usnid" : "ID do usuario inserido"
     *      }
     * }
     */
    public function register() {
        $this->response = [];
        $data = $this->input->post();
        $this->account_bo->set_data($data);

        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_register() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['errors'] = $this->account_bo->get_errors();
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
                $response['data'] = ['usnid' => $id];
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: api/account/recover
     * @param string uscmail
     * @return JSON
     *
     * Metodo para recuperar o acesso um usuario.
     * Recebe o(s) parametro(s) <i>uscmail</i> atraves de <i>POST</i>
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta",
     *      "user" : {
     *          "usnid" : "ID do usuario",
     *          "uscnome" : "Nome do usuario",
     *          "uscmail" : "E-mail do usuario",
     *          "usclogn" : "Login do usuario",
     *          "uscfoto" : "Caminho da foto do usuario",
     *          "uscstat" : "Status do usuario",
     *          "usdcadt" : "Data de cadastro do usuario"
     *      }
     * }
     */
    public function recover() {
        $this->response = [];
        $data = $this->input->post();
        $this->account_bo->set_data($data);

        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_recover() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['errors'] = $this->account_bo->get_errors();
        } else {
            $user = $this->user_model->find_by_uscmail($data['uscmail']);
            // verifica se houve falha na execucao do model
            if (is_null($user)) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "O e-mail informado não foi encontrado. VERIFIQUE!\n";
            } else {
                $token['rsnidus'] = $user->usnid;
                $token['rsctokn'] = $this->biblivirti_hash->token($user->uscmail); // Gera token de redefinicao de senha
                $id = $this->recover_model->save($token);
                // Verifica se houve falha na execucao do model
                if (is_null($id)) {
                    $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                    $response['response_message'] = "Houve um erro ao tentar recuperar senha de acesso! Tente novamente.\n";
                    $response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
                } else {
                    // Enviar o email com o link de redefinicao de senha
                    // Confirmar o envio do email com o link de redefinicao de senha
                    $response['response_code'] = RESPONSE_CODE_OK;
                    $response['response_message'] = "E-mail confirmado com sucesso!\n";
                    $response['response_message'] .= "Um link de redefinição de senha foi enviado para seu e-mail!";
                    $response['user'] = $user;
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: api/account/recover
     * @param string uscmail
     * @return JSON
     *
     * Metodo para recuperar o acesso um usuario.
     * Recebe o(s) parametro(s) <i>uscmail</i> atraves de <i>POST</i>
     * e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "response_code" : "Codigo da resposta",
     *      "response_message" : "Mensagem da resposta",
     * }
     */
    public function redefine() {
        $this->response = [];
        $data = $this->input->post();
        $this->account_bo->set_data($data);

        // Verifica se os dados nao foram validados
        if ($this->account_bo->validate_redefine() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['errors'] = $this->account_bo->get_errors();
        } else {
            $token = $this->recover_model->find_by_rsctokn($data['rsctokn']);
            if(is_null($token)) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "TOKEN DE REDEFINIÇÃO inválido!";
            } else {
                $user = $this->user_model->find_by_usnid($token->rsnidus);
                $token->rscstat = RSCSTAT_INATIVO;
                $this->recover_model->save($token);
                $data['user'] = $user;
                $data['intent_category'] = INTENT_CATEGORY_ACCOUNT;
                $data['intent_action'] = INTENT_ACTION_ACCOUNT_REDEFINE;
                $response['response_code'] = RESPONSE_CODE_OK;
                $response['response_message'] = "Senha redefinida com sucesso!";
                $response['data'] = $data;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
}