<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Ob\HighchartsBundle\Highcharts\Highchart;

class OpenCloseController extends Controller
{
    /**
     * Homepage du nombre des Ouvertures/Fermetures des tickets
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $toolrender     = $this->get('indicateurs.rendertools');

        /*Rendu du graph*/
        $graph['ajax']['url']   = $this->generateUrl('indicateurs_openclose_graph');
        $graph['id']            = 'chartContainer';
        $graph_HTML             = $this->renderView('IndicateursBundle:Highcharts:chart.html.twig',array('graph'=>$graph));
        $graph_JS               = $this->renderView('IndicateursBundle:Highcharts:chart_javascript.html.twig',array('graph'=>$graph));

        /*Rendu du tableau */
        $table['ajax']['url']                = $this->generateUrl('indicateurs_openclose_table');
        //$table['ajax']['datas'][]            = array('name'=>'year','value'=>'2016');
        $table['id']        = 'opencloseTable';
        $table              = $toolrender->initColTable($table);
        $table['cols'][]    = array('filter'=>0,'name'=>'Ouvert','data'=>'Ouverture');
        $table['cols'][]    = array('filter'=>0,'name'=>'Fermé','data'=>'Fermeture');
        $table_HTML   = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_JS     = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table,'graph'=>$graph));

        return $this->render('IndicateursBundle:OpenClose:index.html.twig',array(
            'activeMenu' => 'openclose',
            'graph_HTML'=>$graph_HTML,
            'graph_JS'=>$graph_JS,
            'table_HTML'=>$table_HTML,
            'table_JS'=>$table_JS,
        ));
    }

    /**
     * Retourne les résultats Open/close pour les afficher dans un tableau
     *
     * @param Request $request
     * @return null|JsonResponse
     */
    public function TableAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $year           = $request->get('year');
                $month          = $request->get('month');
                $project        = $request->get('project');
                $nature         = $request->get('nature');
                $priority       = $request->get('priority');
                $response       = new JsonResponse();

                /*Tickets ouverts fermés par mois par projet*/
                $t_open         = $this->getData($year,$month,$project,$nature,$priority,'openDate');
                $t_closed       = $this->getData($year,$month,$project,$nature,$priority,'closedDate');

                $t_result       = $this->formatForDataTable($t_open,$t_closed);
                $response->setContent(json_encode($t_result));
                return $response;
            }
        }else{
            throw new AccessDeniedException('Access denied');
        }
        return NULL;
    }

    /**
     * * Retourne les résultats Open/close pour les afficher dans un graphique
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function GraphAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $year           = $request->get('year');
                $month          = $request->get('month');
                $project        = $request->get('project');
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

                //Recuperation des données
                $t_open         = $this->getData($year,$month,$project,$nature,$priority,'openDate');
                $t_close        = $this->getData($year,$month,$project,$nature,$priority,'closedDate');

                $monthInterval  = $toolrender->getMonthInterval($t_open,$t_close);

                $t_open         = $toolrender->formatData($t_open,$monthInterval);
                $t_close        = $toolrender->formatData($t_close,$monthInterval);

                //Initialisation du graph
                $data = array(
                    array(
                        'type' => 'column',
                        "name" => "Tickets ouverts.",
                        "data" => $t_open
                    ),
                    array(
                        'type' => 'column',
                        "name" => "Tickets fermés.",
                        "data" => $t_close
                    )
                );

                $ob = new Highchart();
                $ob->chart->renderTo('chartContainer');
                $titleTexte = 'Nombre de Tickets Ouverts/Fermés';
                if($year){
                    $titleTexte .= ' pour '.$year;
                }
                $ob->title->text($titleTexte);

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
            $t_openClose[$open['annee']][$toolrender->getMonthName($open['mois'])][$list_project[$open['projet']]['name']][$open['nature']][$open['priority']]['Ouverture']=$open['somme'];
        }
        foreach ($t_closed as $closed){
            $t_openClose[$closed['annee']][$toolrender->getMonthName($closed['mois'])][$list_project[$closed['projet']]['name']][$closed['nature']][$closed['priority']]['Fermeture']=$closed['somme'];
        }

        //Init des valeurs null
        foreach($t_openClose as $year=>$listMonth){
            foreach($listMonth as $month=>$listProjet){
                foreach($listProjet as $project=>$listNature){
                    foreach($listNature as $nature=>$listPriority){
                        foreach($listPriority as $priority=>$openClose){
                            $openCount = 0;
                            $closeCount = 0;
                            if(isset($openClose['Ouverture'])){
                                $openCount = $openClose['Ouverture'];
                            }
                            if(isset($openClose['Fermeture'])){
                                $closeCount = $openClose['Fermeture'];
                            }
                            $t_result['data'][] = array('Annee'=>$year,'Mois'=>$month,'Projet'=>$project,'Nature'=>$nature,'Priorite'=>$priority,'Ouverture'=>$openCount,'Fermeture'=>$closeCount);
                        }
                    }
                }
            }
        }

        return $t_result;
    }

    /**
     * Récupère la donnée
     *
     * @param $year
     * @param $month
     * @param $project
     * @param $nature
     * @param $priority
     * @param $field
     * @return array
     */
    private function getData($year,$month,$project,$nature,$priority,$field){
        $entityManager  = $this->getDoctrine()->getManager();

        return $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getDateByMonthProject($year,$month,$project,$nature,$priority,$field);
    }
}