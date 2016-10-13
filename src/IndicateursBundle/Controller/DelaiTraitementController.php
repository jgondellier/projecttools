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
        /*Rendu du tableau */
        $table['ajax']['url']           = 'traitement/table';
        $table['ajax']['datas'][]       = array('name'=>'year','value'=>'2016');
        $table['id']        = 'delaiTable';
        $table['cols'][]    = array('filter'=>1,'name'=>'Mois','data'=>'Mois');
        $table['cols'][]    = array('filter'=>1,'name'=>'Projet','data'=>'Projet');
        $table['cols'][]    = array('filter'=>1,'name'=>'Nature','data'=>'Nature');
        $table['cols'][]    = array('filter'=>1,'name'=>'Priorité','data'=>'Priorite');
        $table['cols'][]    = array('filter'=>0,'name'=>'JtracId','data'=>'JtracId');
        $table['cols'][]    = array('filter'=>0,'name'=>'Délai','data'=>'Delai');
        $table_delai_HTML = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_delai_JS = $this->renderView('IndicateursBundle:Table:javscript.html.twig',array('table'=>$table));

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

    public function GraphAction(Request $request)
    {

    }

    private function formatForDataTable($t_data){
        $list_project   = $this->container->getParameter('list_project');
        $toolrender     = $this->get('indicateurs.rendertools');
        $listData       = array();

        //Formalisation de la donnée
        foreach ($t_data as $data){
            $listData['data'][] = array('Mois'=>$toolrender->getMonthName($data['mois']),'Projet'=>$list_project[$data['projet']]['name'],'Nature'=>$data['nature'],'Priorite'=>$data['priority'],'JtracId'=>$data['jtracid'],'Delai'=>$data['delai']);
        }

        return $listData;
    }
}
