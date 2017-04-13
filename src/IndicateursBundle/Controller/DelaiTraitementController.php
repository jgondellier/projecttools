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
        $toolrender     = $this->get('indicateurs.rendertools');
        $jtrac_url      = $this->container->getParameter('jtrac_url');

        /*Rendu du graph*/
        $graph['ajax']['url']   = $this->generateUrl('indicateurs_delai_traitement_graph');
        $graph['id']            = 'chartContainer';
        $graph_HTML             = $this->renderView('IndicateursBundle:Highcharts:chart.html.twig',array('graph'=>$graph));
        $graph_JS               = $this->renderView('IndicateursBundle:Highcharts:chart_javascript.html.twig',array('graph'=>$graph));

        /*Rendu du tableau */
        $table['ajax']['url']       = $this->generateUrl('indicateurs_delai_traitement_table');
        $table['id']                = 'delaiTable';
        $table                      = $toolrender->initColTable($table);
        $table['cols'][]            = array('filter'=>0,'name'=>'JtracId','data'=>'JtracId','href'=>$jtrac_url);
        $table['cols'][]            = array('filter'=>0,'name'=>'Délai','data'=>'Delai','convert'=>'TRUE');
        $table_HTML                 = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_JS                   = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table,'graph'=>$graph));

        return $this->render('IndicateursBundle:DelaiTraitement:index.html.twig',array(
            'activeMenu' => 'delaiTraitement',
            'table_HTML' => $table_HTML,
            'table_JS' => $table_JS,
            'graph_HTML' => $graph_HTML,
            'graph_JS' => $graph_JS,
        ));
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

                /*Tickets avec délai par anne, mois par projet...*/
                $t_delai        = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->delaiTraitement($year,$month,$project,$nature,$priority);

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

                //On cherche l'id du projet
                foreach($list_project as $idPro => $pro){
                    if($pro["name"] == $project){
                        $project = $idPro;
                    }
                }

                $t_delai        = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->delaiTraitement($year,$month,$project,$nature,$priority);

                $data           = $this->formatDataForGraph($t_delai);

                /*On regroupe les résultats en fonction des delais*/

                $ob = new Highchart();
                $ob->chart->renderTo('chartContainer');
                $ob->title->text('Nombre de ticket par delai de traitement moyen');

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
    private function formatDataForGraph($t_delai){
        $t_interval = $this->setIntervalValue();

        $t_NbInterval = array();
        $t_data = array();

        foreach($t_delai as $delai){
            $t_NbInterval = $this->getIntervalDelai($delai,$t_interval,$t_NbInterval);
        }

        foreach($t_NbInterval as $index => $nbInterval){
            $t_data[] = array($index,$nbInterval);
        }

        return $t_data;
    }
    private function getIntervalDelai($data,$t_interval,$t_result){
        //on convertit la donnée en heure
        $delai = $data['delai']/60;
        foreach($t_interval as $i =>$interval){
            if($delai > $interval['min'] AND $delai < $interval['max']){
                if(array_key_exists('Entre '.$interval['min'].' et '.$interval['max'].' heures',$t_result)){
                    $t_result['Entre '.$interval['min'].' et '.$interval['max'].' heures'] += 1;
                }else{
                    $t_result['Entre '.$interval['min'].' et '.$interval['max'].' heures'] = 1;
                }
                return $t_result;
            }
        }
        return $t_result;
    }
    private function setIntervalValue(){
        $contrat        = $this->container->getParameter('contrat');
        $delai_interval = $contrat['delai_interval'];

        $t_interval     = array();
        $previous       = null;
        // trie du tableau des valeurs le plus petites au plus grande
        sort($delai_interval);

        //Premiere valeur
        $firstValue     = array_shift($delai_interval);
        $t_interval[]   = array('min'=>0,'max'=>$firstValue);
        $previous       = $firstValue;

        foreach($delai_interval as $interval){
            $t_interval[]   = array('min'=>$previous,'max'=>$interval);
            $previous       = $interval;
        }
        //Derniere valeur
        $t_interval[] = array('min'=>array_pop($delai_interval),'max'=>99999999);

        return $t_interval;
    }

}
