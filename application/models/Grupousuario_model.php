<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 04/09/2016
 *
 * Model da tabela <b>GRUPOUSUARIO</b>
 */
class Grupousuario_model extends CI_Model {

	/**
     * Grupousuario_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $data
     * @return mixed
     *
     * Metodo para salvar a relacao entre um grupo e um usuario.
     */
    public function save($data) {
        return $this->db->insert('grupousuario', $data);
    }

}