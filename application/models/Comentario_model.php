<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 02/09/2016
 *
 * Model da tabela <b>COMENTARIO</b>
 */
class Comentario_model extends CI_Model {

	/**
     * Comentario_model constructor.
     */
    public function __construct() {
        parent::__construct();
	}

    /**
     * @param $cenid
     * @return mixed
     *
     * Metodo para buscar um Comentario pelo campo cenid (ID).
     */
    public function find_by_cenid($cenid) {
        $this->db->where(['cenid' => $cenid]);
        $query = $this->db->get('comentario');
        return ($query->num_rows() > 0) ? $query->result()[0] : null;
    }

    /**
     * @param $manid
     * @return mixed
     *
     * Metodo para buscar a quantidade de comentarios relacionados com um determinado material.
     */
    public function find_count_by_manid($manid) {
        $this->db->select('cenid');
        $this->db->from('comentario');
        $this->db->join('material', 'cenidma = manid', 'inner');
        $this->db->where(['manid' => $manid]);
        return $this->db->get()->num_rows();
    }

}