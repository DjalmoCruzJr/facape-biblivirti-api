<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 28/08/2016
 *
 * Model da tabela <b>GRUPO</b>
 */
class Recover_model extends CI_Model {

    /**
     * @param $data
     * @return mixed
     *
     * Metodo para salvar um token de recuperacao de senha.
     */
    public function save($data) {
        // Verifica se o ID do token nao foi informado
        if (!isset($data['rsnid'])) {
            $this->db->insert('recuperarsenha', $data);
        } else {
            $rsnid = $data['rsnid'];
            unset($data['rsnid']);
            $this->db->where(['rsnid' => $rsnid]);
            $this->db->update('recuperarsenha', $data);
        }
        return $this->db->insert_id();
    }

    /**
     * @param $rsnid
     * @return bool
     *
     * Metodo para deletar um token de redefinicao de sennha.
     */
    public function find_by_rsctokn($rsctokn) {
        $this->db->where(['rsctokn' => $rsctokn]);
        $query = $this->db->get('recuperarsenha');
        if($query->num_rows() > 0) {
            return $query->result();
        }
        return null;
    }

}