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

        // Loading model
        $this->load->model('grupoconteudo_model');
    }

    /**
     * @param $data
     * @return mixed
     *
     * Metodo para salvar ou atualizar um conteudo.
     */
    public function save($data) {
        // Verifica se o ID do Conteudo nao foi informado
        if (!isset($data['conid'])) { // INSERCAO
            $grnid = $data['grnid'];
            unset($data['grnid']);
            if ($this->db->insert('conteudo', $data) === true) {
                $conid = $this->db->insert_id();
                $this->grupoconteudo_model->save(['gcnidgr' => $grnid, 'gcnidco' => $conid]);
                return $conid;
            }
            return null;
        } else { // ATUALIZACAO
            $grnid = $data['grnid'];
            unset($data['grnid']);
            $conid = $data['conid'];
            unset($data['conid']);
            $this->db->where(['conid' => $conid]);
            if ($this->db->update('conteudo', $data) === true) {
                $this->grupoconteudo_model->delete_by_grnid($grnid);
                $this->grupoconteudo_model->save(['gcnidgr' => $grnid, 'gcnidco' => $conid]);
                return true;
            }
            return false;
        }
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

    public function find_by_cocdesc($cocdesc, $equals = false) {
        if ($equals === true) {
            $this->db->where(['cocdesc' => $cocdesc]);
        } else {
            $this->db->like(['cocdesc' => $cocdesc]);
        }
        $query = $this->db->get('conteudo');
        return ($query->num_rows() > 0) ? $query->result() : null;
    }

}