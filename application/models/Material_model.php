<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 31/08/2016
 *
 * Model da tabela <b>MATERIAL</b>
 */
class Material_model extends CI_Model {

    /**
     * @param $manid
     * @return mixed
     *
     * Metodo para buscar uma Material pelo campo manid (ID).
     */
    public function find_by_manid($manid) {
        $this->db->where(['manid' => $manid]);
        $query = $this->db->get('material');
        return ($query->num_rows() > 0) ? $query->result()[0] : null;
    }

    /**
     * @param $macdesc
     * @return mixed
     *
     * Metodo para buscar uma Material pelo campo macdesc (Descricao).
     * Formato da busca: 'field' LIKE 'value'
     */
    public function find_by_macdesc($macdesc) {
        $this->db->like('macdesc', $macdesc, 'both');
        $query = $this->db->get('material');
        return ($query->num_rows() > 0) ? $query->result() : null;
    }

}