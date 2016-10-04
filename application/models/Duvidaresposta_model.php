<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 02/10/2016
 *
 * Model da tabela <b>DUVIDARESPOSTA</b>
 */
class Duvidaresposta_model extends CI_Model {

    /**
     * Duvidaresposta_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $data
     * @return int
     *
     * Metodo para salvar um registro na tabela DUVIDARESPOSTA
     */
    public function save($data) {
        return $this->db->insert('duvidaresposta', $data) === true ? $this->db->insert_id() : 0;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar todos os registros da tabela DUVIDARESPOSTA
     */
    public function find_all($limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $query = $this->db->get('duvidaresposta', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $drnid
     * @return mixed
     *
     * Metodo para buscar um registro da tabela DUVIDARESPOSTA pelo ID
     */
    public function find_by_drnid($drnid) {
        $this->db->where('drnid', $drnid);
        $query = $this->db->get('duvidaresposta');
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $drndidv
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela DUVIDARESPOSTA relacionados com um DUVIDA
     * Se $active = TRUE a busca trara somente registros co status ATIVO
     */
    public function find_by_drniddv($drndidv, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('drcstat', DRCSTAT_ATIVO);
        }
        $this->db->where('drniddv', $drndidv);
        $query = $this->db->get('duvidaresposta', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $drnidus
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela DUVIDARESPOSTA relacionados com um USUARIO
     * Se $active = TRUE a busca trara somente registros co status ATIVO
     */
    public function find_by_drnidus($drnidus, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('drcstat', DRCSTAT_ATIVO);
        }
        $this->db->where('drnidus', $drnidus);
        $query = $this->db->get('duvidaresposta', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $drnidgr
     * @param $drnidus
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela DUVIDARESPOSTA relacionados com uma DUVIDA e um USUARIO
     * Se $active = TRUE a busca trara somente registros co status ATIVO
     */
    public function find_by_drniddv_and_drnidus($drniddv, $drnidus, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('drcstat', DRCSTAT_ATIVO);
        }
        $this->db->where('drniddv', $drniddv);
        $this->db->where('drnidus', $drnidus);
        $query = $this->db->get('duvidaresposta', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $drctext
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela DUVIDARESPOSTA relacionados pelo TEXTO
     * Se $like = TRUE a busca sera feita no formato: field LIKE value
     * Se $like = FALSE a busca sera feita no formato: field = value
     * Se $active = TRUE a busca trara somente registros co status ATIVO
     */
    public function find_by_drctext($drctext, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $like = true, $active = false) {
        if ($active === true) {
            $this->db->where('drcstat', DVCSTAT_ATIVO);
        }
        if ($like === true) {
            $this->db->like('drctext', $drctext);
        } else {
            $this->db->where('drctext', $drctext);
        }
        $query = $this->db->get('duvidaresposta', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $data
     * @return int
     *
     * metodo para atualizar um registro da tabela DUVIDARESPOSTA
     */
    public function update($data) {
        $drnid = $data['drnid'];
        unset($data['drnid']);
        $this->db->where('drnid', $drnid);
        return $this->db->update('duvidaresposta', $data) === true ? $drnid : 0;
    }

    /**
     * @param $drnid
     * @return bool
     *
     * Metodo para excluir um registro da tabela DUVIDARESPOSTA
     */
    public function delete($drnid) {
        $this->db->where('drnid', $drnid);
        return $this->db->delete('duvidaresposta');
    }

}