<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 31/08/2016
 *
 * Model da tabela <b>AREAINTERESSE</b>
 */
class Areainteresse_model extends CI_Model {

	/**
     * Areainteresse_model constructor.
     */
    public function __construct() {
        parent::__construct();
	}

    /**
     * @param $ainid
     * @return mixed
     *
     * Metodo para buscar uma Area de Interesse pelo campo ainid (ID).
     */
    public function find_by_ainid($ainid) {
        $this->db->where(['ainid' => $ainid]);
        $query = $this->db->get('areainteresse');
        return ($query->num_rows() > 0) ? $query->result()[0] : null;
    }

    /**
     * @param $aicdesc
     * @return mixed
     *
     * Metodo para buscar uma Area de Interesse pelo campo aicdesc (Descricao).
     * Formato da busca: 'field' LIKE 'value'
     */
    public function find_by_aicdesc($aicdesc) {
        $this->db->like('aicdesc', $aicdesc, 'both');
        $query = $this->db->get('areainteresse');
        return ($query->num_rows() > 0) ? $query->result() : null;
    }
}