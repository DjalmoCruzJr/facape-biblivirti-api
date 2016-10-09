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
     * @param $data
     * @return int
     *
     * Metodo para salvar um registro na tabela AREAINTERESSE
     */
    public function save($data) {
        return $this->db->insert('areainteresse', $data) === true ? $this->db->insert_id() : 0;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar todos os registros da tabela AREAINTERESSE
     */
    public function find_all($limit = 10, $offset = 0) {
        $query = $this->db->get('areainteresse', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $ainid
     * @return mixed
     *
     * Metodo para buscar um registro da tabela AREAINTERESSE pelo ID
     */
    public function find_by_ainid($ainid) {
        $this->db->where('ainid', $ainid);
        $query = $this->db->get('areainteresse');
        return $query->num_rows() > 0 ? $query->result()[0] : null;
    }

    /**
     * @param $aicdesc
     * @param int $limit
     * @param int $offset
     * @param bool $like
     * @return mixed
     *
     * Metodo para buscar registros da tabela AREAINTERSSE pela descricao
     * Se $like = TRUE a busca eh feita no formato: field LIKE value
     * Se $like = FALSE a busca eh feita no formato: field = value
     */
    public function find_by_aicdesc($aicdesc, $limit = 10, $offset = 0, $like = true) {
        if ($like === true) {
            $this->db->like('aicdesc', $aicdesc);
        } else {
            $this->db->where('aicdesc', $aicdesc);
        }
        $query = $this->db->get('areainteresse', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $data
     * @return int
     *
     * metodo para atualizar um registro da tabela AREAINTERESSE
     */
    public function update($data) {
        $ainid = $data['ainid'];
        unset($data['ainid']);
        $this->db->where('ainid', $ainid);
        return $this->db->update('areainteresse', $data) === true ? $ainid : 0;
    }

    /**
     * @param $ainid
     * @return bool
     *
     * Metodo para excluir um registro da tabela AREAINTERESSE
     */
    public function delete($ainid) {
        $this->db->where('ainid', $ainid);
        return $this->db->delete('areainteresse');
    }

}