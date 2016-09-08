<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 04/09/2016
 *
 * Model da tabela <b>QUESTAOSIMULADO</b>
 */
class Questaosimulado_model extends CI_Model {

    /**
     * @param $data
     * @return mixed
     *
     * Metodo para salvar a relacao entre uma questao e um material (Simulado).
     */
    public function save($data) {
        return $this->db->insert('questaosimulado', $data);
    }

}