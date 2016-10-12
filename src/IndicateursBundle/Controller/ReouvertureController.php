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
        /*Rendu du tableau */
        $table['url']       = 'openclose/table/2016';
        $table['id']        = 'opencloseTable';
        $table['cols'][]    = array('filter'=>1,'name'=>'Mois','data'=>'Mois');
        $table['cols'][]    = array('filter'=>1,'name'=>'Projet','data'=>'Projet');
        $table['cols'][]    = array('filter'=>1,'name'=>'Nature','data'=>'Nature');
        $table['cols'][]    = array('filter'=>1,'name'=>'Priorité','data'=>'Priorite');
        $table['cols'][]    = array('filter'=>0,'name'=>'Réouverture','data'=>'Reouverture');
        $table_delai_HTML   = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_delai_JS     = $this->renderView('IndicateursBundle:Table:javscript.html.twig',array('table'=>$table));

        return $this->render('IndicateursBundle:Reouverture:index.html.twig',array(
            'activeMenu' => 'reouverture',
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
            $listData['data'][] = array('Mois'=>$toolrender->getMonthName($data['mois']),'Projet'=>$list_project[$data['projet']]['name'],'Nature'=>$data['nature'],'Priorite'=>$data['priority'],'Reouverture'=>$data['somme']);
        }

        return $listData;
    }
}
