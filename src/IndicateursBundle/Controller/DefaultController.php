<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IndicateursBundle\Repository\Indic_itemsRepository;

class DefaultController extends Controller
{
    public function indexAction()
    {
        //$entityManager = $this->getDoctrine()->getManager();

        /*Tickets ouvert fermé par moi par projet*/
        /*$t_open = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getDateByMonthProject('2016','openDate');
        $t_closed = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getDateByMonthProject('2016','closedDate');

        var_dump($t_open);
*/
        /*$t_openClosed = array();
        foreach ($t_open as $open){
            $t_openClosed[$open['mois']]
        }*/


        /*Délai de traitement par mois
        Anomalie
        Support
        Evolutions
        */






        return $this->render('IndicateursBundle:Default:index.html.twig');
    }

}
