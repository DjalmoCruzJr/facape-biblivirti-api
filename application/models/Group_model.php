<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 28/08/2016
 *
 * Model da tabela <b>GRUPO</b>
 */
class Group_model extends CI_Model {

    /**
     * @param $data
     * @return mixed
     *
     * Metodo para salvar um grupo.
     */
    public function save($data) {
        // Verifica se o ID do Grupo nao foi informado
        if (!isset($data['grnid'])) {
            $this->db->insert('grupo', $data);
        } else {
            $grnid = $data['grnid'];
            unset($data['grnid']);
            $this->db->where(['grnid' => $grnid]);
            $this->db->update('grupo', $data);
        }
        return $this->db->insert_id();
    }

    /**
     * @param $fields
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
                $group->area_of_interest = $this->find_group_area_of_interest($group->grnidai);
                $group->admin = $this->find_group_admin($group->grnid);
                unset($group->grnidai);
            }
            return $groups;
        }
        return null;
    }

    /**
     * @param $grnidai
     * @return mixed
     *
     * Metodo para buscar a area de interesse de um determinado grupo.
     */
    public function find_group_area_of_interest($grnidai) {
        $this->db->where(['ainid' => $grnidai]);
        $query = $this->db->get('areainteresse');
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return null;
    }

    /**
     * @param $grnid
     * @return mixed
     *
     * Metodo para buscar o administrador de um determinado grupo.
     */
    public function find_group_admin($grnid) {
        $this->db->select('usnid, uscnome, uscmail, usclogn, uscfoto, uscstat, usdcadt');
        $this->db->from('grupousuario');
        $this->db->join('usuario', 'gunidus = usnid', 'inner');
        $this->db->where(['gunidgr' => $grnid, 'guladm' => true]);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return null;
    }

}