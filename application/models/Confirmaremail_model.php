<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 28/09/2016
 *
 * Model da tabela <b>CONFIRMAREMAIL</b>
 */
class Confirmaremail_model extends CI_Model {

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
     * Metodo para salvar um token de confirmacao de e-mail.
     */
    public function save($data) {
        return ($this->db->insert('confirmaremail', $data)) ? $this->db->insert_id() : false;
    }

    /**
     * @param $cactokn
     * @return bool
     *
     * Metodo para buscar um token de confirmacao de e-mail pelo <i>rsctokn</i>.
     */
    public function find_by_cactokn($cactokn, $rscstat = RSCSTAT_ATIVO) {
        $this->db->where(['cactokn' => $cactokn, 'cacstat' => $rscstat]);
        $query = $this->db->get('confirmaremail');
        return ($query->num_rows() > 0) ? $query->result()[0] : null;
    }

    /**
     * @param $data
     * @return mixed
     *
     * Metodo para atualizar um registro da tabela CONFIRMAREMAIL
     */
    public function update($data) {
        $canid = $data['canid'];
        unset($data['canid']);
        $this->db->where(['canid' => $canid]);
        return $this->db->update('confirmaremail', $data);
    }

    /**
     * @param $canidus
     * @return bool
     *
     * Metodo para desabilitar todos os tokens de confirmacao de e-mail pelo <i>$canidus</i>.
     */
    public function disable_all_tokens_by_canidus($canidus) {
        $this->db->where(['canidus' => $canidus]);
        return $this->db->update('confirmaremail', ['cacstat' => RSCSTAT_INATIVO]);
    }

}