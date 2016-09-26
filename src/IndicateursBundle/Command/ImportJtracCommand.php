<?php

namespace IndicateursBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class ImportJtracCommand extends Command
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
            'Ouverture du fichier items',
            '',
        ]);

        $output->writeln([
            'Mise en base des items',
            '',
        ]);

        $output->writeln([
            'Ouverture du fichier history',
            '',
        ]);

        $output->writeln([
            'Mise en base des history',
            '',
        ]);

        $output->writeln([
            'Import terminé',
            '============',
            '',
        ]);
    }
}