<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 02/09/2016
 *
 * Model da tabela <b>COMENTARIO</b>
 */
class Comentario_model extends CI_Model {

    /**
     * Comentario_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $data
     * @return int
     *
     * Metodo para salvar um registro na tabela COMENTARIO
     */
    public function save($data) {
        return $this->db->insert('comentario', $data) === true ? $this->db->insert_id() : 0;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar todos os registros da tabela COMENTARIO
     * Se $ative = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_all($limit = 10, $offset = 0, $active = false) {
        if ($active === true) {
            $this->db->where('cecstat', CECSTAT_ATIVO);
        }
        $this->db->where('cenidce', null);
        $query = $this->db->get('comentario', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $cenid
     * @return mixed
     *
     * Metodo para buscar um registro da tabela COMENTARIO pelo ID
     */
    public function find_by_cenid($cenid) {
        $this->db->where('cenid', $cenid);
        $query = $this->db->get('comentario');
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $cenidma
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela COMENTARIO relacionados com um MATERIAL
     * Se $ative = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_cenidma($cenidma, $limit = 10, $offset = 0, $active = false) {
        if ($active === true) {
            $this->db->where('cecstat', CECSTAT_ATIVO);
        }
        $this->db->where('cenidce', null);
        $this->db->where('cenidma', $cenidma);
        $query = $this->db->get('comentario', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $cenidgr
     * @param $cenidus
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela COMENTARIO relacionados com um USUARIO
     * Se $ative = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_cenidus($cenidus, $limit = 10, $offset = 0, $active = false) {
        if ($active === true) {
            $this->db->where('cecstat', CECSTAT_ATIVO);
        }
        $this->db->where('cenidce', null);
        $this->db->where('cenidus', $cenidus);
        $query = $this->db->get('comentario', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $cenidgr
     * @param $cenidma
     * @param $cenidus
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return midex
     *
     * Metodo para buscar os registros da tabela COMENTARIO relacionados com um MATERIAL e um USUARIO
     * Se $ative = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_cenidus_and_cenidma($cenidus, $cenidma, $limit = 10, $offset = 0, $active = false) {
        if ($active === true) {
            $this->db->where('cecstat', CECSTAT_ATIVO);
        }
        $this->db->where('cenidce', null);
        $this->db->where('cenidus', $cenidus);
        $this->db->where('cenidma', $cenidma);
        $query = $this->db->get('comentario', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $cenid
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela COMENTARIO que sao RESPOSTAS de um determinado COMENTARIO
     * Se $ative = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_respostas_by_cenid($cenid, $limit = 10, $offset = 0, $active = false) {
        if ($active === true) {
            $this->db->where('cecstat', CECSTAT_ATIVO);
        }
        $this->db->where('cenidce', $cenid);
        $query = $this->db->get('comentario', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $data
     * @return int
     *
     * metodo para atualizar um registro da tabela COMENTARIO
     */
    public function update($data) {
        $cenid = $data['cenid'];
        unset($data['cenid']);
        $this->db->where('cenid', $cenid);
        return $this->db->update('comentario', $data) === true ? $cenid : 0;
    }

    /**
     * @param $cenid
     * @return bool
     *
     * Metodo para excluir um registro da tabela COMENTARIO
     */
    public function delete($cenid) {
        $this->db->where('cenid', $cenid);
        return $this->db->delete('comentario');
    }

}