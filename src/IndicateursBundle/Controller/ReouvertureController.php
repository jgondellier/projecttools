<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Ob\HighchartsBundle\Highcharts\Highchart;

class ReouvertureController extends Controller
{
    public function indexAction()
    {

        return $this->render('IndicateursBundle:Reouverture:index.html.twig',array('activeMenu' => 'reouverture'));
    }
    public function GraphAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();
                $year           = $request->get('year');
                $project        = $request->get('project');
                $list_project   = $this->container->getParameter('list_project');
                $toolrender     = $this->get('indicateurs.rendertools');

                //On cherche l'id du projet
                foreach($list_project as $idPro => $pro){
                    if($pro["name"] == $project){
                        $project = $idPro;
                    }
                }
                $t_reopen         = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getRefusedCountByMonthProject($year,$project);
                $monthInterval    = $toolrender->getMonthInterval($t_reopen);
                $t_reopen         = $toolrender->formatData($t_reopen,$monthInterval);

                //Initialisation du graph
                $data = array(
                    array(
                        'type' => 'column',
                        "name" => "Nombre de tickets réouvert.",
                        "data" => $t_reopen
                    )
                );

                $ob = new Highchart();
                $ob->chart->renderTo('chartContainer');
                $ob->title->text('Nombre de Tickets Réouvert pour '.$year);
                //$ob->chart->type('column');

                $ob->yAxis->title(array('text' => "Nombre de tickets"));

                $ob->xAxis->title(array('text' => "Mois"));
                $ob->xAxis->categories($toolrender->killindexMonth($monthInterval));

                $ob->series($data);

                return $this->render('@Indicateurs/Highcharts/rendu.html.twig', array(
                    'ob' => $ob
                ));

            }
        }
    }

}
