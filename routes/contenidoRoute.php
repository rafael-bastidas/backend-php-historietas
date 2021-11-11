<?php

/*
* =======================================================================================
*                               ARCHIVO CONTENIDO-ROUTE
* =======================================================================================
*   Este archivo se encarga de cargar la funcion del controlar correspondiente a las rutas
*   de CONTENIDO, las funciones estan definidas en la carpeta CONTROLLERS.
*
*   Tipos de params:
*      
*          GET:     -->     solicitud para OBTENER
*          POST:    -->     solicitud para INSERTAR
*          DELETE   -->     solicitud para ELIMINAR
*
*   
*/

    include_once("controllers/contenidoController.php");

    function contenido($request){
        switch ($request["params"]) {
            case 'GET-INIT':
                $response = getContenidoInit($request["data"]);
                break;
            case 'POST':
                $response = updateInsertContenido($request["data"]);
                break;
            case 'DELETE':
                $response = deleteContenido($request["data"]);
                break;
            default:
                $response = "params de la peticion invalida";
                break;
        }

        error_log("Respuesta: " . $response, 0);
        header("Content-Type: application/json");
        $response = array("response" => $response);
        echo json_encode($response);
    }

?>