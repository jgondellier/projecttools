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
        $itemsFile      = $input->getArgument('fileItems');
        $historyFile    = $input->getArgument('fileHistory');

        $output->writeln([
            'Début de l\'import',
            '============',
            '',
        ]);
        $output->writeln([
            'Ouverture des fichiers jtracs',
            '',
        ]);

        $t_jtrac  = $this->getContainer()->get('indicateurs.importjtrac')->getValue($itemsFile,$historyFile);

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
                if($entity_item == Null){
                    //L'item n'eciste pas on le créé
                    $entity_item = new Indic_items();
                    $entity_item->setItemId($item['id']);
                    $entity_item->setJtracId($item['jtrac_id']);
                    $entity_item->setProjectId($item['project_id']);
                    $entity_item->setCreatedDate(\DateTime::createFromFormat('m/d/y H:i',$item['created_date']));
                    $entity_item->setCreatedBy($item['created_by']);
                    $entity_item->setTitle($item['title']);
                    $entity_item->setDescription($item['description']);
                    $entity_item->setStatus($item['status']);
                    if($item['severity']){
                        $entity_item->setSeverity($item['severity']);
                    }
                    if($item['priority']){
                        $entity_item->setPriority($item['priority']);
                    }
                    if($item['request_nature']){
                        $entity_item->setRequestNature($item['request_nature']);
                    }
                    if($item['cadre']){
                        $entity_item->setCadre($item['cadre']);
                    }
                    $entityManager->persist($entity_item);
                }
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
                //Vérification si le contenu n'existe pas déjà
                $entity_history = $entityManager->getRepository('IndicateursBundle:Indic_history')->getHistoryByHistoryId($historyId);
                if($entity_history == Null){
                    //On ne peut créer une history que si celle ci est rattachée a un item
                    //On recherche l'item auquel est rattaché l'history
                    $entity_item = $entityManager->getRepository('IndicateursBundle:Indic_Items')->getItemByItemId($history['item_id']);
                    if($entity_item != null) {
                        //L'item n'existe pas on le créé
                        $entity_history = new Indic_history();
                        $entity_history->setHistoryId($history['id']);
                        $entity_history->setCreatedDate(\DateTime::createFromFormat('m/d/y H:i', $history['created_date']));
                        if($history['date_qualified']){
                            $entity_history->setQualifiedDate(\DateTime::createFromFormat('m/d/y H:i', $history['date_qualified']));
                        }
                        $entity_history->setCreatedBy($history['created_by']);
                        if($history['request_nature']){
                            $entity_history->setRequestNature($history['request_nature']);
                        }
                        if($history['assigned_to']){
                            $entity_history->setAssignedTo($history['assigned_to']);
                        }
                        $entity_history->setIndicItems($entity_item);
                    }else{
                        //History sans item
                    }

                    $entityManager->persist($entity_history);
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