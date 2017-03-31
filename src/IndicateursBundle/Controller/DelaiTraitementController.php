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

        /*Rendu du tableau */
        $table['ajax']['url']       = 'traitement/table';
        $table['ajax']['datas'][]   = array('name'=>'year','value'=>'2016');
        $table['id']                = 'delaiTable';
        $table                      = $toolrender->initColTable($table);
        $table['cols'][]            = array('filter'=>0,'name'=>'JtracId','data'=>'JtracId','href'=>$jtrac_url);
        $table['cols'][]            = array('filter'=>0,'name'=>'Délai','data'=>'Delai');
        $table_delai_HTML           = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_delai_JS             = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table));

        return $this->render('IndicateursBundle:DelaiTraitement:index.html.twig',array(
            'activeMenu' => 'delaiTraitement',
            'table_delai_HTML'=>$table_delai_HTML,
            'table_delai_JS'=>$table_delai_JS,
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

                /*Tickets ouverts fermés par mois par projet*/
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
                /*$requestNature  = $request->get('requestNature');
                $year           = $request->get('year');
                $priority       = $request->get('priority');
                $entityManager  = $this->getDoctrine()->getManager();
                $toolrender     = $this->get('indicateurs.rendertools');
                $t_liste        = array();
                $data           = array();
                $categories     = array();

                //Les delai contractuel
                $contrat        = $this->container->getParameter('contrat');
                $delai_priorite = $contrat['delai_priorite'];


                $ob = new Highchart();
                $ob->chart->renderTo('chartContainerAssistance');
                $ob->title->text('Delai assistance pour '.$year);

                $ob->yAxis->title(array('text' => "Nombre de tickets"));

                $ob->xAxis->title(array('text' => "Mois"));

                $ob->xAxis->categories($categories);

                $ob->series($data);

                return $this->render('@Indicateurs/Highcharts/rendu.html.twig', array(
                    'ob' => $ob
                ));
                */
            }
        }
    }
}
