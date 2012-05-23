<?php

namespace Maufs\Command;

use Maufs\Config\Loader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DescribeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('mount:describe')
            ->setDescription('See information about a given directory.')
            ->addArgument('target', InputArgument::OPTIONAL, 'The target to look at', '.');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->loadConfig($input, $output);
        
        if (!empty($config['modifications'])) {
            $output->writeln("Local modification stored in:");
            $output->writeln("    <info>$config[modifications]</info>");
        }
        
        $output->writeln("Libraries:");
        
        foreach ($config['libraries'] as $library) {
            $output->writeln("    <info>$library</info>");
        }
        
    }
}