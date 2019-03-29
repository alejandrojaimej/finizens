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
        $error = false;
        $senders = array();
        $fichero = $server->getCallsArray('communications.611111111.log');
        if($fichero !== false){
            if(!empty($fichero)){//si ha datos en el fichero
                //echo '<pre>'.print_r($fichero, true).'</pre>';
                foreach($fichero as $key => $value){
                    if(empty($value)){unset($fichero[$key]);continue;}
                    $valid = false;
                    switch($value[0]){
                        case 'C':
                            $type = 'llamadas';
                            //regex para el formato de las llamadas
                            $re = '/^(C{1})(\d{9})(\d{9})(\d{1})(.{24})(\d{14})(\d{6})/m';
                            preg_match_all($re, $value, $result, PREG_SET_ORDER, 0);
                            if(!$result){break;} //Si no encaja con el regex es una llamada erronea en el fichero
                            $valid = true;
                            $kind = ($result[0][4] == 1 ? 'Entrante' : 'Saliente');
                            $from = ($result[0][4] == 1 ? $result[0][3] : $result[0][2]);
                            $to = ($result[0][4] == 1 ? $result[0][2] : $result[0][3]);
                            $c_name = $result[0][5];
                            $timestamp = $result[0][6];
                            $duration = $result[0][7];
                            $date = substr($timestamp, 0, 2).'-'.substr($timestamp, 2, 2).'-'.substr($timestamp, 4, 4);
                            $time = substr($timestamp, 8, 2).':'.substr($timestamp, 10, 2).':'.substr($timestamp, 12, 2);
                            break;
                        case 'S': 
                            $type = 'mensajes';
                            //regex para el formato de los sms
                            $re = '/^(S{1})(\d{9})(\d{9})(\d{1})(.{24})(\d{14})/m';
                            preg_match_all($re, $value, $result, PREG_SET_ORDER, 0);
                            if(!$result){break;} //Si no encaja con el regex es un mensaje erroneo en el fichero
                            $valid = true;
                            $from = ($result[0][4] == 1 ? $result[0][3] : $result[0][2]);
                            $to = ($result[0][4] == 1 ? $result[0][2] : $result[0][3]);
                            $kind = ($result[0][4] == 1 ? 'Entrante' : 'Saliente');
                            $c_name = $result[0][5];
                            $timestamp = $result[0][6];
                            $date = substr($timestamp, 0, 2).'-'.substr($timestamp, 2, 2).'-'.substr($timestamp, 4, 4);
                            $time = substr($timestamp, 8, 2).':'.substr($timestamp, 10, 2).':'.substr($timestamp, 12, 2);
                            break;
                        default: continue; break;
                    }
                    
                    if($valid){
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
                            $senders[$from][$to]['c_name'] = $c_name; //nombre del contacto en la agenda
                            $senders[$from][$to][$type] = array();
                        }
                        

                        if($value[0] == 'C'){
                            $senders[$from][$to][$type][] = ['kind'=>$kind, 'date' => $date, 'time'=>$time, 'duration' => $duration];
                        }else if($value[0] == 'S'){
                            $senders[$from][$to][$type][] = ['kind'=>$kind, 'date' => $date, 'time'=>$time];
                        }
                    }
                }
            }else{
                $error = 'El fichero está vacío!';
            }
        }else{
            $error = 'El servidor no responde!';
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'error' => $error,
            'data' => $senders
        ]);
    }
}
