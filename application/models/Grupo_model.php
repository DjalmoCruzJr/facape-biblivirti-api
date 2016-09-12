<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 28/08/2016
 *
 * Model da tabela <b>GRUPO</b>
 */
class Grupo_model extends CI_Model {
    /**
     * Grupo_model constructor.
     */
    public function __construct() {
        parent::__construct();

        // Loading model
        $this->load->model('areainteresse_model');
        $this->load->model('grupousuario_model');
    }


    /**
     * @param $data
     * @return int
     *
     * Metodo para salvar um grupo.
     */
    public function save($data) {
        // Verifica se o ID do Grupo nao foi informado
        if (!isset($data['grnid'])) {
            $usnid = $data['usnid'];
            unset($data['usnid']);
            if ($this->db->insert('grupo', $data) === false) {
                return null;
            }
            $grnid = $this->db->insert_id();
            $this->subscribe($grnid, $usnid, true);
            return $grnid;
        } else {
            $grnid = $data['grnid'];
            unset($data['grnid']);
            unset($data['usnid']);
            $this->db->where(['grnid' => $grnid]);
            return $this->db->update('grupo', $data);
        }
    }

    /**
     * @param $usnid
     * @return mixed
     *
     *  Metodo para buscar todos os grupos de um determinado usuario.
     */
    public function find_by_usnid($usnid) {
        $this->db->select('grnid, grcnome, grcfoto, grctipo, grdcadt, grnidai');
        $this->db->from('grupousuario');
        $this->db->join('grupo', 'gunidgr = grnid');
        $this->db->join('usuario', 'gunidus = usnid');
        $this->db->where(['usnid' => $usnid]);
        $this->db->order_by('grcnome', 'ASC');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $groups = $query->result();
            foreach ($groups as $group) {
                $group->area_of_interest = $this->areainteresse_model->find_by_ainid($group->grnidai);
                $group->admin = $this->find_admin($group->grnid);
                unset($group->grnidai);
            }
            return $groups;
        }
        return null;
    }

    /**
     * @param $grnid
     * @return mixed
     *
     *  Metodo para buscar um grupo pelo campo <i>grnid</i> (ID do grupo).
     */
    public function find_by_grnid($grnid) {
        $this->db->where(['grnid' => $grnid]);
        $query = $this->db->get('grupo');
        return ($query->num_rows() > 0) ? $query->result()[0] : null;
    }

    /**
     * @param $grnid
     * @return mixed
     *
     * Metodo para buscar o administrador de um determinado grupo.
     */
    public function find_admin($grnid) {
        $this->db->select('usnid, uscfbid, uscnome, uscmail, usclogn, uscfoto, uscstat, usdcadt');
        $this->db->from('grupousuario');
        $this->db->join('usuario', 'gunidus = usnid', 'inner');
        $this->db->where(['gunidgr' => $grnid, 'guladm' => true]);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result()[0] : null;
    }

    /**
     * @param $grnid
     * @return bool
     *
     * Metodo para deletar um determinado grupo.
     */
    public function delete($grnid) {
        $this->db->where(['grnid' => $grnid]);
        return $this->db->delete('grupo');
    }

    /**
     * @param $data
     * @return bool
     *
     * Metodo para associar um usuario a um grupo.
     */
    public function subscribe($grnid, $usnid, $guladm = false, $gucstat = GUCSTAT_ATIVO) {
        $data = [
            'gunidgr' => $grnid,
            'gunidus' => $usnid,
            'guladm' => $guladm,
            'gucstat' => $gucstat
        ];
        return $this->grupousuario_model->save($data);
    }

}