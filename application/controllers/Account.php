<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Account extends CI_Controller {

    /**
     * Account constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['page_title'] = 'Biblivirti AVAM | Entre ou Cadastre-se!';
        $this->load->view('account/login', $data);
    }

}