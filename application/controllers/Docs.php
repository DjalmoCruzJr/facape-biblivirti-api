<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 28/08/2016
 *
 * Controller para gerenciar os dados da <b>Documentacao</b>
 */
class Docs extends CI_Controller {

    public function index() {
        $data['page_title'] = 'Biblivirti API Docs';
        $this->load->view('docs/index', $data);
    }

}
