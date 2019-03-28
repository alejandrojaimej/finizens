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
        $resp = $server->getCallsArray('communications.611111111.log');
        if($resp !== false){ //si el servidor devuelve lo que esperamos
            dump($resp);
        }
        //dump($resp);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
