<?php

/**
 * Class Material_helper
 * @author djalmocruzjr
 * @since 13/10/2016
 * @version 1.0
 *
 * Classe Helper que retornar as descricoes para cada tipo de material
 */
class Material_helper {

    /**
     * @param $mactipo
     * @return null|string
     *
     * Metodo para retornar as descricoes para cada tipo de material
     */
    public function get_description($mactipo) {
        $result = null;

        switch ($mactipo) {
            case MACTIPO_APRESENTACAO:
                $result = 'Apresentação';
                break;
            case MACTIPO_EXERCICIO:
                $result = 'Exercício';
                break;
            case MACTIPO_FORMULA:
                $result = 'Fórmula';
                break;
            case MACTIPO_JOGO:
                $result = 'Fórmula';
                break;
            case MACTIPO_LIVRO:
                $result = 'Livro';
                break;
            case MACTIPO_SIMULADO:
                $result = 'Simulado';
                break;
            case MACTIPO_VIDEO:
                $result = 'Vídeo';
                break;
            default:
                $result = null;
        }

        return $result;
    }

}