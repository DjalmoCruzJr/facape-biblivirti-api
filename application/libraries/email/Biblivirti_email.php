<?php defined('BASEPATH') OR exit('No direct script access allowed.');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 29/09/2016
 *
 * Classe para auxiliar no processo de envio de emails.
 */
class Biblivirti_email {

    protected $CI;

    /**
     * @var array
     *
     * Array para armazenar as configuracoes de envio dos emails.
     */
    private $configs = [];

    /**
     * @var array
     *
     * Array para armazenar os dados do email a ser enviado (destinatarios, mensagem, titulo, anexos, etc).
     */
    private $data = [];

    private $errors = [];

    /**
     * Account_bo constructor.
     */
    public function __construct() {
        // Loading variables
        $this->CI = &get_instance();

        // Loading email configs
        $this->_load_configs();
    }

    /**
     * @return array
     *
     * Metodo para retornar os erros inerentes processo de envio de email.
     */
    public function get_errors() {
        return $this->errors;
    }

    public function set_data($from = null, $to = null, $subject = null, $message = null) {
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $this->data['reply_to'] = EMAIL_SMTP_USER;
        $this->data['subject'] = $subject;
        $this->data['message'] = $message;
    }

    /**
     * @return bool
     *
     * Metodo para enviar o email.
     */
    public function send() {
        // Loading libraty
        $this->CI->load->library('email');

        // Setando as configuracoes de email no objeto
        $this->CI->email->initialize($this->configs);


        if ($this->_validate_data() === false) {
            return false;
        }

        // Setando os dados do email
        $this->CI->email->from($this->data['from']);
        $this->CI->email->to($this->data['to']);
        $this->CI->email->reply_to($this->data['reply_to']);
        $this->CI->email->subject($this->data['subject']);
        $this->CI->email->message($this->data['message']);

        // Verificando se houve falha no envio do email
        if ($this->CI->email->send()) {
            return true;
        } else {
            $this->errors['errors'] = $this->CI->email->print_debugger();
            return false;
        }

    }

    /**----------------------------------------------------------------------------
     * PRIVATE METHODS
     * -----------------------------------------------------------------------------*/
    private function _load_configs() {
        $this->configs['protocol'] = EMAIL_PROTOCOL;
        $this->configs['smtp_host'] = EMAIL_SMTP_HOST;
        $this->configs['smtp_port'] = EMAIL_SMTP_PORT;
        $this->configs['smtp_user'] = EMAIL_SMTP_USER;
        $this->configs['smtp_pass'] = EMAIL_SMTP_PASS;
        $this->configs['mailtype'] = EMAIL_TYPE;
        $this->configs['charset'] = EMAIL_CHARTSET;
        $this->configs['wordwrap'] = EMAIL_WORDWRAP;
    }

    private function _validate_data() {
        $status = true;

        // Validando o parametro FROM (email de origem)
        if (!isset($this->data['from']) || empty(trim($this->data['from']))) {
            $this->errors['from'] = 'O parâmetro FROM é obrigatório.';
            $status = false;
        } else if (!filter_var($this->data['from'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['from'] = 'Informe um e-mail válido.';
            $status = false;
        }

        // Validando o parametro TO (email do destinatario)
        if (!isset($this->data['to']) || empty(trim($this->data['to']))) {
            $this->errors['to'] = 'O parâmetro TO é obrigatório.';
            $status = false;
        } else if (!filter_var($this->data['to'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['to'] = 'Informe um e-mail válido.';
            $status = false;
        }

        // Validando o parametro REPLAY_TO (email de copia)
        if (!isset($this->data['reply_to']) || empty(trim($this->data['reply_to']))) {
            $this->errors['reply_to'] = 'O parâmetro REPLAY_TO é obrigatório.';
            $status = false;
        } else if (!filter_var($this->data['reply_to'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['reply_to'] = 'Informe um e-mail válido.';
            $status = false;
        }

        // Validando o parametro SUBJECT (titulo do email)
        if (!isset($this->data['subject']) || empty(trim($this->data['subject']))) {
            $this->errors['subject'] = 'O parâmetro SUBJECT é obrigatório.';
            $status = false;
        }

        // Validando o parametro MESSAGE (mensagem do email)
        if (!isset($this->data['message']) || empty(trim($this->data['message']))) {
            $this->errors['subject'] = 'O parâmetro MESSAGE é obrigatório.';
            $status = false;
        }

        return $status;
    }

}