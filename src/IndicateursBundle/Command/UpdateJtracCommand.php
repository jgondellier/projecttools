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


class UpdateJtracCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('update:jtrac')
            ->setDescription('Update des tickets jtrac.')
            ->setHelp("Commande permettant de mettre à jour les tickets jtracs avec les données de stats.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container          = $this->getContainer();
        $entityManager      = $container->get('doctrine')->getManager();
        $listTRSBUser       = $container->getParameter('list_trsb_user');
        $t_status           = $container->getParameter('status');

        $output->writeln([
            'Récupération des items',
            '============',
            '',
        ]);
        $entity_item = $entityManager->getRepository('IndicateursBundle:Indic_items')->findAll();

        //Renseigne si TRSB a été dans la boucle
        $output->writeln([
            'Ajout du TAG et date TRSB',
            '==============================',
            '',
        ]);
        //Pour faire jolie on créé une progress bar
        $progress = new ProgressBar($output, count($entity_item));
        $progress->start();
        foreach($entity_item as $item){
            $update = False;
            if(array_key_exists($item->getCreatedBy(),$listTRSBUser)){
                $item->setTrsb(True);
                $item->setTRSBDate($item->getCreatedDate());
                $update = True;
            }else{
                //on parcourt les history pour voir
                $entity_history = $entityManager->getRepository('IndicateursBundle:Indic_history')->getAllHistoryByItemId($item->getId());
                foreach ($entity_history as $history){
                    if(array_key_exists($history->getCreatedBy(),$listTRSBUser) || array_key_exists($history->getAssignedTo(),$listTRSBUser)){
                        //On ne prend pas si trsb est present que pour la fermeture
                        if ($history->getStatus() != $t_status['corrected'][$item->getProjectId()]){
                            $item->setTrsb(True);
                            $item->setTRSBDate($history->getCreatedDate());
                            $update = True;
                            break;
                        }
                    }
                }
            }
            if($update){
                $entityManager->persist($item);
            }
            //La progressbar avance
            $progress->advance();
        }
        $entityManager->flush();
        //Fin de la barre de progression
        $progress->finish();

        $output->writeln([
            '',
            'FIN du traitement',
            '==============================',
            '',
        ]);
    }
}