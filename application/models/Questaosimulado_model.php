<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 04/09/2016
 *
 * Model da tabela <b>QUESTAOSIMULADO</b>
 */
class Questaosimulado_model extends CI_Model {

    /**
     * Questaosimulado_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }


    /**
     * @param $data
     * @return mixed
     *
     * Metodo para salvar a relacao entre uma questao e um material (Simulado).
     */
    public function save($data) {
        return $this->db->insert('questaosimulado', $data);
    }

    /**
     * @param $mani
     * @return mixed
     *
     * Metodo para buscar todas as questoes relacioandas com um determinado material (SIMULADO).
     */
    public function find_questions_by_manid($manid) {
        $this->db->select('qenid, qectext, qelanex, qecanex, qedcadt, qedaldt');
        $this->db->from('questao');
        $this->db->join('questaosimulado', 'qsnidqe = qenid', 'inner');
        $this->db->join('material', 'qsnidma = manid', 'inner');
        $this->db->where(['manid' => $manid]);
        $this->db->order_by('qenid', 'ASC');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : null;
    }

    /**
     * @param $manid
     * @return mixed
     *
     * Metodo para deletar todas as questoes relacionadas com um deteminado material (SIMULADO)
     */
    public function delete_by_manid($manid) {
        $this->db->where(['qsnidma' => $manid]);
        return $this->db->delete('questaosimulado');
    }

}