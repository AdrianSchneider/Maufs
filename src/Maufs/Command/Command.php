<?php

namespace Maufs\Command;

use Maufs\Aufs;
use Maufs\Config\Loader;
use Maufs\Config\Dumper;
use Muafs\Exception\LogicException;
use Maufs\Exception\CannotResolveDirectoryException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends BaseCommand
{
    protected $dialog;
    
    protected $requiresRoot;
    
    /**
     * Checks if this command requires root privileges
     * @return    boolean        TRUE if root is req
     */
    public function requiresRoot()
    {
        return $this->requiresRoot;
    }
    
    /**
     * Sets whether or not this command requires root privileges
     * @param    boolean        TRUE to require root
     * @return   Maufs\Command\Command
     */
    public function setRequiresRoot($flag = true)
    {
        $this->requiresRoot = (bool)$flag;
        return $this;
    }
    
    public function setApplication(Application $application = null)
    {
        parent::setApplication($application);
        if ($application) {
            $this->dialog = $application->getHelperSet()->get('dialog');
        }
    }
    
    protected function loadConfig(InputInterface $input, OutputInterface $output)
    {
        $target = $input->getArgument('target');
        
        if (!file_exists($file = "$target/.maufs")) {
            throw new LogicException("maufs is not configured in $target");
        }
        
        $loader = new Loader();
        return $loader->load($target);
    }
    
    protected function performChanges(InputInterface $input, OutputInterface $output, array $config)
    {
        $target = $input->getArgument('target');
        
        try {
            $aufs = new Aufs($target);
        
            if ($testing = $input->getOption('test')) {
                $output->writeln("<info>Test run output:</info>");
                $output->writeln("<comment>" . $aufs->testExecute($config) . "</comment>");
                return;
            }
        
            $result = $aufs->mount($config);
        
        } catch (CannotResolveDirectoryException $e) {
            $output->writeln("<error>Could not resolve " . $e->getMessage() . "</error>");
            return;
        }
        
        $dumper = new Dumper($target);
        $dumper->dump($config);
    }
}