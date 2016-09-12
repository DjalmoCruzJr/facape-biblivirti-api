<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 31/08/2016
 *
 * Model da tabela <b>MATERIAL</b>
 */
class Material_model extends CI_Model {
    /**
     * Material_model constructor.
     */
    public function __construct() {
        parent::__construct();

        // Loading models
        $this->load->model('comentario_model');
        $this->load->model('historicoacesso_model');
        $this->load->model('grupomaterial_model');
        $this->load->model('conteudomaterial_model');
        $this->load->model('questaosimulado_model');
    }

    /**
     * @param $manid
     * @return mixed
     *
     * Metodo para salvar ou atualizar uma Material.
     */
    public function save($data) {
        if (!isset($data['manid'])) { // INSERCAO
            $grnid = $data['grnid'];
            unset($data['grnid']);
            if ($data['mactipo'] !== MACTIPO_SIMULADO) { // O material NAO eh um SIMULADO
                $contens = $data['contents'];
                unset($data['contents']);
                if ($this->db->insert('material', $data) === true) {
                    $manid = $this->db->insert_id();
                    $this->grupomaterial_model->save(['gmnidgr' => $grnid, 'gmnidma' => $manid]);
                    foreach ($contens as $content) {
                        $this->conteudomaterial_model->save(['cmnidma' => $manid, 'cmnidco' => $content['conid']]);
                    }
                    return $manid;
                }
            } else { // O material EH um SIMULADO
                $questions = $data['questions'];
                unset($data['questions']);
                if ($this->db->insert('material', $data) === true) {
                    $manid = $this->db->insert_id();
                    $this->grupomaterial_model->save(['gmnidgr' => $grnid, 'gmnidma' => $manid]);
                    foreach ($questions as $question) {
                        $this->questaosimulado_model->save(['qsnidma' => $manid, 'qsnidqe' => $question['qenid']]);
                    }
                    return $manid;
                }
            }
            return null;
        } else { // ATUALIZACAO
            $grnid = $data['grnid'];
            unset($data['grnid']);
            $manid = $data['manid'];
            unset($data['manid']);
            if ($data['mactipo'] !== MACTIPO_SIMULADO) { // O material NAO eh um SIMULADO
                $contens = $data['contents'];
                unset($data['contents']);
                $this->conteudomaterial_model->delete_by_manid($manid);
                $this->db->where(['manid' => $manid]);
                $this->db->update('material', $data);
                foreach ($contens as $content) {
                    $this->conteudomaterial_model->save(['cmnidma' => $manid, 'cmnidco' => $content['conid']]);
                }
                return $manid;
            } else { // O material EH um SIMULADO
                $questions = $data['questions'];
                unset($data['questions']);
                $this->questaosimulado_model->delete_by_manid($manid);
                $this->db->where(['manid' => $manid]);
                $this->db->update('material', $data);
                foreach ($questions as $question) {
                    $this->questaosimulado_model->save(['qsnidma' => $manid, 'qsnidqe' => $question['qenid']]);
                }
                return $manid;
            }
            return false;
        }
    }

    /**
     * @param $manid
     * @return mixed
     *
     * Metodo para buscar uma Material pelo campo manid (ID).
     */
    public function find_by_manid($manid) {
        $this->db->where(['manid' => $manid]);
        $query = $this->db->get('material');
        if ($query->num_rows() > 0) {
            $material = $query->result()[0];
            if ($material->mactipo === MACTIPO_SIMULADO) {
                unset($material->macurl); // Remove o campo macurl (URL do material - NAO SIMULADO)
                $material->questions = $this->questaosimulado_model->find_questions_by_manid($material->manid);
            } else {
                unset($material->macnivl); // Remove o campo macnivl (Nivel do material - SIMULADO)
                $material->constents = $this->conteudomaterial_model->find_contents_by_manid($material->manid);
            }
            return $material;
        }
        return null;
    }

    /**
     * @param $macdesc
     * @return mixed
     *
     * Metodo para buscar uma Material pelo campo macdesc (Descricao).
     * Formato da busca: 'field' LIKE 'value'
     */
    public function find_by_macdesc($macdesc) {
        $this->db->like('macdesc', $macdesc, 'both');
        $query = $this->db->get('material');
        return ($query->num_rows() > 0) ? $query->result() : null;
    }

    /**
     * @param $grnid
     * @return mixed
     *
     * Metodo para buscar os materiais relacionado com um determinado grupo passado pelo parametro grnid(ID do grupo).
     */
    public function find_by_grnid($grnid, $limite = 1000, $offset = 0) {
        $this->db->select('manid, macdesc, mactipo, malanex, macurl, macnivl, macstat, madcadt, madaldt');
        $this->db->from('material');
        $this->db->join('grupomaterial', 'gmnidma = manid', 'inner');
        $this->db->join('grupo', 'gmnidgr = grnid', 'inner');
        $this->db->where(['grnid' => $grnid]);
        $this->db->order_by('madcadt, macdesc', 'DESC');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $materials = $query->result();
            foreach ($materials as $material) {
                $material->manqtdce = $this->comentario_model->find_count_by_manid($material->manid);
                $material->manqtdha = $this->historicoacesso_model->find_count_by_hanidma($material->manid);
            }
            return $materials;
        }
        return null;
    }

}