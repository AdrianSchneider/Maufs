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

class RemountCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('remount')
            ->setRequiresRoot()
            ->setDescription('Remount a mount (if config exists).')
            ->addArgument('target', InputArgument::OPTIONAL, 'The target of the new mounted directory.', '.')
            ->addOption('test', null,  InputOption::VALUE_OPTIONAL, 'If enabled, it will print out the resulting mount command', false);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->loadConfig($input, $output);
        $this->performChanges($input, $output, $config);
    }
}
