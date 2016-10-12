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

        /*Rendu du tableau */
        $table['url']       = 'contrat/reoinci/count/2016';
        $table['id']        = 'reoinciTable';
        $table['cols'][]    = array('filter'=>1,'name'=>'Mois','data'=>'Mois');
        $table['cols'][]    = array('filter'=>1,'name'=>'Projet','data'=>'Projet');
        $table['cols'][]    = array('filter'=>0,'name'=>'Nombre','data'=>'Nombre');
        $table_incidentReouverture_HTML = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_incidentReouverture_JS = $this->renderView('IndicateursBundle:Table:javscript.html.twig',array('table'=>$table));

        return $this->render('IndicateursBundle:Contractuel:index.html.twig',array(
            'activeMenu' => 'contractuel',
            'table_incidentReouverture_HTML'=>$table_incidentReouverture_HTML,
            'table_incidentReouverture_JS'=>$table_incidentReouverture_JS,
        ));
    }

    //incident = ano P1

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
    public function IncidentReouvertureAction(Request $request)
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

                if(count($t_incidentreouvert)>0){
                    $t_result       = $this->formatForDataTable($t_incidentreouvert);
                }else{
                    $t_result = null;
                }
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
            $listData['data'][] = array('Mois'=>$toolrender->getMonthName($data['mois']),'Projet'=>$list_project[$data['projet']]['name'],'Nombre'=>$data['somme']);
        }

        return $listData;
    }

    //Delai de traitement sur incident
    public function IncidentDelaiTraitement()
    {

    }

    //Nombre de réouverture sur incident
}
