<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 04/09/2016
 *
 * Model da tabela <b>GRUPOMATERIAL</b>
 */
class Grupomaterial_model extends CI_Model {

	/**
     * Grupomaterial_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $data
     * @return mixed
     *
     * Metodo para salvar a relacao entre um grupo e um material.
     */
    public function save($data) {
        return $this->db->insert('grupomaterial', $data);
    }

}