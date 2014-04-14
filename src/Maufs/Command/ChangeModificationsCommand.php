<?php

namespace Maufs\Command;

use Maufs\Config\Loader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ChangeModificationsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('dirs:modifications')
            ->setRequiresRoot()
            ->setDescription('Change the local modifications directory.')
            ->addArgument('path', InputArgument::REQUIRED, 'The new path you\'d like to use for local modifications.')
            ->addArgument('target', InputArgument::OPTIONAL, 'The target of the new mounted directory.', '.')
            ->addOption('test', null,  InputOption::VALUE_OPTIONAL, 'If enabled, it will print out the resulting mount command', false);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->loadConfig($input, $output);
        $config['modifications'] = $input->getArgument('path');
        
        $this->performChanges($input, $output, $config);
    }
}
