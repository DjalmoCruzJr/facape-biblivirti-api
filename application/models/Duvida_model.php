<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 02/10/2016
 *
 * Model da tabela <b>DUVIDA</b>
 */
class Duvida_model extends CI_Model {

    /**
     * Duvida_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $data
     * @return int
     *
     * Metodo para salvar um registro na tabela DUVIDA
     */
    public function save($data) {
        $contents = $data['contents'];
        unset($data['contents']);
        $dvnid = $this->db->insert('duvida', $data) === true ? $this->db->insert_id() : 0;
        if ($dvnid !== 0) {
            foreach ($contents as $content) {
                $this->add_content($dvnid, $content['conid']);
            }
            return $dvnid;
        }
        return 0;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar todos os registros da tabela DUVIDA
     */
    public function find_all($limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $query = $this->db->get('duvida', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $dvnid
     * @return mixed
     *
     * Metodo para buscar um registro da tabela DUVIDA pelo ID
     */
    public function find_by_dvnid($dvnid) {
        $this->db->where('dvnid', $dvnid);
        $query = $this->db->get('duvida');
        return $query->num_rows() > 0 ? $query->result()[0] : null;
    }

    /**
     * @param $dvndigr
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela DUVIDA relacionados com um GRUPO
     * Se $active = TRUE a busca trara somente registros co status ATIVO
     */
    public function find_by_dvnidgr($dvndigr, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('dvcstat', DVCSTAT_ATIVO);
        }
        $this->db->where('dvnidgr', $dvndigr);
        $query = $this->db->get('duvida', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $dvndius
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela DUVIDA relacionados com um USUARIO
     * Se $active = TRUE a busca trara somente registros co status ATIVO
     */
    public function find_by_dvnidus($dvndius, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('dvcstat', DVCSTAT_ATIVO);
        }
        $this->db->where('dvnidus', $dvndius);
        $query = $this->db->get('duvida', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $dvndigr
     * @param $dvndius
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela DUVIDA relacionados com um GRUPO e um USUARIO
     * Se $active = TRUE a busca trara somente registros co status ATIVO
     */
    public function find_by_dvnidgr_and_dvnidus($dvndigr, $dvndius, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('dvcstat', DVCSTAT_ATIVO);
        }
        $this->db->where('dvnidgr', $dvndigr);
        $this->db->where('dvnidus', $dvndius);
        $query = $this->db->get('duvida', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $dvctext
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela DUVIDA relacionados pelo TEXTO
     * Se $like = TRUE a busca sera feita no formato: field LIKE value
     * Se $like = FALSE a busca sera feita no formato: field = value
     * Se $active = TRUE a busca trara somente registros co status ATIVO
     */
    public function find_by_dvctext($dvctext, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $like = true, $active = false) {
        if ($active === true) {
            $this->db->where('dvcstat', DVCSTAT_ATIVO);
        }
        if ($like === true) {
            $this->db->like('dvctext', $dvctext);
        } else {
            $this->db->where('dvctext', $dvctext);
        }
        $query = $this->db->get('duvida', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $dvnid
     * @param $conid
     * @return bool
     *
     * Metodo para registar a relacao entre uma DUVIDA e um CONTEUDO.
     */
    public function add_content($dvnid, $conid) {
        $data = ['dcniddv' => $dvnid, 'dcnidco' => $conid];
        return $this->db->insert('duvidaconteudo', $data) === true;
    }

    /**
     * @param $data
     * @return int
     *
     * metodo para atualizar um registro da tabela DUVIDA
     */
    public function update($data) {
        $dvnid = $data['dvnid'];
        $contents = $data['contents'];
        unset($data['dvnid']);
        unset($data['contents']);
        $this->db->where('dvnid', $dvnid);
        $dvnid = $this->db->update('duvida', $data) === true ? $dvnid : 0;
        if ($dvnid !== 0) {
            $this->remove_contents($dvnid);
            foreach ($contents as $content) {
                $this->add_content($dvnid, $content['conid']);
            }
            return $dvnid;
        }
        return 0;
    }

    /**
     * @param $dvnid
     * @return bool
     *
     * Metodo para remover todos os conteudos relacionados com uma DUVIDA
     */
    public function remove_contents($dvnid) {
        $this->db->where('dcniddv', $dvnid);
        return $this->db->delete('duvidaconteudo') === true;
    }

    /**
     * @param $dvnid
     * @return bool
     *
     * Metodo para excluir um registro da tabela DUVIDA
     */
    public function delete($dvnid) {
        $this->db->where('dvnid', $dvnid);
        return $this->db->delete('duvida');
    }

}