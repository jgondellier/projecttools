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
        $toolrender     = $this->get('indicateurs.rendertools');
        $jtrac_url   = $this->container->getParameter('jtrac_url');
        /*Rendu du tableau */
        $table['ajax']['url']           = $this->generateUrl('indicateurs_evolutionNBTicket_table');
        //$table['ajax']['datas'][]       = array('name'=>'year','value'=>'2016');
        //$table['ajax']['datas'][]       = array('name'=>'month','value'=>'12');
        $table['id']                    = 'ticketTable';
        $table                          = $toolrender->initColTable($table);
        $table['cols'][]                = array('filter'=>1,'name'=>'Status','data'=>'Status');
        $table['cols'][]                = array('filter'=>0,'name'=>'JtracId','data'=>'JtracId','href'=>$jtrac_url);
        $table_HTML = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_JS = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table));

        /*Rendu du graph*/
        $graph['ajax']['url']               = $this->generateUrl('indicateurs_evolutionNBTicket_graph');
        $graph['id']                        = 'chartContainer';
        $graph_HTML             = $this->renderView('IndicateursBundle:Highcharts:chart.html.twig',array('graph'=>$graph));
        $graph_JS               = $this->renderView('IndicateursBundle:Highcharts:chart_javascript.html.twig',array('graph'=>$graph));


        return $this->render('IndicateursBundle:EvolutionNBTcket:index.html.twig',array(
            'activeMenu' => 'evolutionNbTicket',
            'graph_HTML'=>$graph_HTML,
            'graph_JS'=>$graph_JS,
            'table_HTML'=>$table_HTML,
            'table_JS'=>$table_JS,
        ));
    }
    public function GraphAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();
                $toolrender     = $this->get('indicateurs.rendertools');
                $categories     = array();
                $datas          = array();

                //On récupère la liste des mois et année des tickets disponible
                $t_listDate     = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getListeDateTickets();

                //On fait une requete pour chaque mois de l'année
                $t_evolution    = array();
                foreach ($t_listDate as $date){
                    $t_result = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->evolutionNBTicket($date['annee'],$date['mois']);
                    foreach($t_result as $result){
                        $t_evolution[$result['annee']][$result['mois']][$date['annee'].$date['mois']] = $result["nombre"];
                    }
                    $categories[] = $toolrender->getMonthName($date['mois']);
                }

                //Initialisation du graph
                foreach($t_evolution as $year => $t_month) {
                    foreach ($t_month as $month => $evol) {
                        $data = array();
                        foreach ($t_listDate as $date) {
                            if (isset($evol[$date['annee'].$date['mois']])) {
                                $data[] = intval($evol[$date['annee'].$date['mois']]);
                            } else {
                                $data[] = 0;
                            }
                        }

                        $datas[] = array(
                            "name" => $toolrender->getMonthName($month).' '.$year,
                            "data" => $data,
                        );
                    }
                }

                $ob = new Highchart();
                $ob->chart->renderTo('chartContainer');
                $ob->title->text('Evolution du nombre de tickets pour '.$year);
                $ob->chart->type('column');
                $ob->legend->enabled(false);

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

    public function TableAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();
                $response       = new JsonResponse();

                $t_listDate     = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getLastDate();

                /*Tickets non ferme et non corrigé par mois*/
                $t_Ticket     = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->evolutionNBTicket($t_listDate[0]['annee'],$t_listDate[0]['mois'],True);

                $response->setContent(json_encode($this->formatForDataTable($t_Ticket)));
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
            $listData['data'][] = array('Annee'=>$data['annee'],'Mois'=>$toolrender->getMonthName($data['mois']),'Projet'=>$list_project[$data['projet']]['name'],'Nature'=>$data['nature'],'Priorite'=>$data['priority'],'Status'=>$list_project[$data['projet']]['status'][$data['status']],'JtracId'=>$data['jtracid']);
        }

        return $listData;
    }
}