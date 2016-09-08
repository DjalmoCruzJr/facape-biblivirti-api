<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 07/09/2016
 *
 * Model da tabela <b>QUESTAO</b>
 */
class Questao_model extends CI_Model {

    /**
     * @param $qenid
     * @return mixed
     *
     * Metodo para buscar uma Questao pelo campo qenid (ID).
     */
    public function find_by_qenid($qenid) {
        $this->db->where(['qenid' => $qenid]);
        $query = $this->db->get('questao');
        return ($query->num_rows() > 0) ? $query->result()[0] : null;
    }

}