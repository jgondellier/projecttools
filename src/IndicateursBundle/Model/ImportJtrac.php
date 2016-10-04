<?php

namespace IndicateursBundle\Model;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use IndicateursBundle\Entity\Indic_items;
use IndicateursBundle\Entity\Indic_history;

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

    /**
     * Fonction Permettant de récuperer les données de l'export jtrac au format xml.
     *
     * @param $itemsFile
     * @param $historyFile
     * @return array
     */
    public function getXMLValue($itemsFile,$historyFile){
        $t_item               = array();
        $t_history            = array();
        $dateMin              = new \DateTime('2016-01-01 00:00:00');

        $patterns[0] = '/<detail>.*?<\/detail>/s';
        $patterns[1] = '/<comment>.*?<\/comment>/s';
        $replacements[0] = '<detail></detail>';//<![CDATA[$1]]>
        $replacements[1] = '<comment></comment>';//<![CDATA[$1]]>

        if (file_exists($itemsFile)) {
            $stringFile         = file_get_contents ($itemsFile);
            $stringFile         = preg_replace($patterns,$replacements,$stringFile,-1,$count);
            $simplexmlItemsFile = simplexml_load_string($stringFile);
        }else{
            echo 'Fichier '.$itemsFile.' introuvable !';
            exit();
        }
        //Traitements des items
        foreach($simplexmlItemsFile->dbo_items as $dbo_items){
            if(isset($dbo_items->space_id) && array_key_exists(strval($dbo_items->space_id),$this->list_project)) {
                $dateCreation = \DateTime::createFromFormat('Y-m-d H:i:s', str_replace('T',' ',strval($dbo_items->time_stamp)));
                if ($dateCreation > $dateMin) {
                    $t_item[strval($dbo_items->id)]['id'] = strval($dbo_items->id);
                    $t_item[strval($dbo_items->id)]['project_id'] = strval($dbo_items->space_id);
                    $t_item[strval($dbo_items->id)]['jtrac_id'] = strval($dbo_items->sequence_num);
                    $t_item[strval($dbo_items->id)]['created_date'] = $dateCreation;
                    $t_item[strval($dbo_items->id)]['created_by'] = strval($dbo_items->logged_by);
                    $t_item[strval($dbo_items->id)]['title'] = strval($dbo_items->summary);
                    $t_item[strval($dbo_items->id)]['description'] = strval($dbo_items->detail);
                    $t_item[strval($dbo_items->id)]['status'] = strval($dbo_items->status);
                    if(isset($dbo_items->severity)){
                        $t_item[strval($dbo_items->id)]['severity'] = strval($dbo_items->severity);
                    }
                    if(isset($dbo_items->priority)){
                        $t_item[strval($dbo_items->id)]['priority'] = strval($dbo_items->priority);
                    }
                    if(isset($dbo_items->cus_int_01)){
                        $t_item[strval($dbo_items->id)]['request_nature'] = strval($dbo_items->cus_int_01);
                    }
                    if(isset($dbo_items->cus_int_05)){
                        $t_item[strval($dbo_items->id)]['cadre'] = strval($dbo_items->cus_int_05);
                    }
                }
            }
        }

        if (file_exists($historyFile)) {
            $historyFile            = file_get_contents ($historyFile);
            //$historyFile            = preg_replace($patterns,$replacements,$historyFile);
            //var_dump($historyFile);exit;
            $simplexmlHistoryFile   = simplexml_load_string($historyFile);
        }else{
            echo 'Fichier '.$historyFile.' introuvable !';
            exit();
        }

        //traitements des historys
        foreach($simplexmlHistoryFile->dbo_history as $dbo_history){
            $dateCreation = \DateTime::createFromFormat('Y-m-d H:i:s', str_replace('T',' ',strval($dbo_history->time_stamp)));
            if ($dateCreation > $dateMin) {
                //On ne prend que les historys qui ont un item
                if(isset($t_item[strval($dbo_history->item_id)])){
                    $t_history[strval($dbo_history->id)]['id'] = strval($dbo_history->id);
                    $t_history[strval($dbo_history->id)]['item_id'] = strval($dbo_history->item_id);
                    $t_history[strval($dbo_history->id)]['created_date'] = $dateCreation;
                    $t_history[strval($dbo_history->id)]['created_by'] = strval($dbo_history->logged_by);
                    $t_history[strval($dbo_history->id)]['assigned_to'] = strval($dbo_history->assigned_to);
                    $t_history[strval($dbo_history->id)]['status'] = strval($dbo_history->status);
                    if(isset($dbo_history->cus_int_01)){
                        $t_history[strval($dbo_history->id)]['request_nature'] = strval($dbo_history->cus_int_01);
                    }

                    if($t_item[strval($dbo_history->item_id)]['jtrac_id'] == 55 || $t_item[strval($dbo_history->item_id)]['jtrac_id'] ==93){
                        if(isset($dbo_history->cus_tim_01)) {
                            $dateQualified = \DateTime::createFromFormat('Y-m-d H:i:s', str_replace('T',' ',strval($dbo_history->cus_tim_01)));
                            $t_history[strval($dbo_history->id)]['date_qualified'] = $dateQualified;
                        }
                    }else{
                        if(isset($dbo_history->cus_tim_02)) {
                            $dateQualified = \DateTime::createFromFormat('Y-m-d H:i:s', str_replace('T',' ',strval($dbo_history->cus_tim_02)));
                            $t_history[strval($dbo_history->id)]['date_qualified'] = $dateQualified;
                        }
                    }
                }
            }
        }
        return array('items'=>$t_item,'historys'=>$t_history);
    }

    public function getValue($itemsFile,$historyFile){
        $t_item               = array();
        $t_history            = array();
        $separateur           = '¤';
        $dateMin              = new \DateTime('2016-01-01 00:00:00');

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
        $historyDateAffected = 33;

        //Ouverture du Fichier des items
        if (file_exists($itemsFile)) {
            $fp=fopen($itemsFile,"r");
        }else{
            echo 'Fichier introuvable !';
            exit();
        }
        //Parse du Fichier des items
        $nbLigne = 1;
        while($line=fgets ($fp)) {
            if($nbLigne != 1){
                $t_ligne = explode($separateur, $line);
                //Certaine ligne ont un projet vide
                if(array_key_exists ($space_id,$t_ligne)){
                    //filtre sur la liste des projets
                    if(array_key_exists ($t_ligne[$space_id],$this->list_project)){
                        //filtre sur la une date minimum (janvier 2016)
                        if(\DateTime::createFromFormat('d/m/y H:i',$t_ligne[$item_time_stamp]) > $dateMin){
                            $t_item[$t_ligne[$id]] = array('id'=>$t_ligne[$id],'project_id'=>$t_ligne[$space_id],'jtrac_id'=>$t_ligne[$sequence_num],'created_date'=>$t_ligne[$item_time_stamp],'created_by'=>$t_ligne[$logged_by],'title'=>$t_ligne[$summary],'description'=>$t_ligne[$detail],'status'=>$t_ligne[$status],'severity'=>$t_ligne[$severity],'priority'=>$t_ligne[$priority],'request_nature'=>$t_ligne[$nature_request],'cadre'=>$t_ligne[$cadre]);
                        }
                    }
                }
            }
            $nbLigne++;
        }

        //Ouverture du Fichier des history
        if (file_exists($historyFile)) {
            $fp=fopen($historyFile,"r");
        }else{
            echo 'Fichier introuvable !';
            exit();
        }
        //Parse du Fichier des history
        $nbLigne = 1;
        while($line=fgets ($fp)) {
            if($nbLigne != 1) {
                $t_ligne = explode($separateur, htmlspecialchars_decode(html_entity_decode($line)));
                //filtre sur la une date minimum (janvier 2016)
                if(\DateTime::createFromFormat('d/m/y H:i',$t_ligne[$history_time_stamp]) > $dateMin){
                    //On regarde si l'history existe dans les items
                    if (array_key_exists($t_ligne[$item_id], $t_item)) {
                        $t_history[$t_ligne[$id]] = array('id' => $t_ligne[$id], 'item_id' => $t_ligne[$item_id], 'created_date' => $t_ligne[$history_time_stamp], 'created_by' => $t_ligne[$logged_by], 'assigned_to' => $t_ligne[$assigned_to], 'request_nature' => $t_ligne[$nature_request], 'date_qualified' => $t_ligne[$historyDateAffected]);
                    }
                }
            }
            $nbLigne++;
        }

        return array('items'=>$t_item,'historys'=>$t_history);
    }

    public function setItem($t_item){

    }
}