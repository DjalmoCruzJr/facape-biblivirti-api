<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 31/08/2016
 *
 * Model da tabela <b>AREAINTERESSE</b>
 */
class Areainteresse_model extends CI_Model {

	/**
     * Areainteresse_model constructor.
     */
    public function __construct() {
        parent::__construct();
	}

    /**
     * @param $data
     * @return mixed
     *
     * Metodo para salvar um ou atualizar uma area de interesse.
     */
	public function save($data) {
        // Verifica se o ID nao foi informado
	    if(!isset($data['ainid'])) { // INSERCAO
            return $this->db->insert('areainteresse', $data) ? $this->db->insert_id() : null;
        } else { // ATUALIZACAO
            $ainid = $data['ainid'];
            unset($data['ainid']);
            $this->db->where(['ainid' => $ainid]);
            return $this->db->update('areainteresse', $data);
        }
    }

    /**
     * @param $ainid
     * @return mixed
     *
     * Metodo para buscar uma Area de Interesse pelo campo ainid (ID).
     */
    public function find_by_ainid($ainid) {
        $this->db->where(['ainid' => $ainid]);
        $query = $this->db->get('areainteresse');
        return ($query->num_rows() > 0) ? $query->result()[0] : null;
    }

    /**
     * @param $aicdesc
     * @param bool $equals
     * @return mixed
     *
     * * Metodo para buscar uma Area de Interesse pelo campo aicdesc (Descricao).
     * se $equal == true a busca he feita no formato: FIELD = VALUE;
     * se $equal == false a busca he feita no formato: FIELD like %VALUE%;
     */
    public function find_by_aicdesc($aicdesc, $equals = false) {
        if($equals === false) {
            $this->db->like('aicdesc', $aicdesc, 'both');
        } else {
            $this->db->where('aicdesc', $aicdesc);
        }
        $query = $this->db->get('areainteresse');
        return ($query->num_rows() > 0) ? $query->result() : null;
    }
}