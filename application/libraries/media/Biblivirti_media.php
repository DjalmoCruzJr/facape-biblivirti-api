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

    /**
     * @param $user_id
     * @param $image
     * @return null|string
     *
     * Metodo para salvar em disco as medias do tipo IMAGEM
     */
    public function save_image($user_id, $image) {
        try {
            $data = explode('.', $image);
            $image_content = base64_decode($data[0]);
            $image_mime = $data[1];
            $filename = 'image' . '-' . $user_id . '-' . date('d-m-Y', time()) . '-' . date('H-m-s', time()) . '.' . $image_mime;
            if (file_put_contents(ROOT_DIR . UPLOAD_IMAGES_PATH . $filename, $image_content) == false) {
                return null;
            }
            return $filename;
        } catch (Exception $e) {
            return null;
        }

    }

    public function save_video() {
    }

    public function save_file() {
    }

    /**
     * @param null $image_name
     * @return bool
     *
     * Metodo para excluir do disco as medias do tipo IMAGEM
     */
    public function delete_image($image_name = null) {
        if (is_null($image_name)) {
            return true;
        } else {
            $filename = ROOT_DIR . UPLOAD_IMAGES_PATH . $image_name;
            if (file_exists($filename)) {
                unlink($filename);
                return true;
            } else {
                return false;
            }
        }
    }

}