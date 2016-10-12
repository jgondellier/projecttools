<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Ob\HighchartsBundle\Highcharts\Highchart;

class DelaiTraitementController extends Controller
{
    public function indexAction()
    {

        return $this->render('IndicateursBundle:Default:index.html.twig',array('activeMenu' => 'homepage'));
    }

    public function TableAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();
                $year           = $request->get('year');
                $month          = $request->get('month');
                $project        = $request->get('project');
                $nature         = $request->get('nature');
                $priority       = $request->get('priority');
                $response       = new JsonResponse();

                /*Tickets ouverts fermés par mois par projet*/
                $t_open         = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getDateByMonthProject($year,$month,$project,$nature,$priority,'openDate');
                $t_closed       = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getDateByMonthProject($year,$month,$project,$nature,$priority,'closedDate');

                $t_result       = $this->formatForDataTable($t_open,$t_closed);
                $response->setContent(json_encode($t_result));
                return $response;
            }
        }else{
            throw new AccessDeniedException('Access denied');
        }
        return NULL;
    }

    private function formatForDataTable($t_data){
        $list_project   = $this->container->getParameter('list_project');
        $toolrender     = $this->get('indicateurs.rendertools');
        $listData       = array();

        //Formalisation de la donnée
        foreach ($t_data as $data){
            $listData['data'][] = array('Mois'=>$toolrender->getMonthName($data['mois']),'Projet'=>$list_project[$data['projet']]['name'],'Nature'=>$data['nature'],'Priorite'=>$data['priority'],'Reouverture'=>$data['somme']);
        }

        return $listData;
    }
}
