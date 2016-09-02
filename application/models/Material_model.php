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

    /**
     * @param $grnid
     * @return mixed
     *
     * Metodo para buscar os materiais relacionado com um determinado grupo passado pelo parametro grnid(ID do grupo).
     */
    public function find_by_grnid($grnid, $limite = 1000, $offset = 0) {
        $this->db->select('manid, macdesc, mactipo, malanex, macurl, macnivl, macstat, madcadt, madaldt');
        $this->db->from('material');
        $this->db->join('grupomaterial', 'gmnidma = manid', 'inner');
        $this->db->join('grupo', 'gmnidgr = grnid', 'inner');
        $this->db->where(['grnid' => $grnid]);
        $this->db->order_by('madcadt, macdesc', 'DESC');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : null;
    }

}