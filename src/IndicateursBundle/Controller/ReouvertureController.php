<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Ob\HighchartsBundle\Highcharts\Highchart;

class ReouvertureController extends Controller
{
    public function indexAction()
    {
        $toolrender     = $this->get('indicateurs.rendertools');

        /*Rendu du graph*/
        $graph['ajax']['url']           = $this->generateUrl('indicateurs_reouverture_graph');
        $graph['id']                    = 'chartContainer';
        $graph_HTML                     = $this->renderView('IndicateursBundle:Highcharts:chart.html.twig',array('graph'=>$graph));
        $graph_JS                       = $this->renderView('IndicateursBundle:Highcharts:chart_javascript.html.twig',array('graph'=>$graph));

        /*Rendu du tableau */
        $table['ajax']['url']           = $this->generateUrl('indicateurs_reouverture_table');
        //$table['ajax']['datas'][]       = array('name'=>'year','value'=>'2016');
        $table['id']                    = 'reopenTable';
        $table                          = $toolrender->initColTable($table);
        $table['cols'][]                = array('filter'=>0,'name'=>'Réouverture','data'=>'Reouverture');
        $table_HTML                     = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_JS                       = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table,'graph'=>$graph));

        return $this->render('IndicateursBundle:Reouverture:index.html.twig',array(
            'activeMenu' => 'reouverture',
            'graph_HTML'=>$graph_HTML,
            'graph_JS'=>$graph_JS,
            'table_HTML'=>$table_HTML,
            'table_JS'=>$table_JS,
        ));
    }
    public function TableAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();
                $year           = $request->get('year');
                $project        = $request->get('project');
                $month          = $request->get('month');
                $nature         = $request->get('nature');
                $priority       = $request->get('priority');
                $response       = new JsonResponse();

                /*Tickets réouvert par mois par projet*/
                $t_reopen         = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getRefusedCountByMonthProject($year,$month,$project,$nature,$priority);

                $t_result       = $this->formatForDataTable($t_reopen);
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
                $month          = $request->get('month');
                $nature         = $request->get('nature');
                $priority       = $request->get('priority');
                $list_project   = $this->container->getParameter('list_project');
                $toolrender     = $this->get('indicateurs.rendertools');

                //On cherche l'id du projet
                foreach($list_project as $idPro => $pro){
                    if($pro["name"] == $project){
                        $project = $idPro;
                    }
                }

                $t_reopen         = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getRefusedCountByMonthProject($year,$month,$project,$nature,$priority);

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
    /**
     * Formate les données pour les afficher dans un tableau DataTable
     *
     * @param $t_data
     * @return array
     */
    private function formatForDataTable($t_data){
        $list_project   = $this->container->getParameter('list_project');
        $toolrender     = $this->get('indicateurs.rendertools');
        $listData       = array();

        //Formalisation de la donnée
        foreach ($t_data as $data){
            $listData['data'][] = array('Annee'=>$data['annee'],'Mois'=>$toolrender->getMonthName($data['mois']),'Projet'=>$list_project[$data['projet']]['name'],'Nature'=>$data['nature'],'Priorite'=>$data['priority'],'Reouverture'=>$data['somme']);
        }

        return $listData;
    }
}
