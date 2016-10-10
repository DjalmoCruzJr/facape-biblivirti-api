<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 07/09/2016
 *
 * Model da tabela <b>QUESTAO</b>
 */
class Questao_model extends CI_Model {

    /**
     * Questao_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $data
     * @return int
     *
     * Metodo para salvar um registro na tabela QUESTAO
     */
    public function save($data) {
        return $this->db->insert('questao', $data) === true ? $this->db->insert_id() : 0;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar todos os registros da tabela QUESTAO
     */
    public function find_all($limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $query = $this->db->get('questao', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $qenid
     * @return mixed
     *
     * Metodo para buscar um registro da tabela QUESTAO pelo ID
     */
    public function find_by_qenid($qenid) {
        $this->db->where('qenid', $qenid);
        $query = $this->db->get('questao');
        return $query->num_rows() > 0 ? $query->result()[0] : null;
    }

    /**
     * @param $qecdesc
     * @param int $limit
     * @param int $offset
     * @param bool $like
     * @return mixed
     *
     * Metodo para buscar os registros da tabela QUESTAO pela DESCRICAO
     * Se $like = TRUE a busca sera feita no formato: fields LIKE value
     * Se $like = FALSE a busca sera feita no formato: fields = value
     */
    public function find_by_qecdesc($qecdesc, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $like = true) {
        if ($like === true) {
            $this->db->like('qecdesc', $qecdesc);
        } else {
            $this->db->where('qecdesc', $qecdesc);
        }
        $query = $this->db->get('questao', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $qectext
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar os registros da tabela QUESTAO pelo TEXTO
     * Se $like = TRUE a busca sera feita no formato: fields LIKE value
     * Se $like = FALSE a busca sera feita no formato: fields = value
     */
    public function find_by_qectext($qectext, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $like = true) {
        if ($like === true) {
            $this->db->like('qectext', $qectext);
        } else {
            $this->db->where('qectext', $qectext);
        }
        $query = $this->db->get('questao', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $data
     * @return int
     *
     * metodo para atualizar um registro da tabela QUESTAO
     */
    public function update($data) {
        $qenid = $data['qenid'];
        unset($data['qenid']);
        $this->db->where('qenid', $qenid);
        return $this->db->update('questao', $data) === true ? $qenid : 0;
    }

    /**
     * @param $qenid
     * @return bool
     *
     * Metodo para excluir um registro da tabela QUESTAO
     */
    public function delete($qenid) {
        $this->db->where('qenid', $qenid);
        return $this->db->delete('questao');
    }

}