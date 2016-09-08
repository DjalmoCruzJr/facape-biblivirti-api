<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 04/09/2016
 *
 * Model da tabela <b>CONTEUDO</b>
 */
class Conteudo_model extends CI_Model {

    /**
     * @param $conid
     * @return mixed
     *
     * Metodo para buscar um conteudo pelo campo conid (ID).
     */
    public function find_by_conid($conid) {
        $this->db->where(['conid' => $conid]);
        $query = $this->db->get('conteudo');
        return ($query->num_rows() > 0) ? $query->result()[0] : null;
    }

}