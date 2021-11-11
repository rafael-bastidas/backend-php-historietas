<?php

/*
* =======================================================================================
*                               ARCHIVO CONTENIDO-CONTROLLER
* =======================================================================================
*   Este archivo se encarga de ejecutar las funciones para manipular la tabla citas_atendidas
*   segun las peticiones, mediante el archivo service/conectionDB.php.
*
*   Ejemplo de Dato:
*        $data = array(
*           'tipocita'      => 'consulta presencial',
*           'horacita'      => '1',
*           'nombre'        => 'rafael bastidas',
*           'email'         => 'rafaelbastidas93@gmail.com',
*           'locacion'      => 'Maracay - VE',
*           'dni'           => '21272288',
*           'phone'         => '+584243617241',
*           'referenciado'  => 'PERSONAL',
*           'fecha'         => '2020-04-20',
*           'tipopago'      => 'zelle',
*           'imagePath'     => '',
*           'nacimiento'    => '2020-04-20',
*           'accion'        => 'cancelada o atendida'
*        );
*   
*   Para getContenidoInit y deleteCitasAtendidas el Dato sera una clausula WHERE:
*       $data = array(
*           'nickname'          => 'don_quijose'
*        );
*
*   Fines de prueba:
*       echo json_encode( array('response' => $response, 'sql' => $sql, 'request' => $data) );
*   SELECT * FROM series INNER JOIN myseries ON series.id_serie = myseries.id_serie;
*/

    include_once("service/conectionDB.php");

    function getContenidoInit($data){
        
        $array_config_init = array();

        $sql = organizarSQL("select", "series", "id_serie,titulo,descripcion,imgportada", "INNER JOIN myseries ON series.id_serie = myseries.id_serie WHERE myseries.id_user = {$data['id_user']}");
        $array_config_init["myseries"] = getArraySQL($sql);
        
        $sql = organizarSQL("select", "series", "id_serie,titulo,descripcion,imgportada", "INNER JOIN mysuscribe ON series.id_serie = mysuscribe.id_serie WHERE mysuscribe.id_user = {$data['id_user']}");
        $array_config_init["mysuscribe"] = getArraySQL($sql);

        $sql = organizarSQL("select", "series", "id_serie,titulo,descripcion,imgportada", "none");
        $array_config_init["series"] = getArraySQL($sql);

        return $array_config_init;
    }

    function getContenidoSerie($data){

        $array_response = array();

        $sql = organizarSQL("select", "series", "id_serie,titulo,descripcion,imgportada", "WHERE series.id_serie = {$data['id_serie']}");
        $array_response['series'] = getArraySQL($sql);

        return $array_response;
    }

    function getContenidoEpisodio($data){

        $array_response = array();

        $sql = organizarSQL("select", "episodios", "id_episodio,id_serie,number_episodio", "WHERE episodios.id_serie = {$data['id_serie']}");
        $array_response['episodios'] = getArraySQL($sql);

        //Falata pedir los comentarios AQUI

        return $array_response;
    }

    function getContenidoFotograma($data){

        $array_response = array();

        $sql = organizarSQL("select", "fotogramas", "id_episodio,id_fotograma,number_fotograma,imagen", "WHERE fotogramas.id_episodio = {$data['id_episodio']}");
        $array_response['fotogramas'] = getArraySQL($sql);

        //Falata pedir los comentarios AQUI

        return $array_response;
    }

    function getContenidoEdit($data){

        $array_response = array();

        $sql = organizarSQL("select", "series", "id_serie,titulo,descripcion,imgportada", "WHERE series.id_serie = {$data['id_serie']}");
        $array_response['series'] = getArraySQL($sql);

        $sql = organizarSQL("select", "episodios", "id_episodio,id_serie,number_episodio", "WHERE episodios.id_serie = {$data['id_serie']}");
        $array_response['episodios'] = getArraySQL($sql);

        //$sql = organizarSQL("select", "fotogramas", "id_episodio,id_fotograma,number_fotograma,imagen", "WHERE fotogramas.id_episodio = {$data['id_episodio']}");
        //$array_response['fotogramas'] = getArraySQL($sql);

        return $array_response;
    }

    function updateInsertContenido($data){

        if (isset($_FILES['file_imgportada'])) {
            $nombrearchivo = $_FILES['file_imgportada']['name'];
            $archivotemporal = $_FILES['file_imgportada']['tmp_name'];
            $dir_subida = '/home/rafaelba/public_html/apis/api-historieta/uploads/imgportadas/';
            $fichero_subida = $dir_subida . basename($nombrearchivo);
            if (move_uploaded_file($archivotemporal, $fichero_subida)) {
                $data['series']['imgportada'] = $fichero_subida;
            } else {
                $data['series']['imagePath'] = '';
            }
        }
        if (isset($_FILES['files_imgfotogramas'])) {
            $nombrearchivo = $_FILES['files_imgfotogramas']['name'];
            $archivotemporal = $_FILES['files_imgfotogramas']['tmp_name'];
            $dir_subida = '/home/rafaelba/public_html/apis/api-historieta/uploads/imgfotogramas/';
            $fichero_subida = $dir_subida . basename($nombrearchivo);
            if (move_uploaded_file($archivotemporal, $fichero_subida)) {
                $data['fotogramas']['imagen'] = $fichero_subida;
            } else {
                $data['fotogramas']['imagen'] = '';
            }
        }

        $sql = isset($data['series']['id_serie']) ? organizarSQL("update", "series", $data['series'], "id_serie = {$data['series']['id_serie']}") : organizarSQL("insert", "series", $data['series'], "null");
        $array_response['series'] = getBooleanSQL($sql);

        if(isset($data['episodios'])){
            $sql = isset($data['episodios']['id_episodio']) ? organizarSQL("update", "episodios", $data['episodios'], "id_episodio = {$data['episodios']['id_episodio']}") : organizarSQL("insert", "episodios", $data['episodios'], "null");
            $array_response['episodios'] = getBooleanSQL($sql);

            //sendNotification(new episode);
        }
        
        if(isset($data['fotogramas'])){
            $sql = isset($data['fotogramas']['id_fotograma']) ? organizarSQL("update", "fotogramas", $data['fotogramas'], "id_fotograma = {$data['fotogramas']['id_fotograma']}") : organizarSQL("insert", "fotogramas", $data['fotogramas'], "null");
            $array_response['fotogramas'] = getBooleanSQL($sql);

            //sendNotification(new fotograma del episode);
        }

        return $array_response;
    }

    function deleteCitasAtendidas($data){

        $sql = organizarSQL("delete", "citas_atendidas", "null", $data['clausula_where']);
        $response = getBooleanSQL($sql);
        return $response;
    }

?>