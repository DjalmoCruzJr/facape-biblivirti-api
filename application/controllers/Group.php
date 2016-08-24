<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 28/08/2016
 *
 * Controller para gerenciar acesso aos dados de <b>Grupos</b>
 */
class Group extends CI_Controller {

    /**
     * @url: api/group/all
     * @param int usnid
     * @return json
     *
     * Metodo para listar todos os grupos de um determinado usuario.
     * Recebe o parametro <i>usnid</i> atraves de <i>POST</i> e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "request_code" : "Codigo da requsicao",
     *      "request_message" : "Mensagem da requsicao",
     *      "groups" : [
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
     *          }
     *      ]
     * }
     */
    public function list_all() {}

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
     * }
     *
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
    public function add_edit() {}

    /**
     * @url: api/group/delete
     * @param int grnid
     * @return JSON
     *
     * Metodo para detetar um grupo.
     * Recebe o parametro <i>grnid</i> atraves de <i>POST</i> e retorna um <i>JSON</i> no seguinte formato:
     * {
     *      "request_code" : "Codigo da requsicao",
     *      "request_message" : "Mensagem da requsicao",
     * }
     */
    public function delete() {}

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
    public function info() {}

}