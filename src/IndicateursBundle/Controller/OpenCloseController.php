<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use IndicateursBundle\Repository\Indic_itemsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Ob\HighchartsBundle\Highcharts\Highchart;

class OpenCloseController extends Controller
{
    public function indexAction()
    {

        return $this->render('IndicateursBundle:OpenClose:index.html.twig',array('activeMenu' => 'openclose'));
    }

    public function TableAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();
                $year           = $request->get('year');
                $project        = $request->get('project');
                $response       = new JsonResponse();

                /*Tickets ouverts fermés par moi par projet*/
                $t_open         = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getDateByMonthProject($year,$project,'openDate');
                $t_closed       = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getDateByMonthProject($year,$project,'closedDate');

                $t_result       = $this->formatForDataTable($t_open,$t_closed);
                $response->setContent(json_encode($t_result));
                return $response;
            }
        }else{
            throw new AccessDeniedException('Access denied');
        }
        return NULL;
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

                //Recuperation des données
                $t_open         = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getDateByMonthProject($year,$project,'openDate');
                $t_close        = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getDateByMonthProject($year,$project,'closedDate');

                $monthInterval  = $toolrender->getMonthInterval($t_open,$t_close);

                $t_open         = $toolrender->formatData($t_open,$monthInterval);
                $t_close        = $toolrender->formatData($t_close,$monthInterval);

                //Initialisation du graph
                $data = array(
                    array(
                        'type' => 'column',
                        "name" => "Nombre de Tickets ouvert.",
                        "data" => $t_open
                    ),
                    array(
                        'type' => 'column',
                        "name" => "Nombre de Tickets fermé.",
                        "data" => $t_close
                    )
                );

                $ob = new Highchart();
                $ob->chart->renderTo('chartContainer');
                $ob->title->text('Nombre de Tickets Ouvert/Fermé pour '.$year);
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

    /**
     * Formate les donnée ouverture fermeture pour les afficher dans un tablea DataTable
     *
     * @param $t_open
     * @param $t_closed
     * @return array
     */
    private function formatForDataTable($t_open,$t_closed){
        $list_project   = $this->container->getParameter('list_project');
        $t_openClose    = array();
        $t_result       = array();
        $toolrender     = $this->get('indicateurs.rendertools');

        //Formalisation de la donnée
        foreach ($t_open as $open){
            $t_openClose[$toolrender->getMonthName($open['mois'])][$list_project[$open['projet']]['name']]['Ouverture']=$open['somme'];
        }
        foreach ($t_closed as $closed){
            $t_openClose[$toolrender->getMonthName($closed['mois'])][$list_project[$closed['projet']]['name']]['Fermeture']=$closed['somme'];
        }

        //Init des valeurs null
        foreach($t_openClose as $month=>$listProjet){
            foreach($listProjet as $project=>$openClose){
                $openCount = 0;
                $closeCount = 0;
                if(isset($openClose['Ouverture'])){
                    $openCount = $openClose['Ouverture'];
                }
                if(isset($openClose['Fermeture'])){
                    $closeCount = $openClose['Fermeture'];
                }
                $t_result['data'][] = array('Mois'=>$month,'Projet'=>$project,'Ouverture'=>$openCount,'Fermeture'=>$closeCount);
            }
        }
        return $t_result;
    }
}