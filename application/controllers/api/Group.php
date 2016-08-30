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
        $this->load->model("group_model");

        // Loading libraries
        $this->load->library('group_bo');
    }

    /**
     * @url: API/group/list
     * @param int usnid
     * @return JSON
     *
     * Metodo para listar todos os grupos de um determinado usuario.
     * Recebe o parametro <i>usnid</i> atraves de <i>POST</i> e retorna um <i>JSON</i> no seguinte formato:
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
     *              "area_of_interest" : {
     *                  "ainid" : "ID da area de interasse",
     *                  "aicdesc" : "Descricao da area de interesse"
     *              },
     *              "admin" : {
     *                  "usnid" : "ID do usuario",
     *                  "uscnome" : "Nome do usuario",
     *                  "uscmail" : "E-mail do usuario",
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
        $data['usnid'] = $this->input->post('usnid');

        $this->response = [];
        $this->group_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->group_bo->validate_list_all() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->group_bo->get_errors();
        } else {
            $groups = $this->group_model->find_by_usnid($data['usnid']);
            // verifica se houve falha na execucao do model
            if (is_null($groups)) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "Nenhum grupo foi encontrado. VERIFIQUE!";
            } else {
                $response['response_code'] = RESPONSE_CODE_OK;
                $response['response_message'] = "Grupo(s) encontrado(s) com sucesso!";
                $response['response_data'] = $groups;
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: api/group/add
     * @param string grcfoto
     * @param string grcnome
     * @param string grnidai
     * @param string grctipo
     * @return JSON
     *
     * Metodo para salva um novo grupo.
     * Recebe os parametros <i>grcfoto</i>, <i>grcnome</i>, <i>grnidai</i> e <i>grctipo</i> atraves de <i>POST</i>
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
        $data['grcnome'] = $this->input->post('grcnome');
        $data['grctipo'] = $this->input->post('grctipo');
        $data['grnidai'] = $this->input->post('grnidai');
        $data['usnid'] = $this->input->post('usnid');

        $this->response = [];
        $this->group_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->group_bo->validate_add() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->group_bo->get_errors();
        } else {
            $id = $this->group_model->save($data);
            // verifica se houve falha na execucao do model
            if ($id === 0) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "Houve um erro ao tentar salvar as informações do grupo! Tente novamente.\n";
                $response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti!";
            } else {
                $response['response_code'] = RESPONSE_CODE_OK;
                $response['response_message'] = "Grupo cadastrado com sucesso!";
                $response['response_data'] = ['grnid' => $id];
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: api/group/edit
     * @param string grnid
     * @param string grcfoto
     * @param string grcnome
     * @param string grnidai
     * @param string grctipo
     * @return JSON
     *
     * Metodo para editar um grupo.
     * Recebe os parametros <i>grnid</i>, <i>grcfoto</i>, <i>grcnome</i>, <i>grnidai</i> e <i>grctipo</i> atraves
     * de <i>POST</i> e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "request_code" : "Codigo da requsicao",
     *      "request_message" : "Mensagem da requsicao",
     * }
     */
    public function edit() {
        $data['grnid'] = $this->input->post('grnid');
        $data['grcnome'] = $this->input->post('grcnome');
        $data['grctipo'] = $this->input->post('grctipo');
        $data['grnidai'] = $this->input->post('grnidai');

        $this->response = [];
        $this->group_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->group_bo->validate_edit() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->group_bo->get_errors();
        } else {
            $group = $this->group_model->find_by_id($data['grnid']);
            // verifica se houve falha na execucao do model
            if (is_null($group)) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "Grupo não encontrado. VERIFIQUE!";
            } else {
                $this->group_model->save($data);
                $response['response_message'] = "Grupo atualizado com sucesso!";
                $response['response_data'] = ['grnid' => $data['grnid']];
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }


    /**
     * @url: api/group/delete
     * @param int grnid
     * @param int usnid
     * @return JSON
     *
     * Metodo para detetar um grupo.
     * Recebe o(s) parametro(s) <i>grnid</i> e <i>usnid</i> atraves de <i>POST</i> e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "request_code" : "Codigo da requsicao",
     *      "request_message" : "Mensagem da requsicao",
     * }
     */
    public function delete() {
        $data['grnid'] = $this->input->post('grnid');
        $data['usnid'] = $this->input->post('usnid');

        $this->response = [];
        $this->group_bo->set_data($data);
        // Verifica se os dados nao foram validados
        if ($this->group_bo->validate_delete() === FALSE) {
            $response['response_code'] = RESPONSE_CODE_BAD_REQUEST;
            $response['response_message'] = "Dados não informados e/ou inválidos. VERIFIQUE!";
            $response['response_errors'] = $this->group_bo->get_errors();
        } else {
            $group = $this->group_model->find_by_grnid($data['grnid']);
            // Verifica o grupo foi encontrado
            if (is_null($group)) {
                $response['response_code'] = RESPONSE_CODE_NOT_FOUND;
                $response['response_message'] = "Grupo não encontrado. VERIFIQUE!";
            } else {
                $admin = $this->group_model->find_group_admin($data['grnid']);
                // Verifica se o usuario eh administrador do grupo
                if ($admin->usnid !== $data['usnid']) {
                    $response['response_code'] = RESPONSE_CODE_UNAUTHORIZED;
                    $response['response_message'] = "Erro ao tentar excluir as informações do grupo!\n";
                    $response['response_message'] .= "Somente o administrador do grupo poderá excluí-lo!";
                } else {
                    if ($this->group_model->delete($data['grnid']) === false) {
                        $response['response_code'] = RESPONSE_CODE_OK;
                        $response['response_message'] = "Houve um erro ao tentar excluir as informações do grupo!\nTente novamente!";
                        $response['response_message'] .= "Se o erro persistir, entre em contato com a equipe de suporte do Biblivirti.";
                    } else {
                        $response['response_code'] = RESPONSE_CODE_OK;
                        $response['response_message'] = "Grupo excluído com sucesso!";
                    }
                }
            }
        }

        $this->output->set_content_type('application/json', 'UTF-8');
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * @url: api/group/info
     * @param int grnid
     * @return JSON
     *
     * Metodo para mostrar as informacoes de um grupo.
     * Recebe o parametro <i>grnid</i> atraves de <i>POST</i> e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "request_code" : "Codigo da requsicao",
     *      "request_message" : "Mensagem da requsicao",
     * }
     */
    public function info() {

    }

}