<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 02/10/2016
 *
 * Model da tabela <b>ALTERNATIVA</b>
 */
class Alternativa_model extends CI_Model {

    /**
     * Alternativa_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $data
     * @return int
     *
     * Metodo para salvar um registro na tabela ALTERNATIVA
     */
    public function save($data) {
        return $this->db->insert('alternativa', $data) === true ? $this->db->insert_id() : 0;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar todos os registros da tabela ALTERNATIVA
     */
    public function find_all($limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $query = $this->db->get('alternativa', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $alnid
     * @return mixed
     *
     * Metodo para buscar um registro da tabela ALTERNATIVA pelo ID
     */
    public function find_by_alnid($alnid) {
        $this->db->where('alnid', $alnid);
        $query = $this->db->get('alternativa');
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $alnidqe
     * @return mixed
     *
     * Metodo para buscar os registros da tabela ALTERNATIVA relacionados com uma QUESTAO
     */
    public function find_by_alnidqe($alnidqe) {
        $this->db->where('alnidqe', $alnidqe);
        $query = $this->db->get('alternativa');
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $alctext
     * @param int $limit
     * @param int $offset
     * @param bool $like
     * @return mixed
     *
     * Metodo para buscar registros da tabela ALTERNATIVA pela TEXTO
     * Se $like = TRUE a busca eh feita no formato: field LIKE value
     * Se $like = FALSE a busca eh feita no formato: field = value
     */
    public function find_by_alctext($alctext, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $like = true) {
        if ($like === true) {
            $this->db->like('alctext', $alctext);
        } else {
            $this->db->where('alctext', $alctext);
        }
        $query = $this->db->get('alternativa', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $data
     * @return int
     *
     * metodo para atualizar um registro da tabela ALTERNATIVA
     */
    public function update($data) {
        $alnid = $data['alnid'];
        unset($data['alnid']);
        $this->db->where('alnid', $alnid);
        return $this->db->update('alternativa', $data) === true ? $alnid : 0;
    }

    /**
     * @param $alnid
     * @return bool
     *
     * Metodo para excluir um registro da tabela ALTERNATIVA
     */
    public function delete($alnid) {
        $this->db->where('alnid', $alnid);
        return $this->db->delete('alternativa');
    }

}