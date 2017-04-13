<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Ob\HighchartsBundle\Highcharts\Highchart;

class DelaiController extends Controller
{
    public function indexAction()
    {
        $toolrender     = $this->get('indicateurs.rendertools');
        $jtrac_url      = $this->container->getParameter('jtrac_url');

        /*Rendu du graph corrected*/
        $graph_corrected['ajax']['url']   = $this->generateUrl('indicateurs_delai_traitement_graph');
        $graph_corrected['id']            = 'correctedChartContainer';
        $graph_corrected_HTML             = $this->renderView('IndicateursBundle:Highcharts:chart.html.twig',array('graph'=>$graph_corrected));
        $graph_corrected_JS               = $this->renderView('IndicateursBundle:Highcharts:chart_javascript.html.twig',array('graph'=>$graph_corrected));

        /*Rendu du tableau corrected*/
        $table_corrected['ajax']['url']       = $this->generateUrl('indicateurs_delai_traitement_table');
        $table_corrected['id']                = 'correctedTable';
        $table_corrected                      = $toolrender->initColTable($table_corrected);
        $table_corrected['cols'][]            = array('filter'=>0,'name'=>'JtracId','data'=>'JtracId','href'=>$jtrac_url);
        $table_corrected['cols'][]            = array('filter'=>0,'name'=>'Délai','data'=>'Delai','convert'=>'TRUE');
        $table_corrected_HTML                 = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table_corrected));
        $table_corrected_JS                   = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table_corrected,'graph'=>$graph_corrected));

        /*Rendu du graph answer*/
        $graph_answer['ajax']['url']   = $this->generateUrl('indicateurs_delai_reponse_graph');
        $graph_answer['id']            = 'answerChartContainer';
        $graph_answer_HTML             = $this->renderView('IndicateursBundle:Highcharts:chart.html.twig',array('graph'=>$graph_answer));
        $graph_answer_JS               = $this->renderView('IndicateursBundle:Highcharts:chart_javascript.html.twig',array('graph'=>$graph_answer));

        /*Rendu du tableau answer*/
        $table_answer['ajax']['url']       = $this->generateUrl('indicateurs_delai_reponse_table');
        $table_answer['id']                = 'answerTable';
        $table_answer                      = $toolrender->initColTable($table_answer);
        $table_answer['cols'][]            = array('filter'=>0,'name'=>'JtracId','data'=>'JtracId','href'=>$jtrac_url);
        $table_answer['cols'][]            = array('filter'=>0,'name'=>'Délai','data'=>'Delai','convert'=>'TRUE');
        $table_answer_HTML                 = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table_answer));
        $table_answer_JS                   = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table_answer,'graph'=>$graph_answer));

        return $this->render('IndicateursBundle:DelaiTraitement:index.html.twig',array(
            'activeMenu' => 'delai',
            'table_corrected_HTML' => $table_corrected_HTML,
            'table_corrected_JS' => $table_corrected_JS,
            'graph_corrected_HTML' => $graph_corrected_HTML,
            'graph_corrected_JS' => $graph_corrected_JS,
            'table_answer_HTML' => $table_answer_HTML,
            'table_answer_JS' => $table_answer_JS,
            'graph_answer_HTML' => $graph_answer_HTML,
            'graph_answer_JS' => $graph_answer_JS,
        ));
    }
}
