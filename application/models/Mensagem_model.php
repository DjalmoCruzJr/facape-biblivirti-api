<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 02/10/2016
 *
 * Model da tabela <b>MENSAGEM</b>
 */
class Mensagem_model extends CI_Model {

    /**
     * Mensagem_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $data
     * @return int
     *
     * Metodo para salvar um registro na tabela MENSAGEN
     */
    public function save($data) {
        return $this->db->insert('mensagem', $data) === true ? $this->db->insert_id() : 0;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar todos os registros da tabela ALTERNATIVA
     */
    public function find_all($limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $query = $this->db->get('mensagem', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $alnid
     * @return mixed
     *
     * Metodo para buscar um registro da tabela MENSAGEN pelo ID
     */
    public function find_by_alnid($alnid) {
        $this->db->where('msnid', $alnid);
        $query = $this->db->get('alternativa');
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $msnidus
     * @return mixed
     *
     * Metodo para buscar os registros da tabela MENSAGEN relacionados com um GRUPO
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_msnidus($msnidgr, $active = false) {
        if ($active = true) {
            $this->db->where('mscstat', MSCSTAT_ATIVO);
        }
        $this->db->where('msnidgr', $msnidgr);
        $query = $this->db->get('mensagem');
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $msctext
     * @param int $limit
     * @param int $offset
     * @param bool $like
     * @return mixed
     *
     * Metodo para buscar registros da tabela MENSAGEN pela TEXTO
     * Se $like = TRUE a busca eh feita no formato: field LIKE value
     * Se $like = FALSE a busca eh feita no formato: field = value
     */
    public function find_by_msctext($msctext, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $like = true) {
        if ($like === true) {
            $this->db->like('msctext', $msctext);
        } else {
            $this->db->where('msctext', $msctext);
        }
        $query = $this->db->get('mensagem', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $data
     * @return int
     *
     * metodo para atualizar um registro da tabela MENSAGEN
     */
    public function update($data) {
        $msnid = $data['msnid'];
        unset($data['msnid']);
        $this->db->where('msnid', $msnid);
        return $this->db->update('mensagem', $data) === true ? $msnid : 0;
    }

    /**
     * @param $msnid
     * @return bool
     *
     * Metodo para excluir um registro da tabela MENSAGEN
     */
    public function delete($msnid) {
        $this->db->where('msnid', $msnid);
        return $this->db->delete('mensagem');
    }

}