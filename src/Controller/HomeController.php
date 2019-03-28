<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Utils\Server;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Server $server)
    {
        $fichero = $server->getCallsArray('communications.611111111.log');
        if($fichero !== false){
            if(!empty($fichero)){//si ha datos en el fichero

                $senders = array();

                foreach($fichero as $key => $value){
                    if(empty($value)){unset($fichero[$key]);continue;}
                    switch($value[0]){
                        case 'C':
                            $type = 'llamadas';
                            $from = substr($value, 1, 9); 
                            $to = substr($value, 10, 9);
                            $kind = (substr($value, 19, 1) == 1 ? 'entrante' : 'saliente');
                            $c_name = substr($value, 20, 24);
                            $timestamp = substr($value, 44, 14);
                            $date_time = substr($timestamp, 0, 2).'-'.substr($timestamp, 2, 2).'-'.substr($timestamp, 4, 4).' '.substr($timestamp, 8, 2).':'.substr($timestamp, 10, 2).'-'.substr($timestamp, 12, 2);                            
                            $duration = substr($value, 58,6);
                            break;
                        case 'S': 
                            $type = 'mensajes';
                            $from = substr($value, 1, 9); 
                            $to = substr($value, 10, 9);
                            $kind = (substr($value, 19, 1) == 1 ? 'entrante' : 'saliente');
                            $c_name = substr($value, 20, 24);
                            $timestamp = substr($value, 44, 14);
                            $date_time = substr($timestamp, 0, 2).'-'.substr($timestamp, 2, 2).'-'.substr($timestamp, 4, 4).' '.substr($timestamp, 8, 2).':'.substr($timestamp, 10, 2).'-'.substr($timestamp, 12, 2);                                                        
                            break;
                        default: break;
                    }
                    //agrupar por el telefono propio
                    if(!isset($senders[$from])){
                        $senders[$from] = array();
                    }
                    //agrupar por destinatario
                    if(!isset($senders[$from][$to])){
                        $senders[$from][$to] = array();
                    }
                    //agrupar por tipo de comunicación
                    if(!isset($senders[$from][$to][$type])){
                        $senders[$from][$to][$type] = array();
                    }
                    $senders[$from][$to][$type]['kind'] = $kind;
                    $senders[$from][$to][$type]['c_name'] = $c_name;
                    $senders[$from][$to][$type]['timestamp'] = $timestamp;
                    $senders[$from][$to][$type]['date_time'] = $date_time;
                    if($value[0] == 'C'){
                        $senders[$from][$to][$type]['duration'] = $duration;
                    }
                }
                echo '<pre>'.print_r($senders, true).'</pre>';
                exit;
            }else{
                //fichero vacío
            }
        }else{
            //respuesta incorrecta del servidor
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
