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
     * @param $rsctokn
     * @return bool
     *
     * Metodo para buscar um token de redefinicao de senha pelo <i>rsctokn</i>.
     */
    public function find_by_rsctokn($rsctokn) {
        $this->db->where(['rsctokn' => $rsctokn]);
        $query = $this->db->get('recuperarsenha');
        if($query->num_rows() > 0) {
            return $query->result()[0];
        }
        return null;
    }

    /**
     * @param $rsnidus
     * @return bool
     *
     * Metodo para desabilitar todos os tokens de redefinicao de senha pelo <i>$rsnidus</i>.
     */
    public function unable_all_tokens_by_rsnidus($rsnidus) {
        $this->db->where(['rsnidus' => $rsnidus]);
        $query = $this->db->update('recuperarsenha', ['rscstat' => RSCSTAT_INATIVO]);
        return $this->db->insert_id();
    }

}