<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Ob\HighchartsBundle\Highcharts\Highchart;

class ContractuelController extends Controller
{
    public function indexAction()
    {
        $jtrac_url   = $this->container->getParameter('jtrac_url');

        /*Paramètre global*/
        $table['cols'][]    = array('filter'=>1,'name'=>'Mois','data'=>'Mois');
        $table['cols'][]    = array('filter'=>1,'name'=>'Projet','data'=>'Projet');
        $table['cols'][]    = array('filter'=>1,'name'=>'Priorité','data'=>'Priorite');
        $table['cols'][]    = array('filter'=>0,'name'=>'Temps de réponse','data'=>'ResponseTime');
        $table['cols'][]    = array('filter'=>0,'name'=>'JtracID','data'=>'JtracID','href'=>$jtrac_url);

        /*Rendu du tableau Réouverture*/
        $table['ajax']['url']           = $this->generateUrl('indicateurs_contrat_reouverture_incident_table');
        $table['ajax']['datas'][]       = array('name'=>'year','value'=>'2016');
        $table['id']                    = 'reoinciTable';
        $table_incidentReouverture_HTML = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_incidentReouverture_JS   = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table));

        /*rendu du tableau traitement incident p1*/
        $table['ajax']['url']       = $this->generateUrl('indicateurs_contrat_traitement_liste', array('year'=>'2016','requestNature'=>'anomalie','priority'=>'p1'));
        $table['id']                = 'incidentTablePun';
        $table_incident_pun_HTML    = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_incident_pun_JS      = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table));

        /*rendu du tableau traitement incident p2*/
        $table['ajax']['url']       = $this->generateUrl('indicateurs_contrat_traitement_liste', array('year'=>'2016','requestNature'=>'anomalie','priority'=>'p2'));
        $table['id']                = 'incidentTablePdeux';
        $table_incident_pdeux_HTML  = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_incident_pdeux_JS    = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table));

        /*rendu du tableau traitement incident p3*/
        $table['ajax']['url']       = $this->generateUrl('indicateurs_contrat_traitement_liste', array('year'=>'2016','requestNature'=>'anomalie','priority'=>'p3'));
        $table['id']                = 'incidentTablePtrois';
        $table_incident_ptrois_HTML = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_incident_ptrois_JS   = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table));

        /*rendu du tableau traitement assisance*/
        $table['ajax']['url']           = $this->generateUrl('indicateurs_contrat_traitement_liste', array('year'=>'2016','requestNature'=>'support','priority'=>'p1'));
        $table['id']                    = 'assistanceTablePun';
        $table_assistance_pun_HTML      = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_assistance_pun_JS        = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table));

        /*rendu du tableau traitement assisance*/
        $table['ajax']['url']           = $this->generateUrl('indicateurs_contrat_traitement_liste', array('year'=>'2016','requestNature'=>'support','priority'=>'p2'));
        $table['id']                    = 'assistanceTablePdeux';
        $table_assistance_pdeux_HTML    = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_assistance_pdeux_JS      = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table));

        /*rendu du tableau traitement assisance*/
        $table['ajax']['url']           = $this->generateUrl('indicateurs_contrat_traitement_liste', array('year'=>'2016','requestNature'=>'support','priority'=>'p3'));
        $table['id']                    = 'assistanceTablePtrois';
        $table_assistance_ptrois_HTML   = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_assistance_ptrois_JS     = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table));

        /*Rendu du graph traitement assisance*/
        $graph['ajax']['url']           = $this->generateUrl('indicateurs_contrat_traitement_graph', array('year'=>'2016','requestNature'=>'support'));
        $graph['id']                    = 'chartContainerAssistance';
        $graph_assistance_HTML           = $this->renderView('IndicateursBundle:Highcharts:chart.html.twig',array('graph'=>$graph));
        $graph_assistance_JS             = $this->renderView('IndicateursBundle:Highcharts:chart_javascript.html.twig',array('graph'=>$graph));

        return $this->render('IndicateursBundle:Contractuel:index.html.twig',array(
            'activeMenu' => 'contractuel',
            'table_incidentReouverture_HTML'=>$table_incidentReouverture_HTML,
            'table_incidentReouverture_JS'=>$table_incidentReouverture_JS,
            'table_incident_pun_HTML'=>$table_incident_pun_HTML,
            'table_incident_pun_JS'=>$table_incident_pun_JS,
            'table_incident_pdeux_HTML'=>$table_incident_pdeux_HTML,
            'table_incident_pdeux_JS'=>$table_incident_pdeux_JS,
            'table_incident_ptrois_HTML'=>$table_incident_ptrois_HTML,
            'table_incident_ptrois_JS'=>$table_incident_ptrois_JS,
            'table_assistance_pun_HTML'=>$table_assistance_pun_HTML,
            'table_assistance_pun_JS'=>$table_assistance_pun_JS,
            'table_assistance_pdeux_HTML'=>$table_assistance_pdeux_HTML,
            'table_assistance_pdeux_JS'=>$table_assistance_pdeux_JS,
            'table_assistance_ptrois_HTML'=>$table_assistance_ptrois_HTML,
            'table_assistance_ptrois_JS'=>$table_assistance_ptrois_JS,
            'graph_assistance_HTML'=>$graph_assistance_HTML,
            'graph_assistance_JS'=>$graph_assistance_JS,
        ));
    }

    //Nombre de réouverture sur incident
    public function IncidentReouvertureCountAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();
                $year           = $request->get('year');
                $response       = new JsonResponse();

                $count          = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getIncidentReouvertureCount($year);
                $template       = $this->renderView('IndicateursBundle:Contractuel:ReouvertureIncidentCount.render.html.twig',array("count"=>$count["somme"]));

                $response->setContent(json_encode($template));
                return $response;
            }
        }
    }

    /**
     * Liste de réouverture sur incident
     *
     * @param Request $request
     * @return null|JsonResponse
     */
    public function IncidentReouvertureTableAction(Request $request)
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

                $t_incidentreouvert = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getIncidentReouverture($year);

                $t_result       = $this->formatForDataTable($t_incidentreouvert);

                $response->setContent(json_encode($t_result));
                return $response;
            }
        }else{
            throw new AccessDeniedException('Access denied');
        }
        return NULL;
    }

    /**
     * Permet de formater la donnée afin de l'afficher dans le tableau.
     *
     * @param $t_data
     * @return array
     */
    private function formatForDataTable($t_data){
        $list_project   = $this->container->getParameter('list_project');
        $toolrender     = $this->get('indicateurs.rendertools');
        $listData       = array();

        if(count($t_data)>0){
            //Formalisation de la donnée
            foreach ($t_data as $data){
                $listData['data'][] = array(
                    'Mois'=>$toolrender->getMonthName($data['mois']),
                    'Projet'=>$list_project[$data['projectId']]['name'],
                    'Priorite'=>$data['priority'],
                    'ResponseTime'=>$data['ResponseTime'],
                    'JtracID'=>$data['jtracId']
                );
            }
        }else{
            $listData['draw'] = 1;
            $listData['recordsTotal'] = 0;
            $listData['recordsFiltered'] = 0;
            $listData['data'] = array();
        }

        return $listData;
    }
    public function GraphAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $requestNature  = $request->get('requestNature');
                $year           = $request->get('year');
                $priority       = $request->get('priority');
                $response       = new JsonResponse();
                $entityManager  = $this->getDoctrine()->getManager();
                $toolrender     = $this->get('indicateurs.rendertools');
                $t_liste        = array();
                $data           = array();
                $categories     = array();

                //Les delai contractuel
                $contrat        = $this->container->getParameter('contrat');
                $delai_priorite = $contrat['delai_priorite'];

                //Les mois
                for ($mois = 1; $mois <= 12; $mois++) {
                    //Initialisation du graphe sur 12 mois
                    $data['0']['data'][$mois-1] = 0;
                    $data['0']['type'] = 'line';
                    $data['0']['name'] = 'P1';
                    $data['1']['data'][$mois-1] = 0;
                    $data['1']['type'] = 'line';
                    $data['1']['name'] = 'P2';
                    $data['2']['data'][$mois-1] = 0;
                    $data['2']['type'] = 'line';
                    $data['2']['name'] = 'P3';
                    $categories[] = $toolrender->getMonthName($mois);
                }

                //Liste des tickets dépassant le délai
                if($priority && $priority!=-1){
                    $t_liste        = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->delai('ResponseTime',$requestNature,array('time'=>$delai_priorite[$requestNature][$priority]*60,'operator'=>'>'),array('year'=>$year,'priority'=>$priority),'liste');
                }else{
                    $t_priority = array('p1','p2','p3');
                    foreach($t_priority as $priority ){
                        $t_liste    = array_merge ($t_liste,$entityManager->getRepository("IndicateursBundle:Indic_TRSB")->delai('ResponseTime',$requestNature,array('time'=>$delai_priorite[$requestNature][$priority]*60,'operator'=>'>'),array('year'=>$year,'priority'=>$priority),'liste'));
                    }
                }

                //Reorganiation de la liste
                foreach($t_liste as $liste){
                    switch ($liste['priority']){
                        case 'p1':
                            $index=0;
                            break;
                        case 'p2':
                            $index=1;
                            break;
                        case 'p3':
                            $index=2;
                            break;
                    }
                    if(array_key_exists($index,$data) && array_key_exists('mois',$data[$index]['data'])){
                        $data[$index]['data'][$liste['mois']-1] += 1;
                    }else{
                        $data[$index]['data'][$liste['mois']-1] = 1;
                    }
                }

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

            }
        }
    }

    /**
     * Delai de traitement sur incident P1 suivant le contrat
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function IncidentDelaiTraitementContratAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();
                $year           = $request->get('year');
                $response       = new JsonResponse();

                $count          = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->delaiTraitementIncidentContractuel($year);

                $template       = $this->renderView('IndicateursBundle:Contractuel:DelaiTraitementncidentContrat.html.twig',array("nb"=>$count['nb'],"total"=>$count['total'],"pourcent"=>round($count['nb']/$count['total']*100)));

                $response->setContent(json_encode($template));
                return $response;
            }
        }
    }

    /**
     * Liste des tickets en fonction du type et de la priorité.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listeDelaiContratAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();
                $requestNature  = $request->get('requestNature');
                $year           = $request->get('year');
                $priority       = $request->get('priority');
                $response       = new JsonResponse();

                //Les delai contractuel
                $contrat        = $this->container->getParameter('contrat');
                $delai_priorite = $contrat['delai_priorite'];

                //Liste des tickets dépassant le délai
                $t_liste        = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->delai('ResponseTime',$requestNature,array('time'=>$delai_priorite[$requestNature][$priority]*60,'operator'=>'>'),array('year'=>$year,'priority'=>$priority),'liste');

                $t_result       = $this->formatForDataTable($t_liste);

                $response->setContent(json_encode($t_result));
                return $response;
            }
        }
    }

}
