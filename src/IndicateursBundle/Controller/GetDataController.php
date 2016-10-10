<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use IndicateursBundle\Repository\Indic_itemsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GetDataController extends Controller
{
    public function OpenCloseAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            if ($request->getMethod() === 'GET') {
                $entityManager  = $this->getDoctrine()->getManager();
                $list_project   = $this->container->getParameter('list_project');
                $year           = $request->get('year');
                $type           = $request->get('type');
                $project        = $request->get('project');
                $response       = new JsonResponse();

                /*Tickets ouverts fermés par moi par projet*/
                $t_open         = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getDateByMonthProject($year,$project,'openDate');
                $t_closed       = $entityManager->getRepository("IndicateursBundle:Indic_TRSB")->getDateByMonthProject($year,$project,'closedDate');

                switch($type){
                    case 'table':
                        $t_result       = $this->formatForDataTable($t_open,$t_closed);
                        break;
                    case 'graph':
                        if($project){
                            $t_result       = $this->sommeDataByMonth($t_open,$t_closed,$project);
                            $t_result       = $this->convertToHightcharts($t_result);
                        }else{
                            $t_result       = $this->sommeDataByMonth($t_open,$t_closed);
                            $t_result       = $this->convertToHightcharts($t_result);
                        }
                        break;
                    default:
                        $t_result       = $this->formatForDataTable($t_open,$t_closed);
                        break;
                }
                $response->setContent(json_encode($t_result));
                return $response;
            }
        }else{
            throw new AccessDeniedException('Access denied');
        }
        return NULL;
    }

    private function convertToHightcharts($t_value){
        $t_result       = array();
        foreach($t_value["Ouverture"] as $ouverture){
            if(isset($t_result['categorie'])){
                $t_result['categorie'] .= ','.$ouverture['Label'];
            }else{
                $t_result['categorie'] = $ouverture['Label'];
            }
            $t_result['Ouverture']['series']['name'] = 'Ouverture';
            if(isset($t_result['Ouverture']['series']['data'])){
                $t_result['Ouverture']['series']['data'] .= ','.$ouverture['y'];
            }else{
                $t_result['Ouverture']['series']['data'] = $ouverture['y'];
            }
        }
        foreach($t_value["Fermeture"] as $fermeture){
            $t_result['Fermeture']['series']['name'] = 'Fermeture';
            if(isset($t_result['Fermeture']['series']['data'])){
                $t_result['Fermeture']['series']['data'] .= ','.$fermeture['y'];
            }else{
                $t_result['Fermeture']['series']['data'] = $fermeture['y'];
            }
        }
        return $t_result;
    }

    private function sommeDataByMonth($t_open,$t_closed,$project = Null){
        $t_openClose    = array();
        $t_result       = array();
        //Formalisation de la donnée
        foreach ($t_open as $open){
            if($project && $open['projet'] == $project){
                $t_openClose[$this->getMonthName($open['mois'])]['Ouverture'] = $open['somme'];
            }else{
                $t_openClose[$this->getMonthName($open['mois'])]['Ouverture'] = $open['somme'];
            }
        }
        foreach ($t_closed as $closed){
            if($project && $closed['projet'] == $project){
                $t_openClose[$this->getMonthName($closed['mois'])]['Fermeture'] = $closed['somme'];
            }else{
                $t_openClose[$this->getMonthName($closed['mois'])]['Fermeture'] = $closed['somme'];
            }
        }

        //Init des valeurs null
        foreach($t_openClose as $month=>$etat){
            $openCount = 0;
            $closeCount = 0;
            if(isset($etat['Ouverture'])){
                $openCount = $etat['Ouverture'];
            }
            if(isset($etat['Fermeture'])){
                $closeCount = $etat['Fermeture'];
            }
            $t_result['Ouverture'][]=array('Label'=>$month,'y'=>intval($openCount));
            $t_result['Fermeture'][]=array('Label'=>$month,'y'=>intval($closeCount));
        }

        return $t_result;
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

        //Formalisation de la donnée
        foreach ($t_open as $open){
            $t_openClose[$this->getMonthName($open['mois'])][$list_project[$open['projet']]['name']]['Ouverture']=$open['somme'];
        }
        foreach ($t_closed as $closed){
            $t_openClose[$this->getMonthName($closed['mois'])][$list_project[$closed['projet']]['name']]['Fermeture']=$closed['somme'];
        }

        //Init des valeurs null
        foreach($t_openClose as $month=>$listProjet){
            foreach($listProjet as $project=>$openClose){
                $openCount = 0;
                $closeCount = 0;
                if(isset($openClose['Ouverture'])){
                    $openCount = $openClose['Ouverture'];
                }
                if(isset($openClose['Fermeture'])){
                    $closeCount = $openClose['Fermeture'];
                }
                $t_result['data'][] = array('Mois'=>$month,'Projet'=>$project,'Ouverture'=>$openCount,'Fermeture'=>$closeCount);
            }
        }
        return $t_result;
    }

    /**
     * Retourne le nom du mois
     *
     * @param int $month
     * @return null|string
     */
    private function getMonthName($month){
        switch ($month){
            case '1':
                return 'Janvier';
            case '2':
                return 'Fevrier';
            case '3':
                return 'Mars';
            case '4':
                return 'Avril';
            case '5':
                return 'Mai';
            case '6':
                return 'Juin';
            case '7':
                return 'Juillet';
            case '8':
                return 'Aout';
            case '9':
                return 'Septembre';
            case '10':
                return 'Octobre';
            case '11':
                return 'Novembre';
            case '12':
                return 'Décembre';
        }
        return Null;
    }
}
