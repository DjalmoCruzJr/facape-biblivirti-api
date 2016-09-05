<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 04/09/2016
 *
 * Classe para auxiliar no processo de leitura dos dados JSON atraves do RAW INPUT STREAM.
 */
class Biblivirti_input {

    /**
     * @var
     *
     * Referencia para a instancia atual do Codeigniter.
     */
    private $CI;

    /**
     * Biblivirti_input constructor.
     */
    public function __construct() {
        $this->CI = &get_instance();

        // Loading libraries
    }

    /**
     * @return string
     *
     * Retorna os dados recebidos no formato JSON atraves do RAW INPUT STREAM.
     */
    public function get_raw_input_data($data_type = JSON_OBJECT_AS_ARRAY) {
        $data = trim($this->CI->security->xss_clean($this->CI->input->raw_input_stream));
        return json_decode($data, $data_type);
    }

}