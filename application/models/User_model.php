<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 28/08/2016
 *
 * Model da tabela <b>USUARIO</b>
 */
class User_model extends CI_Model {

    /**
     * @param $data
     * @return mixed
     *
     * Metodo para salvar um usuario.
     */
    public  function save($data) {
        $this->db->insert('usuario', $data);
        return $this->db->insert_id();
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
        if($query->num_rows() > 0) {
            $user = $query->result();
            unset($user->uscsenh);
            return $user;
        }
        return null;
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
        if($query->num_rows() > 0) {
            $user = $query->result();
            unset($user->uscsenh);
            return $user;
        }
        return null;
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
        if($query->num_rows() > 0) {
            $user = $query->result();
            unset($user->uscsenh);
            return $user;
        }
        return null;
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
        if($query->num_rows() > 0) {
            $user = $query->result();
            unset($user->uscsenh);
            return $user;
        }
        return null;
    }

    public function find_by_usclogn($usclogn) {
        $this->db->where(['usclogn' => $usclogn]);
        $query = $this->db->get('usuario');
        if($query->num_rows() > 0) {
            $user = $query->result();
            unset($user->uscsenh);
            return $user;
        }
        return null;
    }



}