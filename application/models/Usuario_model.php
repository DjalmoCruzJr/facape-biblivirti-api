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
     * @return mixed
     *
     * Metodo para salvar um ou atualizar um  usuario.
     */
    public function save($data) {
        // Verifica se o ID do usuario foi inbformado
        if (!isset($data['usnid'])) { // INSERCAO
            return $this->db->insert('usuario', $data) ? $this->db->insert_id() : null;
        } else { // ATUALIZACAO
            $usnid = $data['usnid'];
            unset($data['usnid']);
            $this->db->where(['usnid' => $usnid]);
            return $this->db->update('usuario', $data);
        }
    }

    /**
     * @param $usnid
     * @return mixed
     *
     * Metodo para buscar um usuario pelo <i>usnid</i> (ID do usuario).
     */
    public function find_by_usnid($usnid) {
        $this->db->where(['usnid' => $usnid]);
        $query = $this->db->get('usuario');
        return ($query->num_rows() > 0) ? $query->result()[0] : null;
    }

    /**
     * @param $uscmail
     * @param $uscsenh
     * @return mixed
     *
     * Metodo para buscar um usuario pelo <i>uscmail</i> (e-amil) e <i>uscsenh</i> (senha).
     */
    public function find_by_uscmail_and_uscsenh($uscmail, $uscsenh) {
        $this->db->where(['uscmail' => $uscmail, 'uscsenh' => $uscsenh]);
        $query = $this->db->get('usuario');
        return ($query->num_rows() > 0) ? $query->result()[0] : null;
    }

    /**
     * @param $uscfbid
     * @return mixed
     *
     * Metodo para buscar um usuario pelo <i>uscfbid</i> (FacebookID).
     */
    public function find_by_uscfbid($uscfbid) {
        $this->db->where(['uscfbid' => $uscfbid]);
        $query = $this->db->get('usuario');
        return ($query->num_rows() > 0) ? $query->result()[0] : null;
    }

    /**
     * @param $uscmail
     * @return mixed
     *
     * Metodo para buscar um usuario pelo <i>uscmail</i> (e-mail).
     */
    public function find_by_uscmail($uscmail) {
        $this->db->where(['uscmail' => $uscmail]);
        $query = $this->db->get('usuario');
        return ($query->num_rows() > 0) ? $query->result()[0] : null;
    }

    /**
     * @param $usclogn
     * @return mixed
     *
     * Metodo para buscar um usuario pelo <i>usclogn</i> (login).
     */
    public function find_by_usclogn($usclogn) {
        $this->db->where(['usclogn' => $usclogn]);
        $query = $this->db->get('usuario');
        return ($query->num_rows() > 0) ? $query->result()[0] : null;
    }

    /**
     * @param $usnid
     * @return bool
     *
     * Metodo para excluir um usuario.
     */
    public function delete($usnid) {
        $this->db->where('usnid', $usnid);
        return $this->db->delete('usuario');
    }
}