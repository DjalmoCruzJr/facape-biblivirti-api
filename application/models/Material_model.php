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
    }

    /**
     * @param $data
     * @return int
     *
     * Metodo para salvar um registro na tabela MATERIAL
     */
    public function save($data) {
        if ($data['mactipo'] !== MACTIPO_SIMULADO) { // Material NAO eh simulado
            $contents = $data['contents'];
            unset($data['contents']);
            $manid = $this->db->insert('material', $data) === true ? $this->db->insert_id() : 0;
            if ($manid !== 0) {
                foreach ($contents as $content) {
                    $this->add_content($manid, $content['conid']);
                }
                return $manid;
            }
            return 0;
        } else { // Material EH simulado
            $contents = $data['contents'];
            $questions = $data['questions'];
            unset($data['contents']);
            unset($data['questions']);
            $manid = $this->db->insert('material', $data) === true ? $this->db->insert_id() : 0;
            if ($manid !== 0) {
                foreach ($contents as $content) {
                    $this->add_content($manid, $content['conid']);
                }
                foreach ($questions as $question) {
                    $this->add_question($manid, $question['qenid']);
                }
                return $manid;
            }
            return 0;
        }
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar todos os registros da tabela MATERIAL
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_all($limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('macstat', MACSTAT_ATIVO);
        }
        $query = $this->db->get('material', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $manid
     * @return mixed
     *
     * Metodo para buscar um registro da tabela MATERIAL pelo ID
     */
    public function find_by_manid($manid) {
        $this->db->where('manid', $manid);
        $query = $this->db->get('material');
        return $query->num_rows() > 0 ? $query->result()[0] : null;
    }

    /**
     * @param $manidgr
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela MATERIAL relaciondo com um GRUPO
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_manidgr($manidgr, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('macstat', MACSTAT_ATIVO);
        }
        $this->db->where('manidgr', $manidgr);
        $query = $this->db->get('material', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $macdesc
     * @param int $limit
     * @param int $offset
     * @param bool $like
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela MATERIAL pela DESCRICAO
     * Se $like = TRUE a busca sera no formato: field LIKE value
     * Se $like = FALSE a busca sera no formato: field = value
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_macdesc($macdesc, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $like = true, $active = false) {
        if ($active === true) {
            $this->db->where('macstat', MACSTAT_ATIVO);
        }
        if ($like === true) {
            $this->db->like('macdesc', $macdesc);
        } else {
            $this->db->where('macdesc', $macdesc);
        }
        $query = $this->db->get('material', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $mactipo
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela MATERIAL pela TIPO
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_mactipo($mactipo, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('macstat', MACSTAT_ATIVO);
        }
        $this->db->where('mactipo', $mactipo);
        $query = $this->db->get('material', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $macnivl
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela MATERIAL pelo NIVEL (TIPO = S -> SIMULADO)
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_macnivl($macnivl, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('macstat', MACSTAT_ATIVO);
        }
        $this->db->where('macnivl', $macnivl);
        $query = $this->db->get('material', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $manidgr
     * @param $macdesc
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela MATERIAL relaciondo com um GRUPO e pela DESCRICAO
     * Se $like = TRUE a busca sera no formato: field LIKE value
     * Se $like = FALSE a busca sera no formato: field = value
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_manidgr_and_macdesc($manidgr, $macdesc, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $like = true, $active = false) {
        if ($active === true) {
            $this->db->where('macstat', MACSTAT_ATIVO);
        }
        if ($like === true) {
            $this->db->like('macdesc', $macdesc);
        } else {
            $this->db->where('macdesc', $macdesc);
        }
        $this->db->where('manidgr', $manidgr);
        $query = $this->db->get('material', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $manidgr
     * @param $mactipo
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela MATERIAL relaciondo com um GRUPO e pelo TIPO
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_manidgr_and_mactipo($manidgr, $mactipo, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('macstat', MACSTAT_ATIVO);
        }
        $this->db->where('mactipo', $mactipo);
        $this->db->where('manidgr', $manidgr);
        $query = $this->db->get('material', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $macdesc
     * @param $mactipo
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela MATERIAL pela DESCFRICAO e pelo TIPO
     * Se $like = TRUE a busca sera no formato: field LIKE value
     * Se $like = FALSE a busca sera no formato: field = value
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_macdesc_and_mactipo($macdesc, $mactipo = null, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $like = true, $active = false) {
        if ($active === true) { // Verifica se a busca trara somente registros ativos
            $this->db->where('macstat', MACSTAT_ATIVO);
        }
        if ($like === true) { // Verifica se a busca sera feita no formato LIKE
            $this->db->like('macdesc', $macdesc);
        } else { // Verifica se a busca sera feita no formato WHERE
            $this->db->where('macdesc', $macdesc);
        }
        if (!is_null($mactipo)) { // Verifica se a busca sera feita por um tipo especifico de material
            $this->db->where('mactipo', $mactipo);
        }
        $query = $this->db->get('material', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $manidgr
     * @param $macnivl
     * @param int $limit
     * @param int $offset
     * @param bool $active
     * @return mixed
     *
     * Metodo para buscar os registros da tabela MATERIAL relaciondo com um GRUPO e pelo NIVEL (TIPO = S - SIMULADO)
     * Se $like = TRUE a busca sera no formato: field LIKE value
     * Se $like = FALSE a busca sera no formato: field = value
     * Se $active = TRUE a busca trara somente registros com status ATIVO
     */
    public function find_by_manidgr_and_macnivl($manidgr, $macnivl, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT, $active = false) {
        if ($active === true) {
            $this->db->where('macstat', MACSTAT_ATIVO);
        }
        $this->db->where('macnivl', $macnivl);
        $this->db->where('manidgr', $manidgr);
        $query = $this->db->get('material', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $manid
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar os registros da tabela CONTEUDO relaciondo com um MATERIAL.
     */
    public function find_material_contents($manid, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $this->db->select('conid, conidgr, cocdesc, codcadt, codaldt');
        $this->db->join('conteudomaterial', 'cmnidco = conid', 'inner');
        $this->db->join('material', 'manid = cmnidma', 'inner');
        $this->db->where('manid', $manid);
        $query = $this->db->get('conteudo', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }

    /**
     * @param $manid
     * @param int $limit
     * @param int $offset
     * @return mixed
     *
     * Metodo para buscar os registros da tabela QUESTAO relaciondo com um MATERIAL (Simulado).
     */
    public function find_material_questions($manid, $limit = LIMIT_DEFAULT, $offset = OFFSET_DEFAULT) {
        $this->db->select('qenid, qecdesc, qectext, qecanex, qedcadt, qedaldt');
        $this->db->join('questaosimulado', 'qsnidqe = qenid', 'inner');
        $this->db->join('material', 'manid = qsnidma', 'inner');
        $this->db->where('manid', $manid);
        $query = $this->db->get('questao', $limit, $offset);
        return $query->num_rows() > 0 ? $query->result() : null;
    }


    /**
     * @param $manid
     * @param $conid
     * @return bool
     *
     * Metodo para registar a relacao entre um MATERIAL e um CONTEUDO.
     */
    public function add_content($manid, $conid) {
        $data = ['cmnidma' => $manid, 'cmnidco' => $conid];
        return $this->db->insert('conteudomaterial', $data) === true;
    }

    /**
     * @param $manid
     * @param $qenid
     * @return bool
     *
     * Metodo para registar a relacao entre um MATERIAL (Simulado) e uma QUESTAO.
     */
    public function add_question($manid, $qenid) {
        $data = ['qsnidma' => $manid, 'qsnidqe' => $qenid];
        return $this->db->insert('questaosimulado', $data) === true;
    }

    /**
     * @param $data
     * @return int
     *
     * metodo para atualizar um registro da tabela MATERIAL
     */
    public function update($data) {
        if ($data['mactipo'] !== MACTIPO_SIMULADO) { // Material NAO eh simulado
            $manid = $data['manid'];
            $contents = $data['contents'];
            unset($data['manid']);
            unset($data['contents']);
            $this->db->where('manid', $manid);
            $manid = $this->db->update('material', $data) === true ? $manid : 0;
            if ($manid !== 0) {
                $this->remove_contents($manid);
                foreach ($contents as $content) {
                    $this->add_content($manid, $content['conid']);
                }
                return $manid;
            }
            return 0;
        } else { // Material EH simulado
            $manid = $data['manid'];
            $contents = $data['contents'];
            $questions = $data['questions'];
            unset($data['manid']);
            unset($data['contents']);
            unset($data['questions']);
            $this->db->where('manid', $manid);
            $manid = $this->db->update('material', $data) === true ? $manid : 0;
            if ($manid !== 0) {
                $this->remove_contents($manid);
                $this->remove_questions($manid);
                foreach ($contents as $content) {
                    $this->add_content($manid, $content['conid']);
                }
                foreach ($questions as $question) {
                    $this->add_question($manid, $question['qenid']);
                }
                return $manid;
            }
            return 0;
        }
    }

    /**
     * @param $manid
     * @return bool
     *
     * Metodo para remover todos os conteudos relacionados com um MATERIAL
     */
    public function remove_contents($manid) {
        $this->db->where('cmnidma', $manid);
        return $this->db->delete('conteudomaterial') === true;
    }

    /**
     * @param $manid
     * @return bool
     *
     * Metodo para remover todas as questoes relacionadas com um MATERIAL (Simulado)
     */
    public function remove_questions($manid) {
        $this->db->where('qsnidma', $manid);
        return $this->db->delete('questaosimulado') === true;
    }

    /**
     * @param $manid
     * @return bool
     *
     * Metodo para excluir um registro da tabela MATERIAL
     */
    public function delete($manid) {
        $this->db->where('manid', $manid);
        return $this->db->delete('material');
    }
}