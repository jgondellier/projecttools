<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Ob\HighchartsBundle\Highcharts\Highchart;

class EvolutionNbTicketController extends Controller
{
    public function indexAction()
    {
        /*Rendu du graph*/
        $graph['ajax']['url']               = 'evolutionnbticket/graph';
        $graph['id']                        = 'chartContainer';
        $graph_JS     = $this->renderView('IndicateursBundle:Highcharts:javascript.html.twig',array('graph'=>$graph));

        return $this->render('IndicateursBundle:EvolutionNBTcket:index.html.twig',array(
            'activeMenu' => 'evolutionNbTicket',
            'graph_JS'=>$graph_JS,
        ));
    }
    public function GraphAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $year           = $request->get('year');
                $entityManager  = $this->getDoctrine()->getManager();
                $toolrender     = $this->get('indicateurs.rendertools');
                $categories     = array();
                $datas          = array();

                //On fait une requete pour chaque mois de l'année
                $t_evolution    = array();
                for ($mois = 1; $mois <= 12; $mois++) {
                    $t_result = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->evolutionNBTicket($year,$mois);
                    foreach($t_result as $result){
                        $t_evolution[$result['mois']][$mois] = $result["nombre"];
                    }
                    $categories[] = $toolrender->getMonthName($mois);
                }

                //Initialisation du graph
                foreach($t_evolution as $month => $evol){
                    $data=array();
                    for ($mois = 1; $mois <= 12; $mois++) {
                        if(isset($evol[$mois])){
                            $data[] = intval($evol[$mois]);
                        }else{
                            $data[] = 0;
                        }
                    }

                    $datas[] = array(
                        "name" => $toolrender->getMonthName($month),
                        "data" => $data,
                    );
                }

                $ob = new Highchart();
                $ob->chart->renderTo('chartContainer');
                $ob->title->text('Evolution du nombre de tickets pour'.$year);
                $ob->chart->type('column');

                $ob->yAxis->title(array('text' => "Nombre de tickets"));

                $ob->xAxis->title(array('text' => "Mois"));

                $ob->xAxis->categories($categories);

                $ob->plotOptions->column(array(
                    'stacking'  => 'normal',
                    'dataLabels'    => array('enabled' => false),
                ));

                $ob->series($datas);

                return $this->render('@Indicateurs/Highcharts/rendu.html.twig', array(
                    'ob' => $ob
                ));
            }
        }
    }
}