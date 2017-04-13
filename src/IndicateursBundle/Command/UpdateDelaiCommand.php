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


class UpdateDelaiCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('update:jtrac:delai')
            ->setDescription('Update des tickets jtrac.')
            ->setHelp("Commande permettant de mettre à jour les tickets jtracs avec les données de stats.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container          = $this->getContainer();
        $entityManager      = $container->get('doctrine')->getManager();
        $t_status           = $container->getParameter('status');
        $list_project       = $container->getParameter('list_project');
        $listTRSBUser       = $container->getParameter('list_trsb_user');

        $output->writeln([
            'Récupération des items',
            '============',
            '',
        ]);
        $entity_item = $entityManager->getRepository('IndicateursBundle:Indic_items')->findAll();

        //Deali de traitement des demandes
        $output->writeln([
            'Ajout des delais',
            '============',
            '',
        ]);
        //Pour faire jolie on créé une progress bar
        $progress = new ProgressBar($output, count($entity_item));
        $progress->start();
        foreach($entity_item as $item){
            /* @var \IndicateursBundle\Entity\Indic_TRSB $entity_trsb */
            $entity_trsb = $entityManager->getRepository('IndicateursBundle:Indic_TRSB')->getTRSBByItemId($item->getId());

            if($entity_trsb) {
                $jtracType          = $item->getRequestNature();
                $projectid          = $item->getProjectId();
                $dateDebut          = $entity_trsb->getKnowledgeDate();
                $dateCorrected      = $entity_trsb->getCorrectedDate();
                $dateClosed         = $entity_trsb->getClosedDate();
                $dateAnswer         = $entity_trsb->getAnswerDate();

                //On renseigne le temps de traitement de la demande
                $delaiTreatment     = 0;
                if($dateDebut){
                    if($dateCorrected){
                        $delaiTreatment = $this->getTimeElapsed($dateDebut,$dateCorrected);
                    }elseif($dateClosed){
                        $delaiTreatment = $this->getTimeElapsed($dateDebut,$dateClosed);
                    }
                    if($delaiTreatment != 0){
                        $entity_trsb->setTreatmentTime(round($delaiTreatment,2));
                    }elseif($delaiTreatment == 0){
                        $entity_trsb->setTreatmentTime(0);
                    }
                    //On renseigne le temps de réponse
                    $delaiResponse = 0;
                    if(array_key_exists($item->getCreatedBy(),$listTRSBUser)){
                        $entity_trsb->setResponseTime(0);
                    }else if($dateAnswer){
                        $delaiResponse = round($this->getTimeElapsed($dateDebut,$dateAnswer),2);
                        $entity_trsb->setResponseTime($delaiResponse);
                    }

                    if($delaiTreatment != 0 || $delaiResponse){
                        $entityManager->persist($entity_trsb);
                    }
                }
            }

            //La progressbar avance
            $progress->advance();
        }
        $entityManager->flush();
        //Fin de la barre de progression
        $progress->finish();

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
    private function getTimeElapsed($date1,$date2,$step='m',$outputFormat='m'){
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