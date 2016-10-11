<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 28/08/2016
 *
 * Model da tabela <b>USUARIO</b>
 */
class Usuario_model extends CI_Model {

    /**
     * Usuario_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $data
     * @return int
     *
     * Metodo para salvar um registro na tabela USUARIO
     */
    public function save($data) {
        return $this->db->insert('usuario', $data) === true ? $this->db->insert_id() : 0;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar todos os registros da tabela USUARIO
     * Se $active = TRUE a busca trara apenas registro com status ATIVO
     */
    public function find_all($limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('uscstat', USCSTAT_ATIVO);
        }
        $query = $this->db->get('usuario', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $usnid
     * @return mixed
     *
     * Metodo para buscar um registro da tabela USUARIO pelo ID
     */
    public function find_by_usnid($usnid) {
        $this->db->where('usnid', $usnid);
        $query = $this->db->get('usuario');
        return $query->num_rows() > 0 ? $query->result()[0] : null;
    }

    /**
     * @param $uscmail
     * @param int $limit
     * @param int $offset
     * @param bool $like
     * @return mixed
     *
     * Metodo para buscar registros da tabela USUARIO pela EMAIL
     * Se $like = TRUE a busca eh feita no formato: field LIKE value
     * Se $like = FALSE a busca eh feita no formato: field = value
     * Se $active = TRUE a busca trara apenas registro com status ATIVO
     */
    public function find_by_uscmail($uscmail, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $like = true, $active = false) {
        if ($like === true) {
            $this->db->like('uscmail', $uscmail);
        } else {
            $this->db->where('uscmail', $uscmail);
        }
        if ($active === true) {
            $this->db->where('uscstat', USCSTAT_ATIVO);
        }
        $query = $this->db->get('usuario', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $usclogn
     * @param int $limit
     * @param int $offset
     * @param bool $like
     * @return mixed
     *
     * Metodo para buscar registros da tabela USUARIO pela LOGIN
     * Se $like = TRUE a busca eh feita no formato: field LIKE value
     * Se $like = FALSE a busca eh feita no formato: field = value
     * Se $active = TRUE a busca trara apenas registro com status ATIVO
     */
    public function find_by_usclogn($usclogn, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $like = true, $active = false) {
        if ($like === true) {
            $this->db->like('usclogn', $usclogn);
        } else {
            $this->db->where('usclogn', $usclogn);
        }
        if ($active === true) {
            $this->db->where('uscstat', USCSTAT_ATIVO);
        }
        $query = $this->db->get('usuario', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $uscfbid
     * @return mixed
     *
     * Metodo para buscar registros da tabela USUARIO pela FacebookID
     */
    public function find_by_uscfbid($uscfbid) {
        $this->db->where('uscfbid', $uscfbid);
        $query = $this->db->get('usuario');
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $uscmail
     * @param $uscsenh
     * @param int $limit
     * @param int $offset
     * @param bool $like
     * @return mixed
     *
     * Metodo para buscar registros da tabela USUARIO pelo EMAIL e SENHA
     * Se $active = TRUE a busca trara apenas registro com status ATIVO
     */
    public function find_by_uscmail_and_uscsenh($uscmail, $uscsenh, $active = false) {
        if ($active === true) {
            $this->db->where('uscstat', USCSTAT_ATIVO);
        }
        $this->db->where('uscmail', $uscmail);
        $this->db->where('uscsenh', $uscsenh);
        $query = $this->db->get('usuario');
        return $query->num_rows() > 0 ? $query->result() : null;
    }


    /**
     * @param $erefrence
     * @param int $limit
     * @param int $offset
     * @param bool $like
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar registros da tabela USUARIO pela REFERENCIA
     * Se $like = TRUE a busca eh feita no formato: field LIKE value
     * Se $like = FALSE a busca eh feita no formato: field = value
     * Se $active = TRUE a busca trara apenas registro com status ATIVO
     */
    public function find_by_reference($erefrence, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $like = true, $active = false) {
        if ($like === true) {
            $this->db->or_like('uscnome', $erefrence);
            $this->db->or_like('uscmail', $erefrence);
            $this->db->or_like('usclogn', $erefrence);
        } else {
            $this->db->or_where('uscnome', $erefrence);
            $this->db->or_where('uscmail', $erefrence);
            $this->db->or_where('usclogn', $erefrence);
        }
        if ($active === true) {
            $this->db->where('uscstat', USCSTAT_ATIVO);
        }
        $query = $this->db->get('usuario', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $data
     * @return int
     *
     * metodo para atualizar um registro da tabela USUARIO
     */
    public function update($data) {
        $usnid = $data['usnid'];
        unset($data['usnid']);
        $this->db->where('usnid', $usnid);
        return $this->db->update('usuario', $data) === true ? $usnid : 0;
    }

    /**
     * @param $usnid
     * @return bool
     *
     * Metodo para excluir um registro da tabela USUARIO
     */
    public function delete($usnid) {
        $this->db->where('usnid', $usnid);
        return $this->db->delete('usuario');
    }
}