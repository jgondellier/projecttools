<?php

namespace IndicateursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IndicateursBundle\Repository\Indic_itemsRepository;

class DefaultController extends Controller
{
    public function indexAction()
    {

        /*Délai de traitement par mois
        Anomalie
        Support
        Evolutions
        */


        $entityManager = $this->getDoctrine()->getManager();
        $t_jtracs = $entityManager->getRepository('IndicateursBundle:Indic_items')->getItemsArray();

//var_dump($t_jtracs);exit;

        var_dump($this->DelaiTraitementIncident($t_jtracs));
        exit;
        return $this->render('IndicateursBundle:Default:index.html.twig');
    }

    private function DelaiTraitementIncident($t_jtracs)
    {
        $t_result = array();
        $t_status = $this->getParameter('status');
        $t_delaiPriorite = $this->getParameter('delai_priorite');
        foreach($t_jtracs as $jtrac){
            //On ne prend que les anomalies
            if($jtrac['requestNature'] == 1){
                //On cherche dans les history le status corrected
                foreach($jtrac['history'] as $history){
                    if ($history['status'] == $t_status['corrected'][$jtrac['projectId']]){
                        //On ne prend que les Tickets ou TRSB est dans le workflow sans compter la fermeture


                        //Ticket de respectant pas les delais
                        if($delai->format('%H')>$t_delaiPriorite['anomalie'][$priority]){
                            $t_result['bad'][date_format($jtrac['createdDate'],'m')][$jtrac['projectId']][$priority][$jtrac['jtracId']] = $delai->format('%H');
                        }
                        /*var_dump(date_format($jtrac['createdDate'],'m'));
                        var_dump($delai->format('%H'));
                        var_dump($t_delaiPriorite['anomalie'][$priority]);
                        var_dump($priority);
                        exit;*/
                        break;
                    }
                }

            }
        }
        return $t_result;
    }

    /**
     * Permet de récupérer le temps passé entre deux dates en prenant en compte
     * Les jours fériés et les heures ouvrées
     *
     * @param $date1
     *  Date de début
     * @param $date2
     *  Date de fin
     * @param string $step
     *  m pour comparé les dates minutes par minute
     * @param string $outputFormat
     *  h pour sortir le temps passé en heure
     * @return float|int
     */
    private function getTimeElapsed($date1,$date2,$step='m',$outputFormat='h'){
        //On avance de minute en minute
        if($step == 'm') {
            $dateInterval = new \DateInterval('PT1M');
        }elseif($step == 'h'){
            $dateInterval = new \DateInterval('PT1H');
        }else{
            $dateInterval = new \DateInterval('PT1M');
        }

        //On créé une iteration de la différence
        $daterange = new \DatePeriod($date1, $dateInterval ,$date2);
        $delai = 0;
        foreach($daterange as $range){
            //Weekend
            if(!$this->isWeekend($range->format("y-m-d"))){
                //Heure ouvrée 9h00 18h00
                if($range->format("H") > 8 && $range->format("H") <= 17){
                    $delai++;
                }
            }
        }
        //On convertit le résultat en heure
        if($step == 'm' && $outputFormat == 'h'){
            $delai = $delai/60;
        }
        return $delai;
    }

    /**
     * Fonction permettant de savoir si une date est un weekend ou pas.
     *
     * @param $date
     * @return bool
     */
    private function isWeekend($date){
        if(date('w',strtotime($date)) == 0 || date('w',strtotime($date)) == 6){
            return true;
        }
        return false;
    }

}
