<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Tout ce qui concerne le delai de traitement des tickets support
 *
 * Class DelaiTmntSupportController
 * @package IndicateursBundle\Controller
 */
class DelaiTmntSupportController extends Controller
{
    public function indexAction()
    {

    }

    public function DelaiTtmntSupportAction(Request $request){
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();
                $year           = $request->get('year');
                $response       = new JsonResponse();

                $count          = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->delaiTtmntSupport($year);

                $delai          = round(($count['delaiTotal']/$count['nb'])/60,2);

                $template       = $this->renderView('IndicateursBundle:DelaiTraitement:support.panel.html.twig',array("delai"=>$delai));

                $response->setContent(json_encode($template));
                return $response;
            }
        }
    }
    public function DelaiReponseSupportAction(Request $request){
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();
                $year           = $request->get('year');
                $response       = new JsonResponse();

                $count          = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->delaiReponseSupport($year);

                $delai          = round(($count['delaiTotal']/$count['nb'])/60,2);

                $template       = $this->renderView('IndicateursBundle:DelaiReponse:support.panel.html.twig',array("delai"=>$delai));

                $response->setContent(json_encode($template));
                return $response;
            }
        }
    }
}
