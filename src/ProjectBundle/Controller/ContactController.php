<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ContactController extends Controller
{
    public function indexAction()
    {
        /*Rendu du tableau */
        $table['ajax']['url']           = $this->generateUrl('project_contact_table');
        $table['id']                    = 'contactTable';
        $table['cols'][]                = array('filter'=>0,'name'=>'Nom','data'=>'nom');
        $table['cols'][]                = array('filter'=>0,'name'=>'Prenom','data'=>'prenom');
        $table['cols'][]                = array('filter'=>0,'name'=>'Mail','data'=>'mail');
        $table['cols'][]                = array('filter'=>0,'name'=>'Projet','data'=>'projet');
        $table['cols'][]                = array('filter'=>0,'name'=>'description','data'=>'description');
        $table_HTML                     = $this->renderView('IndicateursBundle:Table:table.html.twig',array('table'=>$table));
        $table_JS                       = $this->renderView('IndicateursBundle:Table:table_javascript.html.twig',array('table'=>$table));

        return $this->render('ProjectBundle:Contact:Contacts.html.twig',array(
            'activeMenu' => 'contact',
            'table_HTML'=>$table_HTML,
            'table_JS'=>$table_JS,
        ));
    }
    /**
     * Retourne les contacts pour les afficher dans un tableau
     *
     * @param Request $request
     * @return null|JsonResponse
     */
    public function TableAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();

                $nom            = $request->get('nom');
                $prenom         = $request->get('prenom');
                $mail           = $request->get('mail');
                $projet         = $request->get('projet');
                $description    = $request->get('description');

                $response       = new JsonResponse();

                /*Recuperation des contacts en base*/
                $t_contact      = ;

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
}
