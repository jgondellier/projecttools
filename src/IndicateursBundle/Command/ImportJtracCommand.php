<?php

namespace IndicateursBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use IndicateursBundle\Model\ImportJtrac;
use IndicateursBundle\Entity\Indic_items;
use IndicateursBundle\Entity\Indic_history;

class ImportJtracCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:jtrac')
            ->setDescription('Import des tickets jtrac.')
            ->setHelp("Commande permettant d'importer des tickets jtracs.")
            ->addArgument('fileItems', InputArgument::REQUIRED, 'Jtrac item.')
            ->addArgument('fileHistory', InputArgument::REQUIRED, 'Jtrac history.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container      = $this->getContainer();
        $itemsFile      = $input->getArgument('fileItems');
        $historyFile    = $input->getArgument('fileHistory');
        $list_project   = $container->getParameter('list_project');
        $dateFormat     = 'Y-m-d H:i:s';

        $output->writeln([
            'Début de l\'import',
            '============',
            '',
        ]);
        $output->writeln([
            'Ouverture des fichiers jtracs',
            '',
        ]);

        $t_jtrac  = $container->get('indicateurs.importjtrac')->getXMLValue($itemsFile,$historyFile);

        $entityManager = $this->getContainer()->get('doctrine')->getManager();

        $output->writeln([
            '',
            'Mise en base des items',
            '',
        ]);
        $t_items = $t_jtrac['items'];

        //Pour faire jolie on créé une progress bar
        $progress = new ProgressBar($output, count($t_items));
        $progress->start();

        if (count($t_items)>0){
            foreach($t_items as $item){
                $itemId = $item['id'];
                //Vérification si le contenu n'existe pas déjà
                $entity_item = $entityManager->getRepository('IndicateursBundle:Indic_items')->getItemByItemId($itemId);
                if($entity_item == Null) {
                    //L'item n'existe pas on le créé
                    $entity_item = new Indic_items();
                }
                $entity_item->setItemId($item['id']);
                /*Traitement pour les id jtrac*/
                $entity_item->setJtracId($list_project[$item['project_id']]['prefix'].'-'.$item['jtrac_id']);
                $entity_item->setProjectId($item['project_id']);
                $entity_item->setCreatedDate($item['created_date']);
                $entity_item->setCreatedBy($item['created_by']);
                $entity_item->setTitle($item['title']);
                $entity_item->setDescription($item['description']);
                $entity_item->setStatus($item['status']);
                if(isset($item['severity'])){
                    $entity_item->setSeverity($item['severity']);
                }
                if(isset($item['priority'])){
                    $entity_item->setPriority($list_project[$item['project_id']]['priority'][$item['priority']]);
                }else{
                    $entity_item->setPriority('-');
                }
                if(isset($item['request_nature'])){
                    //On uniformise la request nature
                    $entity_item->setRequestNature($list_project[$item['project_id']]['type'][$item['request_nature']]);
                }else{
                    $entity_item->setRequestNature('-');
                }
                if(isset($item['cadre'])){
                    $entity_item->setCadre($item['cadre']);
                }
                $entityManager->persist($entity_item);
                //La progressbar avance
                $progress->advance();
            }
            //Sauvegarde en base
            $entityManager->flush();
            //Fin de la barre de progression
            $progress->finish();
        }else{
            echo 'Aucun Item a traiter.', PHP_EOL;
        }

        $output->writeln([
            '',
            'Mise en base des history',
            '',
        ]);

        $t_history = $t_jtrac['historys'];

        //Pour faire jolie on créé une progress bar
        $progress = new ProgressBar($output, count($t_history));
        $progress->start();

        if (count($t_history)>0){
            foreach($t_history as $history){
                $historyId = $history['id'];
                //On ne peut créer une history que si celle ci est rattachée a un item
                //On recherche l'item auquel est rattaché l'history
                $entity_item = $entityManager->getRepository('IndicateursBundle:Indic_Items')->getItemByItemId($history['item_id']);
                if($entity_item != null) {
                    //Vérification si le contenu n'existe pas déjà
                    $entity_history = $entityManager->getRepository('IndicateursBundle:Indic_history')->getHistoryByHistoryId($historyId);
                    if($entity_history == Null) {
                        //L'item n'existe pas on le créé
                        $entity_history = new Indic_history();
                    }
                    $entity_history->setHistoryId($history['id']);
                    $entity_history->setCreatedDate($history['created_date']);
                    if(isset($history['date_qualified']) && $history['date_qualified'] != ""){
                        $entity_history->setQualifiedDate($history['date_qualified']);
                    }
                    $entity_history->setCreatedBy($history['created_by']);
                    if(isset($history['request_nature']) && $history['request_nature'] != ""){
                        $entity_history->setRequestNature($history['request_nature']);
                    }
                    if(isset($history['assigned_to']) && $history['assigned_to'] != ""){
                        $entity_history->setAssignedTo($history['assigned_to']);
                    }
                    if(isset($history['status']) && $history['status'] != ""){
                        $entity_history->setStatus($history['status']);
                    }
                    $entity_history->setIndicItems($entity_item);

                    $entityManager->persist($entity_history);
                }else{
                    //History sans item
                }
                //La progressbar avance
                $progress->advance();
            }
            //Sauvegarde en base
            $entityManager->flush();
            //Fin de la barre de progression
            $progress->finish();
        }else{
            echo 'Aucune History a traiter.', PHP_EOL;
        }

        $output->writeln([
            '',
            'Import terminé',
            '========================',
            '',
        ]);
    }
}