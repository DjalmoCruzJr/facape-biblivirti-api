<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 28/08/2016
 *
 * Model da tabela <b>GRUPO</b>
 */
class Recuperarsenha_model extends CI_Model {

    /**
     * Recuperarsenha_model constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $data
     * @return mixed
     *
     * Metodo para salvar um token de recuperacao de senha.
     */
    public function save($data) {
        return ($this->db->insert('recuperarsenha', $data)) ? $this->db->insert_id() : false;
    }

    /**
     * @param $rsctokn
     * @return bool
     *
     * Metodo para buscar um token de redefinicao de senha pelo <i>rsctokn</i>.
     */
    public function find_by_rsctokn($rsctokn, $rscstat = RSCSTAT_ATIVO) {
        $this->db->where(['rsctokn' => $rsctokn, 'rscstat' => $rscstat]);
        $query = $this->db->get('recuperarsenha');
        return ($query->num_rows() > 0) ? $query->result()[0] : null;
    }

    /**
     * @param $data
     * @return mixed
     *
     * Metodo para atualizar um registro da tabela RECUPERARSENHA
     */
    public function update($data) {
        $rsnid = $data['rsnid'];
        unset($data['rsnid']);
        $this->db->where(['rsnid' => $rsnid]);
        return $this->db->update('recuperarsenha', $data);
    }

    /**
     * @param $rsnidus
     * @return bool
     *
     * Metodo para desabilitar todos os tokens de redefinicao de senha pelo <i>$rsnidus</i>.
     */
    public function disable_all_tokens_by_rsnidus($rsnidus) {
        $this->db->where(['rsnidus' => $rsnidus]);
        return $this->db->update('recuperarsenha', ['rscstat' => RSCSTAT_INATIVO]);
    }

}