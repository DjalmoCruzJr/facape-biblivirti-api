<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 02/10/2016
 *
 * Model da tabela <b>RESPOSTA</b>
 */
class Resposta_model extends CI_Model {

    /**
     * Resposta_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $data
     * @return int
     *
     * Metodo para salvar um registro na tabela RESPOSTA
     */
    public function save($data) {
        return $this->db->insert('resposta', $data) === true ? $this->db->insert_id() : 0;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar todos os registros da tabela RESPOSTA
     */
    public function find_all($limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $query = $this->db->get('resposta', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $renid
     * @return mixed
     *
     * Metodo para buscar um registro da tabela RESPOSTA pelo ID
     */
    public function find_by_renid($renid) {
        $this->db->where('renid', $renid);
        $query = $this->db->get('resposta');
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $renidav
     * @return mixed
     *
     * Metodo para buscar os registros da tabela RESPOSTA relacionados com uma AVALIACAO
     */
    public function find_by_renidav($renidav, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $this->db->where('renidav', $renidav);
        $query = $this->db->get('resposta', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $renidal
     * @return mixed
     *
     * Metodo para buscar os registros da tabela RESPOSTA relacionados com uma ALTERNATIVA
     */
    public function find_by_renidal($renidal, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $this->db->where('renidqe', $renidal);
        $query = $this->db->get('resposta', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $recstat
     * @return mixed
     *
     * Metodo para buscar os registros da tabela RESPOSTA relacionados pelo STATUS
     */
    public function find_by_recstat($recstat, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $this->db->where('recstat', $recstat);
        $query = $this->db->get('resposta', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $renidav
     * @param $renidal
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar os registros da tabela RESPOSTA relacionados com uma AVALIACAO e uma ALTERNATIVA
     */
    public function find_by_renidav_and_renidal($renidav, $renidal, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $this->db->where('renidav', $renidav);
        $this->db->where('renidal', $renidal);
        $query = $this->db->get('resposta', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $renidav
     * @param $recstat
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar os registros da tabela RESPOSTA relacionados com uma AVALIACAO pelo STATUS
     */
    public function find_by_renidav_and_recstat($renidav, $recstat, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $this->db->where('renidav', $renidav);
        $this->db->where('recstat', $recstat);
        $query = $this->db->get('resposta', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $renidal
     * @param $recstat
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar os registros da tabela RESPOSTA relacionados com uma ALTERNATIVA pelo STATUS
     */
    public function find_by_renidal_and_recstat($renidal, $recstat, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $this->db->where('renidal', $renidal);
        $this->db->where('recstat', $recstat);
        $query = $this->db->get('resposta', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $renidav
     * @param $renidal
     * @param $recstat
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar os registros da tabela RESPOSTA relacionados com uma AVALIACAO e uma ALTERNATIVA pelo STATUS
     */
    public function find_by_renidav_and_renidal_and_recstat($renidav, $renidal, $recstat, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $this->db->where('renidav', $renidav);
        $this->db->where('renidal', $renidal);
        $this->db->where('recstat', $recstat);
        $query = $this->db->get('resposta', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $data
     * @return int
     *
     * metodo para atualizar um registro da tabela RESPOSTA
     */
    public function update($data) {
        $renid = $data['renid'];
        unset($data['renid']);
        $this->db->where('renid', $renid);
        return $this->db->update('resposta', $data) === true ? $renid : 0;
    }

    /**
     * @param $avnid
     * @return bool
     *
     * Metodo para excluir um registro da tabela RESPOSTA
     */
    public function delete($renid) {
        $this->db->where('renid', $renid);
        return $this->db->delete('resposta');
    }

}