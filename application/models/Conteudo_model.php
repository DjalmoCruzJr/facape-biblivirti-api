<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 04/09/2016
 *
 * Model da tabela <b>CONTEUDO</b>
 */
class Conteudo_model extends CI_Model {

    /**
     * Conteudo_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $conid
     * @return mixed
     *
     * Metodo para buscar um conteudo pelo campo conid (ID).
     */
    public function find_by_conid($conid) {
        $this->db->where(['conid' => $conid]);
        $query = $this->db->get('conteudo');
        return ($query->num_rows() > 0) ? $query->result()[0] : null;
    }

    /**
     * @param $grnid
     * @return mixed
     *
     * Metodo para buscar todos os conteudo relacionados com um determinado grupo.
     */
    public function find_by_grnid($grnid) {
        $this->db->select('conid, cocdesc, codcadt, codaldt');
        $this->db->from('conteudo');
        $this->db->join('grupoconteudo', 'gcnidco = conid', 'inner');
        $this->db->join('grupo', 'gcnidgr = grnid', 'inner');
        $this->db->where(['grnid' => $grnid]);
        $this->db->order_by('conid ASC', 'cocdesc ASC');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : null;
    }

}