<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 04/09/2016
 *
 * Model da tabela <b>CONTEUDO</b>
 */
class Conteudo_model extends CI_Model {

    /**
     * Conteudo_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $data
     * @return int
     *
     * Metodo para salvar um registro na tabela CONTEUDO
     */
    public function save($data) {
        return $this->db->insert('conteudo', $data) === true ? $this->db->insert_id() : 0;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar todos os registros da tabela CONTEUDO
     */
    public function find_all($limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $query = $this->db->get('conteudo', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $conid
     * @return mixed
     *
     * Metodo para buscar um registro da tabela CONTEUDO pelo ID
     */
    public function find_by_conid($conid) {
        $this->db->where('conid', $conid);
        $query = $this->db->get('conteudo');
        return $query->num_rows() > 0 ? $query->result()[0] : null;
    }

    /**
     * @param $cocdesc
     * @param int $limit
     * @param int $offset
     * @param bool $like
     * @return mixed
     *
     * Metodo para buscar registros da tabela CONTEUDO pela descricao
     * Se $like = TRUE a busca eh feita no formato: field LIKE value
     * Se $like = FALSE a busca eh feita no formato: field = value
     */
    public function find_by_cocdesc($cocdesc, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $like = true) {
        if ($like === true) {
            $this->db->like('cocdesc', $cocdesc);
        } else {
            $this->db->where('cocdesc', $cocdesc);
        }
        $query = $this->db->get('conteudo', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $grnid
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar registros da tabela CONTEUDO relacionado com um GRUPO
     */
    public function find_by_grnid($grnid, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $this->db->where('conidgr', $grnid);
        $query = $this->db->get('conteudo', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $data
     * @return int
     *
     * metodo para atualizar um registro da tabela CONTEUDO
     */
    public function update($data) {
        $conid = $data['conid'];
        unset($data['conid']);
        $this->db->where('conid', $conid);
        return $this->db->update('conteudo', $data) === true ? $conid : 0;
    }

    /**
     * @param $conid
     * @return bool
     *
     * Metodo para excluir um registro da tabela CONTEUDO
     */
    public function delete($conid) {
        $this->db->where('conid', $conid);
        return $this->db->delete('conteudo');
    }

}