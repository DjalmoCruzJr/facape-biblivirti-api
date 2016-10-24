<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 02/10/2016
 *
 * Model da tabela <b>AVALIACAO</b>
 */
class Avaliacao_model extends CI_Model {

    /**
     * Avaliacao_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $data
     * @return int
     *
     * Metodo para salvar um registro na tabela AVALIACAO
     */
    public function save($data) {
        return $this->db->insert('avaliacao', $data) === true ? $this->db->insert_id() : 0;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar todos os registros da tabela AVALIACAO
     */
    public function find_all($limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $query = $this->db->get('avaliacao', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $avnid
     * @return mixed
     *
     * Metodo para buscar um registro da tabela AVALIACAO pelo ID
     */
    public function find_by_avnid($avnid) {
        $this->db->where('avnid', $avnid);
        $query = $this->db->get('avaliacao');
        return $query->num_rows() > 0 ? $query->result()[0] : null;
    }

    /**
     * @param $avnidus
     * @return mixed
     *
     * Metodo para buscar os registros da tabela AVALIACAO relacionados com um USUARIO
     */
    public function find_by_avnidus($avnidus, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $this->db->where('avnidus', $avnidus);
        $query = $this->db->get('avaliacao', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $avnidma
     * @return mixed
     *
     * Metodo para buscar os registros da tabela AVALIACAO relacionados com um MATERIAL (SIMULADO)
     */
    public function find_by_avnidma($avnidma, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $this->db->where('avnidma', $avnidma);
        $query = $this->db->get('avaliacao', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $avcstat
     * @return mixed
     *
     * Metodo para buscar os registros da tabela AVALIACAO pelo STATUS
     */
    public function find_by_avcstat($avcstat, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $this->db->where('avcstat', $avcstat);
        $query = $this->db->get('avaliacao', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $avnidus
     * @param $avnidma
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar os registros da tabela AVALIACAO relacionados com um USUARIO e um MATERIAL (SIMULADO)
     */
    public function find_by_avnidus_and_avnidma($avnidus, $avnidma, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $this->db->where('avnidus', $avnidus);
        $this->db->where('avnidma', $avnidma);
        $query = $this->db->get('avaliacao', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $avnidus
     * @param $avcstat
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar os registros da tabela AVALIACAO relacionados com um USUARIO pelo STATUS
     */
    public function find_by_avnidus_and_avcstat($avnidus, $avcstat, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $this->db->where('avnidus', $avnidus);
        $this->db->where('avcstat', $avcstat);
        $query = $this->db->get('avaliacao', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $avnidma
     * @param $avcstat
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar os registros da tabela AVALIACAO relacionados com um MATERIAL (SIMULADO) pelo STATUS
     */
    public function find_by_avnidma_and_avcstat($avnidma, $avcstat, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $this->db->where('avnidma', $avnidma);
        $this->db->where('avcstat', $avcstat);
        $query = $this->db->get('avaliacao', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $avnidus
     * @param $avnidma
     * @param $avcstat
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar os registros da tabela AVALIACAO relacionados com um USUARIO e um MATERIAL (SIMULADO) pelo STATUS
     */
    public function find_by_avnidus_and_avnidma_and_avcstat($avnidus, $avnidma, $avcstat, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $this->db->where('avnidus', $avnidus);
        $this->db->where('avnidma', $avnidma);
        $this->db->where('avcstat', $avcstat);
        $query = $this->db->get('avaliacao', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $data
     * @return int
     *
     * metodo para atualizar um registro da tabela AVALIACAO
     */
    public function update($data) {
        $avnid = $data['avnid'];
        unset($data['avnid']);
        $this->db->where('avnid', $avnid);
        return $this->db->update('avaliacao', $data) === true ? $avnid : 0;
    }

    /**
     * @param $avnid
     * @return bool
     *
     * Metodo para excluir um registro da tabela AVALIACAO
     */
    public function delete($avnid) {
        $this->db->where('avnid', $avnid);
        return $this->db->delete('avaliacao');
    }

}