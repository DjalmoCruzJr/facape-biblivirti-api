<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 04/09/2016
 *
 * Model da tabela <b>HISTORICOACESSO</b>
 */
class Historicoacesso_model extends CI_Model {

	/**
     * Historicoacesso_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $hanidma
     * @return mixed
     *
     * Metodo que retorna a qtd. de vizualizacoes de um determinado material.
     */
    public function count_by_hanidma($hanidma) {
        $this->db->where(['hanidma' => $hanidma]);
        $query = $this->db->get('historicoacesso');
        return $query->num_rows();
    }

}