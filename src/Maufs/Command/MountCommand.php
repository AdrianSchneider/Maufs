<?php

namespace Maufs\Command;

use Maufs\Aufs;
use Maufs\Config\Dumper;
use Maufs\Exception\CannotResolveDirectoryException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class MountCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('mount:init')
            ->setRequiresRoot()
            ->setDescription('Mount paths to create a stacked filesystem.')
            ->addArgument('target', InputArgument::OPTIONAL, 'The target of the new mounted directory.', '.')
            ->addOption('test', null,  InputOption::VALUE_OPTIONAL, 'If enabled, it will print out the resulting mount command', false);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Enter the libraries you'd like to add.  Leave a blank line when you are done.</info>");
        
        $config = array('libraries' => array());
        while ($library = $this->dialog->ask($output, 'Add library path? ')) {
            $config['libraries'][] = $library;
        }
        
        $output->writeln(sprintf(
            "<info>Specify where you'd like file modifications to be placed.  If ommitted, %s will be used</info>",
            reset($config['libraries'])
        ));
        
        if ($modificationPath = $this->dialog->ask($output, 'Modifications path? ')) {
            $config['modifications'] = $modificationPath;
        }
        
        $this->performChanges($input, $output, $config);
    }
}