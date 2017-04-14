<?php

namespace IndicateursBundle\Model;


/**
 * Class ToolRender
 * @package IndicateursBundle\Model
 */
class ToolRender{


    /**
     * Format les données pour les exploiter dans le graph
     *
     * @param $t_data
     * @param $monthInterval
     * @return array
     */
    public function formatData($t_data,$monthInterval){
        $t_result   = array();
        foreach($t_data as $data) {
            if (isset($t_result[$data["annee"].sprintf("%02d", $data["mois"])])) {
                $t_result[$data["annee"].sprintf("%02d", $data["mois"])] += $data['somme'];
            } else {
                $t_result[$data["annee"].sprintf("%02d", $data["mois"])] = $data['somme'];
            }
        }
        $data       = array();

        foreach($monthInterval as $idMonth =>$nameMonth){
            if(isset($t_result[$idMonth])){
                $data[] = array($nameMonth,floatval($t_result[$idMonth]));
            }else{
                $data[] = array($nameMonth,floatval(0));
            }
        }

        return $data;
    }

    /**
     * Récupère la liste des dates utilisé entre deux liste.
     * Evite la desynchro des index de date entre deux array.
     *
     * @param array $t_data1
     * @param array $t_data2
     * @return array
     */
    public function getMonthInterval($t_data1,$t_data2 = Null){
        $listYear = array();
        foreach ($t_data1 as $data){
            $listYear[$data["annee"].sprintf("%02d", $data["mois"])]=$this->getMonthName($data["mois"])." ".$data["annee"];
        }
        if($t_data2){
            foreach ($t_data2 as $data){
                $listYear[$data["annee"].sprintf("%02d", $data["mois"])]=$this->getMonthName($data["mois"])." ".$data["annee"];
            }
        }
        ksort($listYear);

        return $listYear;
    }

    /**
     * Retourne la liste des mois mais avec un index remis à zeros.
     *
     * @param $monthInterval
     * @return array
     */
    public function killindexMonth($monthInterval){
        $listMonth = array();
        foreach($monthInterval as $interval){
            $listMonth[] = $interval;
        }
        return $listMonth;
    }

    /**
     * Retourne le nom du mois
     *
     * @param int $month
     * @return null|string
     */
    public function getMonthName($month)
    {
        switch ($month) {
            case '1':
                return 'Janvier';
            case '2':
                return 'Février';
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
                return 'Août';
            case '9':
                return 'Septembre';
            case '10':
                return 'Octobre';
            case '11':
                return 'Novembre';
            case '12':
                return 'Décembre';
        }

        return null;
    }

    /**
     * Permet d'initier le tableau avec les premières colonnes habituelles
     *
     * @param array $table
     * @return array $table
     */
    public function initColTable($table){
        $table['cols'][]    = array('filter'=>1,'name'=>'Année','data'=>'Annee','id'=>'year');
        $table['cols'][]    = array('filter'=>1,'name'=>'Mois','data'=>'Mois','id'=>'month');
        $table['cols'][]    = array('filter'=>1,'name'=>'Projet','data'=>'Projet','id'=>'project');
        $table['cols'][]    = array('filter'=>1,'name'=>'Nature','data'=>'Nature','id'=>'nature');
        $table['cols'][]    = array('filter'=>1,'name'=>'Priorité','data'=>'Priorite','id'=>'priority');
        return $table;
    }

    /**
     * Retourne les valeurs formatées correctement pour afficher un camembert
     *
     * @param $t_delai
     * @param $contrat
     * @return array
     */
    public function formatDataForPieGraph($t_delai,$contrat){
        $t_interval = $this->setIntervalValue($contrat);

        $t_NbInterval = array();
        $t_data = array();

        foreach($t_delai as $delai){
            $t_NbInterval = $this->getIntervalDelai($delai,$t_interval,$t_NbInterval);
        }

        foreach($t_NbInterval as $index => $nbInterval){
            $t_data[] = array($index,$nbInterval);
        }

        return $t_data;
    }

    /**
     * Permet d'avoir le nombre d'occurence de delai entre une période fournie.
     *
     * @param $data
     * @param $t_interval
     * @param $t_result
     * @return mixed
     */
    private function getIntervalDelai($data,$t_interval,$t_result){
        //on convertit la donnée en heure
        $delai = $data['delai']/60;
        foreach($t_interval as $i =>$interval) {
            if ($interval['max'] == 'max') {
                if ($delai >= $interval['min']) {
                    $label = 'Plus de '.$interval['min'].' heures';
                    return $this->setIntervalResultValue($label,$t_result);
                }
            }elseif($interval['min'] == 'min'){
                if ($delai <= $interval['max']) {
                    $label = 'Moins de '.$interval['max'].' heures';
                    return $this->setIntervalResultValue($label,$t_result);
                }
            }else{
                if($delai > $interval['min'] AND $delai < $interval['max']) {
                    $label = 'Entre '.$interval['min'].' et '.$interval['max'].' heures';
                    return $this->setIntervalResultValue($label,$t_result);
                }
            }
        }
        return $t_result;
    }

    /**
     * Renseigne un array pour les résultats des intervelles de delai.
     *
     * @param $label
     * @param $t_result
     * @return mixed
     */
    private function setIntervalResultValue($label,$t_result){
        if (array_key_exists($label, $t_result)) {
            $t_result[$label] += 1;
        } else {
            $t_result[$label] = 1;
        }
        return $t_result;
    }

    /**
     * Retourne un tableau avec des valeur founis pour faire un intervale de temps.
     *
     * @param $contrat
     * @return array
     */
    private function setIntervalValue($contrat){
        $delai_interval = $contrat['delai_interval'];

        $t_interval     = array();
        $previous       = null;
        // trie du tableau des valeurs le plus petites au plus grande
        sort($delai_interval);

        //Premiere valeur
        $firstValue     = array_shift($delai_interval);
        $t_interval[]   = array('min'=>0,'max'=>$firstValue);
        $previous       = $firstValue;

        foreach($delai_interval as $interval){
            $t_interval[]   = array('min'=>$previous,'max'=>$interval);
            $previous       = $interval;
        }
        //Derniere valeur
        $t_interval[] = array('min'=>array_pop($delai_interval),'max'=>'max');

        return $t_interval;
    }
}