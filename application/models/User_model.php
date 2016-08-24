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
        $this->db->insert('USUARIO', $data);
        return $this->db->insert_id();
    }

    /**
     * @param $fields
     * @return midex
     *
     * Metodo para buscar um usuario pelo campos passados como parametros.
     * Os campos devem compor um array no seguinte formato:
     *  [
     *      "field_name" => "fields_value",
     *      "field_name" => "fields_value",
     *       ...
     *  ]
     *
     */
    public function find_by_fields($fields) {
        $this->db->where($fields);
        $query = $this->db->get('USUARIO');
        if($query->num_rows() > 0) {
            return $query->result();
        }
        return null;
    }


}