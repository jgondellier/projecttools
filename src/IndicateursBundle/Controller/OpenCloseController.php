<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Ob\HighchartsBundle\Highcharts\Highchart;

class OpenCloseController extends Controller
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
        $table['cols'][]    = array('filter'=>0,'name'=>'Ouvert','data'=>'Ouvert');
        $table['cols'][]    = array('filter'=>0,'name'=>'Fermé','data'=>'Ferme');
        $table_delai_HTML   = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_delai_JS     = $this->renderView('IndicateursBundle:Table:javscript.html.twig',array('table'=>$table));
        return $this->render('IndicateursBundle:OpenClose:index.html.twig',array(
            'activeMenu' => 'openclose',
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
                $t_open         = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getDateByMonthProject($year,$month,$project,$nature,$priority,'openDate');
                $t_closed       = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getDateByMonthProject($year,$month,$project,$nature,$priority,'closedDate');

                $t_result       = $this->formatForDataTable($t_open,$t_closed);
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
                $t_open         = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getDateByMonthProject($year,$month,$project,$nature,$priority,'openDate');
                $t_close        = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getDateByMonthProject($year,$month,$project,$nature,$priority,'closedDate');

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
                $ob->title->text('Nombre de Tickets Ouverts/Fermés pour '.$year);
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
            $t_openClose[$toolrender->getMonthName($open['mois'])][$list_project[$open['projet']]['name']][$open['nature']][$open['priority']]['Ouverture']=$open['somme'];
        }
        foreach ($t_closed as $closed){
            $t_openClose[$toolrender->getMonthName($closed['mois'])][$list_project[$closed['projet']]['name']][$open['nature']][$open['priority']]['Fermeture']=$closed['somme'];
        }

        //Init des valeurs null
        foreach($t_openClose as $month=>$listProjet){
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
                        $t_result['data'][] = array('Mois'=>$month,'Projet'=>$project,'Nature'=>$nature,'Priorite'=>$priority,'Ouverture'=>$openCount,'Fermeture'=>$closeCount);
                    }
                }
            }
        }
        return $t_result;
    }
}