<?php

/**
 * @author djalmocruzjr (djalmo.cruz@gmail.com)
 * @version 1.0
 * @since 13/02/2017
 *
 * Classe para fazer o armazenamentos das medias (fotos, videos e arquivos) em disco
 */
class Biblivirti_media
{

    protected $CI;

    /**
     * Biblivirti_media constructor.
     */
    public function __construct()
    {
        // Loading variables
        $this->CI = &get_instance();
    }

    /**
     * @param $user_id
     * @param $image
     * @return null|string
     *
     * Metodo para salvar em disco as imagens enviadas para a aplicacao
     */
    public function save_image($user_id, $image)
    {
        try {
            $data = explode('.', $image);
            $image_content = base64_decode($data[0]);
            $image_mime = $data[1];
            $filename = 'image' . '-' . $user_id . '-' . date('d-m-Y', time()) . '-' . date('H-m-s', time()) . '.' . $image_mime;

            if (file_put_contents(ROOT_DIR . UPLOAD_IMAGES_PATH . $filename, $image_content) === false) {
                return null;
            }
            return $filename;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param $user_id
     * @param $file
     * @return null|string
     *
     * Metodo para salvar em disco os arquivos enviados para a aplicacao
     */
    public function save_file($user_id, $file)
    {
        try {
            $data = explode('.', $file);
            $file_content = base64_decode($data[0]);
            $file_mime = $data[1];
            $filename = 'file' . '-' . $user_id . '-' . date('d-m-Y', time()) . '-' . date('H-m-s', time()) . '.' . $file_mime;
            if (file_put_contents(ROOT_DIR . UPLOAD_FILES_PATH . $filename, $file_content) === false) {
                return null;
            }
            return $filename;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param null $image_name
     * @return bool
     *
     * Metodo para excluir do disco as imagens enviadas para a aplicacao
     */
    public function delete_image($image_name = null)
    {
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

    /**
     * @param null $file_name
     * @return bool
     *
     * Metodo para excluir do disco os arquivos enviados para a aplicacao
     */
    public function delete_file($file_name = null)
    {
        if (is_null($file_name)) {
            return true;
        } else {
            $filename = ROOT_DIR . UPLOAD_FILES_PATH . $file_name;
            if (file_exists($filename)) {
                unlink($filename);
                return true;
            } else {
                return false;
            }
        }
    }

}