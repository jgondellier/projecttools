<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Ob\HighchartsBundle\Highcharts\Highchart;

class SupAnoForfaitController extends Controller
{
    public function indexAction()
    {

        return $this->render('IndicateursBundle:Default:index.html.twig',array('activeMenu' => 'homepage'));
    }

    public function getCountSupAnoForfaitAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();
                $response       = new JsonResponse();
                $contrat        = $this->container->getParameter('contrat');
                $nbForfait      = $contrat["nbsupano"];

                $nbSupAno       = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getCountSupAnoByDateCreated();
                $nbSupAno       = $nbSupAno['total'];
                $pourcent       = round($nbSupAno/$nbForfait*100);

                $template       = $this->renderView('IndicateursBundle:SupAnoForfait:count.html.twig',array("count"=>$nbSupAno,"nbForfait"=>$nbForfait,"pourcent"=>$pourcent));

                $response->setContent(json_encode($template));
                return $response;

            }
        }
    }

}
