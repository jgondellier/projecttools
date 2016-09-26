<?php

namespace IndicateursBundle\Model;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Class ImportJtrac
 * @package IndicateursBundle\Model
 */
class ImportJtrac
{
    protected $param1;

    public function __construct($list_project) {
        $this->list_project = $list_project;
    }

    public function getValue($itemsFile,$historyFile){
        $t_item               = array();
        $t_history            = array();
        $separateur           = '|';

        //Liste des colonne à récupérer
        $id                 = 0;
        $item_id            = 3;
        $space_id           = 4;
        $sequence_num       = 5;
        $comment            = 6;
        $item_time_stamp    = 6;
        $history_time_stamp = 7;
        $logged_by          = 8;
        $assigned_to        = 9;
        $summary            = 10;
        $detail             = 11;
        $status             = 12;
        $severity           = 13;
        $priority           = 14;
        $nature_request     = 18;
        $cadre              = 22;

        //Ouverture du Fichier des items
        if (file_exists($itemsFile)) {
            $fp=fopen($itemsFile,"r");
        }else{
            echo 'Fichier introuvable !';
            exit();
        }
        //Parse du Fichier des items
        while($line=fgets ($fp)) {
            $t_ligne = explode($separateur, htmlspecialchars_decode(html_entity_decode($line)));
            //filtre sur la liste des projets
            if(array_key_exists ($t_ligne[$space_id],$this->list_project)){
                //filtre sur la une date minimum (janvier 2016)
                if ($t_ligne[$item_time_stamp] > 1451602800){
                    $t_item[$t_ligne[$id]] = array('id'=>$t_ligne[$id],'project_id'=>$t_ligne[$space_id],'jtrac_id'=>$t_ligne[$sequence_num],'created_date'=>$t_ligne[$item_time_stamp],'created_by'=>$t_ligne[$logged_by],'title'=>$t_ligne[$summary],'description'=>$t_ligne[$detail],'status'=>$t_ligne[$status],'severity'=>$t_ligne[$severity],'priority'=>$t_ligne[$priority],'request_nature'=>$t_ligne[$nature_request],'cadre'=>$t_ligne[$cadre]);
                }
            }
        }

        //Ouverture du Fichier des history
        if (file_exists($historyFile)) {
            $fp=fopen($historyFile,"r");
        }else{
            echo 'Fichier introuvable !';
            exit();
        }
        //Parse du Fichier des history
        while($line=fgets ($fp)) {
            $t_ligne = explode($separateur, htmlspecialchars_decode(html_entity_decode($line)));
            //filtre sur la une date minimum (janvier 2016)
            if ($t_ligne[$history_time_stamp] > 1451602800){
                //On regarde si l'history existe dans les items
                if(array_key_exists($t_ligne[$item_id],$t_item)){
                    $t_history[$t_ligne[$id]] = array('id'=>$t_ligne[$id],'item_id'=>$t_ligne[$item_id],'created_date'=>$t_ligne[$history_time_stamp],'created_by'=>$t_ligne[$logged_by],'assigned_to'=>$t_ligne[$assigned_to],'request_nature'=>$t_ligne[$nature_request]);
                }
            }
        }

        return array('items'=>$t_item,'hitorys'=>$t_history);
    }
}