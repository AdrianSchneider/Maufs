<?php

namespace Maufs\Command;

use Maufs\Aufs;
use Maufs\Config\Loader;
use Maufs\Config\Dumper;
use Maufs\Exception\CannotResolveDirectoryException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class AddLibraryCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('mount:dirs:add')
            ->setRequiresRoot()
            ->setDescription('Add another library to the stack.')
            ->addArgument('path', InputArgument::REQUIRED, 'The path you\'d like to add.')
            ->addArgument('target', InputArgument::OPTIONAL, 'The target of the new mounted directory.', '.')
            ->addOption('test', null,  InputOption::VALUE_OPTIONAL, 'If enabled, it will print out the resulting mount command', false);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->loadConfig($input, $output);
        
        if (in_array($path = $input->getArgument('path'), $config['libraries'])) {
            return $output->writeln("<error>$path already found in libraries</error>");
        }
        
        $config['libraries'][] = $input->getArgument('path');
        $this->performChanges($input, $output, $config);
    }
}