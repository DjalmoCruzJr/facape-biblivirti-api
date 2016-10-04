<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 28/08/2016
 *
 * Model da tabela <b>GRUPO</b>
 */
class Grupo_model extends CI_Model {

    /**
     * Grupo_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $data
     * @return int
     *
     * Metodo para salvar um registro na tabela GRUPO
     */
    public function save($data) {
        return $this->db->insert('grupo', $data) === true ? $this->db->insert_id() : 0;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar todos os registros da tabela GRUPO
     * Se $active = TRUE a busca trara apenas registro com status ATIVO
     */
    public function find_all($limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('grcstat', GRCSTAT_ATIVO);
        }
        $query = $this->db->get('grupo', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $grnid
     * @return mixed
     *
     * Metodo para buscar um registro da tabela GRUPO pelo ID
     */
    public function find_by_grnid($grnid) {
        $this->db->where('grnid', $grnid);
        $query = $this->db->get('grupo');
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $grcnome
     * @param int $limit
     * @param int $offset
     * @param bool $like
     * @return mixed
     *
     * Metodo para buscar registros da tabela GRUPO pela NOME
     * Se $like = TRUE a busca eh feita no formato: field LIKE value
     * Se $like = FALSE a busca eh feita no formato: field = value
     * Se $active = TRUE a busca trara apenas registro com status ATIVO
     */
    public function find_by_grcnome($grcnome, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $like = true, $active = false) {
        if ($like === true) {
            $this->db->like('grcnome', $grcnome);
        } else {
            $this->db->where('grcnome', $grcnome);
        }
        if ($active === true) {
            $this->db->where('grcstat', GRCSTAT_ATIVO);
        }
        $query = $this->db->get('grupo', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $grnidai
     * @param int $limit
     * @param int $offset
     * @param bool $like
     * @return mixed
     *
     * Metodo para buscar registros da tabela GRUPO relacionados com uma AREA DE INTERESSE
     * Se $like = TRUE a busca eh feita no formato: field LIKE value
     * Se $like = FALSE a busca eh feita no formato: field = value
     * Se $active = TRUE a busca trara apenas registro com status ATIVO
     */
    public function find_by_grnidai($grnidai, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('grcstat', GRCSTAT_ATIVO);
        }
        $this->db->join('areainteresse', 'ainid = grnidai', 'inner');
        $this->db->where('grnidai', $grnidai);
        $query = $this->db->get('grupo', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $grctipo
     * @param int $limit
     * @param int $offset
     * @param bool $like
     * @return mixed
     *
     * Metodo para buscar registros da tabela GRUPO pela TIPO
     * Se $like = FALSE a busca eh feita no formato: field = value
     * Se $active = TRUE a busca trara apenas registro com status ATIVO
     */
    public function find_by_grctipo($grctipo, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('grcstat', GRCSTAT_ATIVO);
        }
        $this->db->where('grctipo', $grctipo);
        $query = $this->db->get('grupo', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $data
     * @return int
     *
     * metodo para atualizar um registro da tabela GRUPO
     */
    public function update($data) {
        $grnid = $data['grnid'];
        unset($data['grnid']);
        $this->db->where('grnid', $grnid);
        return $this->db->update('grupo', $data) === true ? $grnid : 0;
    }

    /**
     * @param $grnid
     * @return bool
     *
     * Metodo para excluir um registro da tabela GRUPO
     */
    public function delete($grnid) {
        $this->db->where('grnid', $grnid);
        return $this->db->delete('grupo');
    }

}