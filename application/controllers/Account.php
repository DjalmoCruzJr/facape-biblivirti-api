<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Account extends CI_Controller {

    /**
     * Account constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->view('account/login');

        $filename = 'img' . '-' . 123 . '-' . date('d-m-Y', time()) . '-' . date('H-m-s', time());
        echo base_url(UPLOAD_IMAGES_PATH . $filename).'.jpg';
    }

}