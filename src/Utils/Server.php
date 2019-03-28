<?php

namespace App\Utils;

class Server
{
    /**
     * Petición a nuestro servidor
     */
    public function request($fichero = false){
        if(!$fichero){return false;}
        $ch = curl_init();
        $parsedURL = "https://gist.githubusercontent.com/miguelgf/e099e5e5bfde4f6428edb0ae94946c83/raw/fa27e59eb8f14ee131fca5c0c7332ff3b924e0b2/$fichero";
        curl_setopt($ch, CURLOPT_URL,$parsedURL);    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $server_output = curl_exec ($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);
        if($httpCode == 200){ //si hay algun error en las cabeceras de respuesta
            return $server_output;
        }else{
            return false;
        }
    }

    /**
     * Tratamiento de la respuesta del servidor
     */
    public function getCallsArray($fichero){
        $resp = $this->request($fichero);
        if($resp !== false){
            return explode(PHP_EOL, $resp);
        }else{
            return false;
        }
    }
}