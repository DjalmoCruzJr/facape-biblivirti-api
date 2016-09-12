<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 05/09/2016
 *
 * Model da tabela <b>CONTEUDOMATERIAL</b>
 */
class Conteudomaterial_model extends CI_Model {

    /**
     * Conteudomaterial_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $cenid
     * @return mixed
     *
     * Metodo para salvar uma relação entre um CONTEUDO e um MATERIAL.
     */
    public function save($data) {
        return $this->db->insert('conteudomaterial', $data);
    }

    /**
     * @param $manid
     * @return mixed
     *
     * Metodo para buscar os conteudos relacionados com um determinado material (NAO SIMULADO).
     */
    public function find_contents_by_manid($manid) {
        $this->db->select('conid, cocdesc, codcadt, codaldt');
        $this->db->from('conteudo');
        $this->db->join('conteudomaterial', 'cmnidco = conid', 'inner');
        $this->db->join('material', 'cmnidma = manid', 'inner');
        $this->db->where(['manid' => $manid]);
        $this->db->order_by('conid, cocdesc', 'ASC');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : null;
    }

    /**
     * @param $manid
     * @return mixed
     *
     * Metodo para deletar todos conteudos relacionados com um determinado material (NAO SIMULADO).
     */
    public function delete_by_manid($manid) {
        $this->db->where(['cmnidma' => $manid]);
        return $this->db->delete('conteudomaterial');
    }


}