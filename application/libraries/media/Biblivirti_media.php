<?php

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 13/02/2017
 *
 * Classe para fazer o armazenamentos das medias (fotos, videos e arquivos) em disco
 */
class Biblivirti_media {

    protected $CI;

    /**
     * Biblivirti_media constructor.
     */
    public function __construct() {
        // Loading variables
        $this->CI = &get_instance();
    }

    public function save_image($user_id, $image) {
        $data = explode('.', base64_decode($image));
        $image_content = $data[0];
        $image_mime = $data[1];
        $filename = UPLOAD_IMAGES_PATH . 'image' . '-' . $user_id . '-' . date('d/m/Y H:i:s') . $image_mime;
        file_put_contents($filename, $image_content);
        return $filename;
    }

    public function save_video() {
    }

    public function save_file() {
    }

}