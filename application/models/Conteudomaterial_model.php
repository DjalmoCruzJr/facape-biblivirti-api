<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 05/09/2016
 *
 * Model da tabela <b>CONTEUDOMATERIAL</b>
 */
class Conteudomaterial_model extends CI_Model {

    /**
     * @param $cenid
     * @return mixed
     *
     * Metodo para salvar uma relação entre um CONTEUDO e um MATERIAL.
     */
    public function save($data) {
        return $this->db->insert('conteudomaterial', $data);
    }


}