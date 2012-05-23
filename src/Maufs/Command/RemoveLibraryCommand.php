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

class RemoveLibraryCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('mount:dirs:remove')
            ->setRequiresRoot()
            ->setDescription('Remove a library from  the stack.')
            ->addArgument('path', InputArgument::REQUIRED, 'The path you\'d like to remove (partial is fine).')
            ->addArgument('target', InputArgument::OPTIONAL, 'The target of the new mounted directory.', '.')
            ->addOption('test', null,  InputOption::VALUE_OPTIONAL, 'If enabled, it will print out the resulting mount command', false);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->loadConfig($input, $output);

        $find   = $input->getArgument('path');
        $found  = false;
        
        foreach ($config['libraries'] as $key => $library) {
            $found = true;
            if (strpos($library, $find) !== false) {
                if ($this->dialog->askConfirmation($output, "Do you want to remove <comment>$library</comment>? ")) {
                    $removeKey = $key;
                    break;
                }
            }
        }
        
        if (!$found) {
            return $output->writeln("<error>'$find' was not found as a library</error>");
        }
        
        if (!isset($removeKey)) {
            return $output->writeln("Cancelled");
        }
        
        
        unset($config['libraries'][$removeKey]);
        $config['libraries'] = array_values($config['libraries']);
        
        
        $this->performChanges($input, $output, $config);
    }
}