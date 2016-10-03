<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IndicateursBundle\Repository\Indic_itemsRepository;

class DefaultController extends Controller
{
    public function indexAction()
    {

        /*Délai de traitement par mois
        Anomalie
        Support
        Evolutions
        */
        $entityManager = $this->getDoctrine()->getManager();
        $arrayREsult = $entityManager->getRepository('IndicateursBundle:Indic_items')->getItemsArray();

        return $this->render('IndicateursBundle:Default:index.html.twig');
    }
}
