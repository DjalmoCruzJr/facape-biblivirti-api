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

    /**
     * @return mixed
     *
     * Metodo para retornar a mensagem que sera enviada por email.
     */
    public function get_message() {
        return $this->data['message'];
    }

    /**
     * @return array
     *
     * Metodo para retornar os dados a serem envidos por email.
     */
    public function get_data() {
        return $this->data;
    }

    /**
     * @param null $from
     * @param null $to
     * @param null $subject
     * @param null $message
     * @param array $datas
     *
     * Metodo para setar os dados do email a ser enviado.
     */
    public function set_data($from = null, $to = null, $subject = null, $message = null, $datas = []) {
        $this->data['from'] = $from;
        $this->data['to'] = $to;
        $this->data['bcc'] = EMAIL_SMTP_USER;
        $this->data['subject'] = $subject;
        $this->data['message'] = is_null($message) ? $message : $this->_replace_keys($message, $datas);
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
        $this->CI->email->bcc($this->data['bcc']);
        $this->CI->email->subject($this->data['subject']);
        $this->CI->email->message($this->data['message']);

        // Verificando se houve falha no envio do email
        if ($this->CI->email->send()) {
            return true;
        } else {
            $this->errors['seding_errors'] = $this->CI->email->print_debugger();
            return false;
        }

    }

    /**----------------------------------------------------------------------------
     * PRIVATE METHODS
     * -----------------------------------------------------------------------------*/
    /**
     * Metodo para carregar as configuracoes de envio de email.
     */
    private function _load_configs() {
        $this->configs['useragent'] = EMAIL_USERAGENT;
        $this->configs['mailtype'] = EMAIL_MAILTYPE;
        $this->configs['protocol'] = EMAIL_PROTOCOL;
        $this->configs['smtp_host'] = EMAIL_SMTP_HOST;
        $this->configs['smtp_port'] = EMAIL_SMTP_PORT;
        $this->configs['smtp_timeout'] = EMAIL_TIMEOUT;
        $this->configs['smtp_user'] = EMAIL_SMTP_USER;
        $this->configs['smtp_pass'] = base64_decode(EMAIL_SMTP_PASS);
        $this->configs['validate'] = EMAIL_VALIDATE;
        $this->configs['wordwrap'] = EMAIL_WORDWRAP;
        $this->configs['charset'] = EMAIL_CHARTSET;
        $this->configs['newline'] = EMAIL_NEWLINE;
    }

    /**
     * @param null $message
     * @param null $datas
     * @return mixed|null
     *
     * Metodo para realizar a troca das chaves da mensagem do email pelos seus respectivos valores.
     */
    private function _replace_keys($message = null, $datas = null) {
        if (is_null($message) || is_null($datas)) {
            return null;
        }
        $keys = array_keys($datas);
        foreach ($keys as $key) {
            $message = str_replace($key, $datas[$key], $message);
        }
        return $message;
    }

    /**
     * @return bool
     *
     * Metodo para verificar se as informacoes a serem enviadas sao validas.
     */
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

        // Validando o parametro BCC (email de copia em background)
        if (!isset($this->data['bcc']) || empty(trim($this->data['bcc']))) {
            $this->errors['bcc'] = 'O parâmetro BCC é obrigatório.';
            $status = false;
        } else if (!filter_var($this->data['bcc'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['bcc'] = 'Informe um e-mail válido.';
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