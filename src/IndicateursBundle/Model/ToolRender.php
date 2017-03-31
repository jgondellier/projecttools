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
}