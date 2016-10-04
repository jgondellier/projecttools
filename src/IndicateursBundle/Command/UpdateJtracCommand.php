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
            ->setName('import:jtrac')
            ->setDescription('Update des tickets jtrac.')
            ->setHelp("Commande permettant de mettre à jour les tickets jtracs avec les données de stats.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $entityManager      = $this->getContainer()->get('doctrine')->getManager();
        $listTRSBUser       = $input->getArgument('list_trsb_user');

        $output->writeln([
            'Récupération des items',
            '============',
            '',
        ]);
        $entity_item = $entityManager->getRepository('IndicateursBundle:Indic_items')->findAll();

        //Renseigne si TRSB a été dans la boucle
        $output->writeln([
            'Ajout du TAG TRSB',
            '============',
            '',
        ]);
        foreach($entity_item as $item){
            $update = False;
            if(in_array($item->getCreatedBy(),$listTRSBUser)){
                $item->setTrsb(True);
                $update = True;
            }else{
                //on parcourt les history pour voir
                $entity_history = $entityManager->getRepository('IndicateursBundle:Indic_history')->findById($item->getId());
                foreach ($entity_history as $history){
                    if(in_array($entity_history->getCreatedBy(),$listTRSBUser) || in_array($entity_history->getAssignedTo(),$listTRSBUser)){
                        $item->setTrsb(True);
                        $update = True;
                        break;
                    }
                }
            }
            if($update){
                $entityManager->persist($item);
            }
        }
        $entityManager->flush();



        //Renseigne le temps de traitement des anomalies
        /*Prendre la date source au moment ou TRSB est assigné*/
    }
}