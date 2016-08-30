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
     * @return int
     *
     * Metodo para salvar um grupo.
     */
    public function save($data) {
        // Verifica se o ID do Grupo nao foi informado
        if (!isset($data['grnid'])) {
            $usnid = $data['usnid'];
            unset($data['usnid']);
            $this->db->insert('grupo', $data);
            $grnid = $this->db->insert_id();
            $this->subscribe($grnid, $usnid, true);
            return $grnid;
        } else {
            $grnid = $data['grnid'];
            unset($data['grnid']);
            $this->db->where(['grnid' => $grnid]);
            $this->db->update('grupo', $data);
            return $this->db->affected_rows();
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
                $group->area_of_interest = $this->find_group_area_of_interest($group->grnidai);
                $group->admin = $this->find_group_admin($group->grnid);
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
        if ($query->num_rows() > 0) {
            return $query->result()[0];
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
            return $query->result()[0];
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
            return $query->result()[0];
        }
        return null;
    }

    /**
     * @param $grnid
     * @return bool
     *
     * Metodo para deletar um determinado grupo.
     */
    public function delete($grnid) {
        $this->db->where(['grnid' => $grnid]);
        $this->db->delete('grupo');
        return $this->db->affected_rows() !== 0 ? true : false;
    }

    /**
     * @param $data
     * @return bool
     *
     * Metodo para salvar um grupo.
     */
    public function subscribe($grnid, $usnid, $guladm = false) {
        $data = [
            'gunidgr' => $grnid,
            'gunidus' => $usnid,
            'guladm' => $guladm,
        ];
        $this->db->insert('grupousuario', $data);
        return $this->db->affected_rows() !== 0 ? true : false;
    }

}