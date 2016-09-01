<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 28/08/2016
 *
 * Classe para geracao de Hashs para as senha dos usuarios.
 */
class Biblivirti_hash {

    public function make($password) {
        if (!isset($password)) {
            return null;
        }
        return md5(BIBLIVIRTI_HASH_KEY . $password);
    }

    public function token($mail) {
        if (!isset($mail)) {
            return null;
        }
        return md5(BIBLIVIRTI_HASH_KEY . $mail . strtotime('now'));
    }

}