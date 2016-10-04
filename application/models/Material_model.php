<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 31/08/2016
 *
 * Model da tabela <b>MATERIAL</b>
 */
class Material_model extends CI_Model {
    /**
     * Material_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $data
     * @return int
     *
     * Metodo para salvar um registro na tabela MATERIAL
     */
    public function save($data) {
        return $this->db->insert('material', $data) === true ? $this->db->insert_id() : 0;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar todos os registros da tabela MATERIAL
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_all($limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('macstat', MACSTAT_ATIVO);
        }
        $query = $this->db->get('material', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $manid
     * @return mixed
     *
     * Metodo para buscar um registro da tabela MATERIAL pelo ID
     */
    public function find_by_manid($manid) {
        $this->db->where('manid', $manid);
        $query = $this->db->get('material');
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $manidgr
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela MATERIAL relaciondo com um GRUPO
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_manidgr($manidgr, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('macstat', MACSTAT_ATIVO);
        }
        $this->db->where('manidgr', $manidgr);
        $query = $this->db->get('material', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $macdesc
     * @param int $limit
     * @param int $offset
     * @param bool $like
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela MATERIAL pela DESCRICAO
     * Se $like = TRUE a busca sera no formato: field LIKE value
     * Se $like = FALSE a busca sera no formato: field = value
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_macdesc($macdesc, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $like = true, $active = false) {
        if ($active === true) {
            $this->db->where('macstat', MACSTAT_ATIVO);
        }
        if ($like === true) {
            $this->db->like('macdesc', $macdesc);
        } else {
            $this->db->where('macdesc', $macdesc);
        }
        $query = $this->db->get('material', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $mactipo
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela MATERIAL pela TIPO
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_mactipo($mactipo, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('macstat', MACSTAT_ATIVO);
        }
        $this->db->where('mactipo', $mactipo);
        $query = $this->db->get('material', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $macnivl
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela MATERIAL pelo NIVEL (TIPO = S -> SIMULADO)
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_macnivl($macnivl, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('macstat', MACSTAT_ATIVO);
        }
        $this->db->where('macnivl', $macnivl);
        $query = $this->db->get('material', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $manidgr
     * @param $macdesc
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela MATERIAL relaciondo com um GRUPO e pela DESCRICAO
     * Se $like = TRUE a busca sera no formato: field LIKE value
     * Se $like = FALSE a busca sera no formato: field = value
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_manidgr_and_macdesc($manidgr, $macdesc, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $like = true, $active = false) {
        if ($active === true) {
            $this->db->where('macstat', MACSTAT_ATIVO);
        }
        if ($like === true) {
            $this->db->like('macdesc', $macdesc);
        } else {
            $this->db->where('macdesc', $macdesc);
        }
        $this->db->where('manidgr', $manidgr);
        $query = $this->db->get('material', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $manidgr
     * @param $mactipo
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela MATERIAL relaciondo com um GRUPO e pelo TIPO
     * Se $like = TRUE a busca sera no formato: field LIKE value
     * Se $like = FALSE a busca sera no formato: field = value
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_manidgr_and_mactipo($manidgr, $mactipo, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('macstat', MACSTAT_ATIVO);
        }
        $this->db->where('mactipo', $mactipo);
        $this->db->where('manidgr', $manidgr);
        $query = $this->db->get('material', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $manidgr
     * @param $macnivl
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela MATERIAL relaciondo com um GRUPO e pelo NIVEL (TIPO = S - SIMULADO)
     * Se $like = TRUE a busca sera no formato: field LIKE value
     * Se $like = FALSE a busca sera no formato: field = value
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_manidgr_and_macnivl($manidgr, $macnivl, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('macstat', MACSTAT_ATIVO);
        }
        $this->db->where('macnivl', $macnivl);
        $this->db->where('manidgr', $manidgr);
        $query = $this->db->get('material', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $data
     * @return int
     *
     * metodo para atualizar um registro da tabela MATERIAL
     */
    public function update($data) {
        $manid = $data['manid'];
        unset($data['manid']);
        $this->db->where('manid', $manid);
        return $this->db->update('material', $data) === true ? $manid : 0;
    }

    /**
     * @param $manid
     * @return bool
     *
     * Metodo para excluir um registro da tabela MATERIAL
     */
    public function delete($manid) {
        $this->db->where('manid', $manid);
        return $this->db->delete('material');
    }
}