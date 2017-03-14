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
        $data = explode('.', $image);
        $image_content = base64_decode($data[0]);
        $image_mime = $data[1];
        $filename = 'image' . '-' . $user_id . '-' . date('d-m-Y', time()) . '-' . date('H-m-s', time()). '.' .$image_mime;
        file_put_contents(base_url(UPLOAD_IMAGES_PATH . $filename), $image_content);
        return $filename;
    }

    public function save_video() {
    }

    public function save_file() {
    }

}