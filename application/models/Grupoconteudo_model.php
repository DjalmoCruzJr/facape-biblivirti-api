<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 11/09/2016
 *
 * Model da tabela <b>GRUPOCONTEUDO</b>
 */
class Grupoconteudo_model extends CI_Model {

    /**
     * Grupoconteudo_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $cenid
     * @return mixed
     *
     * Metodo para salvar uma relação entre um GRUPO e um CONTEUDO.
     */
    public function save($data) {
        return $this->db->insert('grupoconteudo', $data);
    }

    /**
     * @param $grnid
     * @return mixed
     *
     * Metodo para deletar todos conteudos relacionados com um determinado grupo.
     */
    public function delete_by_grnid($grnid) {
        $this->db->where(['gcnidgr' => $grnid]);
        return $this->db->delete('grupoconteudo');
    }

}