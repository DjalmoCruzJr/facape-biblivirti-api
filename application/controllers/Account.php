<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Account extends CI_Controller {

    /**
     * Account constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->load->library('email/biblivirti_email');
    }

    public function index() {
        $data['page_title'] = 'Biblivirti AVAM | Entre ou Cadastre-se!';
        $this->load->view('account/login', $data);

        $this->biblivirti_email->set_data(
            EMAIL_SMTP_USER,
            'djalmo.cruz@gmail.com',
            'TESTE',
            'TESTE'
        );

        if ($this->biblivirti_email->send() === false) {
            $errors = $this->biblivirti_email->get_errors();
            print_r($errors);
        } else {
            echo "E-mail enviado com sucesso!";
        }
    }

}