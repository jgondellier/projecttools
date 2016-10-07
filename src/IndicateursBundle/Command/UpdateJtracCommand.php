<?php

namespace IndicateursBundle\Command;

use IndicateursBundle\Entity\Indic_TRSB;
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
            ->setName('update:jtrac:TRSB')
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
            $findKnowledge      = False;
            $findCorrected      = False;
            $findAnswer         = False;
            $refused            = 0;

            //On regarde si une entrée TRSB n'existe pas déjà
            /* @var \IndicateursBundle\Entity\Indic_TRSB $entity_trsb */
            $entity_trsb = $entityManager->getRepository('IndicateursBundle:Indic_TRSB')->getTRSBByItemId($item->getId());

            //Date de creation du ticket
            $entity_trsb = $this->setTRSBDate($item,$entity_trsb,$item->getCreatedDate(),'open');

            //on parcourt les history pour voir
            $entity_history = $entityManager->getRepository('IndicateursBundle:Indic_history')->getAllHistoryByItemId($item->getId());
            foreach ($entity_history as $pos => $history){
                /* @var \IndicateursBundle\Entity\Indic_history $history */

                //On regarde a quel moment TRSB est informé, présent dans le workflow
                if(!$findKnowledge && (array_key_exists($history->getCreatedBy(),$listTRSBUser) || array_key_exists($history->getAssignedTo(),$listTRSBUser))){
                    //On ne prend pas si trsb est present que pour la fermeture
                    if ($history->getStatus() != $t_status['closed'][$item->getProjectId()]){
                        $entity_trsb = $this->setTRSBDate($item,$entity_trsb,$history->getCreatedDate(),'knowledge');
                        $findKnowledge = True;
                    }
                }
                //On regarde a quel moment TRSB à fait sa première réponse
                if(!$findAnswer && (array_key_exists($history->getCreatedBy(),$listTRSBUser) && $pos != 0)){
                    //On ne prend pas si trsb est present que pour la fermeture sans avoir renseigné la date de correction
                    if (($history->getStatus() != $t_status['closed'][$item->getProjectId()])){
                        $entity_trsb = $this->setTRSBDate($item,$entity_trsb,$history->getCreatedDate(),'answer');
                        $findAnswer = True;
                    }
                }
                //On regarde quand le statut du ticket passe à corrigé pour la dernière fois
                if(array_key_exists($history->getCreatedBy(),$listTRSBUser) && ($history->getStatus() == $t_status['corrected'][$item->getProjectId()])){
                    if(!$findCorrected){
                        //Date de la première correction
                        $entity_trsb = $this->setTRSBDate($item,$entity_trsb,$history->getCreatedDate(),'firstcorrected');
                    }
                    //On garde la date de la correction définitive
                    $entity_trsb = $this->setTRSBDate($item,$entity_trsb,$history->getCreatedDate(),'corrected');
                    $findCorrected = True;
                }
                //On renseigne la date de fermeture
                if($history->getStatus() == $t_status['closed'][$item->getProjectId()]){
                    $entity_trsb = $this->setTRSBDate($item,$entity_trsb,$history->getCreatedDate(),'closed');
                    if(!$findCorrected){
                        //Si on n'a jamais trouvé de statut corrigé mais que le ticket est fermé
                        $entity_trsb = $this->setTRSBDate($item,$entity_trsb,$history->getCreatedDate(),'firstcorrected');
                        $entity_trsb = $this->setTRSBDate($item,$entity_trsb,$history->getCreatedDate(),'corrected');
                    }
                }

                //On calcul le nombre de réouverture en fait le nombre de refuse
                if($history->getStatus() == $t_status['refused'][$item->getProjectId()]){
                    $refused++;
                }
            }
            if($refused!=0){
                $entity_trsb->setRefusedCount($refused);
            }
            //On ne doit créé des entrées que pour les tickets concernés par TRSB
            if($findKnowledge){
                $entityManager->persist($entity_trsb);
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

    private function setTRSBDate($item,$entity_trsb,$date,$type){
        //On regarde si on entrée TRSB n'existe pas déjà
        /* @var \IndicateursBundle\Entity\Indic_TRSB $entity_trsb */
        if(!$entity_trsb){
            $entity_trsb = new Indic_TRSB();
        }
        $entity_trsb->setIndicItems($item);
        switch ($type){
            case 'open':
                $entity_trsb->setOpenDate($date);
                break;
            case 'knowledge':
                $entity_trsb->setKnowledgeDate($date);
                break;
            case 'answer':
                $entity_trsb->setAnswerDate($date);
                break;
            case 'corrected':
                $entity_trsb->setCorrectedDate($date);
                break;
            case 'firstcorrected':
                $entity_trsb->setFirstCorrectedDate($date);
                break;
            case 'closed':
                $entity_trsb->setClosedDate($date);
                break;
        }
        return $entity_trsb;
    }
}