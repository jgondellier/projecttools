<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Ob\HighchartsBundle\Highcharts\Highchart;

class DelaiTraitementController extends Controller
{

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

                /*Tickets avec délai par anne, mois par projet...*/
                $t_delai        = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getDelai($year,$month,$project,$nature,$priority,'TreatmentTime');

                $t_result       = $this->formatForDataTable($t_delai);
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
            $listData['data'][] = array('Annee'=>$data['annee'],'Mois'=>$toolrender->getMonthName($data['mois']),'Projet'=>$list_project[$data['projet']]['name'],'Nature'=>$data['nature'],'Priorite'=>$data['priority'],'JtracId'=>$data['jtracid'],'Delai'=>$data['delai']);
        }

        return $listData;
    }

    /**
     * Permet de faire le graphique en camembert
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function GraphAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $year           = $request->get('year');
                $month          = $request->get('month');
                $project        = $request->get('project');
                $nature         = $request->get('nature');
                $priority       = $request->get('priority');
                $entityManager  = $this->getDoctrine()->getManager();
                $list_project   = $this->container->getParameter('list_project');
                $toolrender     = $this->get('indicateurs.rendertools');

                //On cherche l'id du projet
                foreach($list_project as $idPro => $pro){
                    if($pro["name"] == $project){
                        $project = $idPro;
                    }
                }

                $t_delai        = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getDelai($year,$month,$project,$nature,$priority,'TreatmentTime');
                $contrat        = $this->container->getParameter('contrat');
                $data           = $toolrender->formatDataForPieGraph($t_delai,$contrat);

                $ob = new Highchart();
                $ob->chart->renderTo('correctedChartContainer');
                $ob->title->text('Nombre de ticket par delai de traitement.');

                $ob->plotOptions->pie(array(
                    'allowPointSelect'  => true,
                    'cursor'    => 'pointer',
                    'dataLabels'    => array(
                        'enabled' => true,
                        'format' => '<b>{point.name}</b>: {point.percentage:.1f} %'),
                    'showInLegend'  => false
                ));

                $ob->series(array(array('type' => 'pie','name' => 'Nombre', 'data' => $data)));

                return $this->render('@Indicateurs/Highcharts/rendu.html.twig', array(
                    'ob' => $ob
                ));
            }
        }
    }
}
