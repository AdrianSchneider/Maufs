<?php

namespace Maufs\Console;

use Maufs\Command;
use Muafs\Exception\LogicException;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class Application extends BaseApplication
{
    /**
     * Wraps command in permission check, and prints a pretty error for any LogicException's
     * 
     * {@inheritDoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->registerCommands();
        
        try {
            $cmd = $this->getCommandForPermissionsCheck($input);

            if (method_exists($cmd, 'requiresRoot') and $cmd->requiresRoot() and !$this->hasRootPrivileges()) {
                return $output->writeln("<error>This command requires root to execute</error>");
            }
            
            return parent::doRun($input, $output);
            
        } catch (LogicException $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
        }
    }
    
    /**
     * Gets an instance of the command for testing
     * @param    InputInterface
     * @return   Command
     */
    protected function getCommandForPermissionsCheck(InputInterface $input)
    {
        $name = $this->getCommandName($input);
        if (!$name) {
            $name = 'list';
            $input = new ArrayInput(array('command' => 'list'));
        }
        return $this->find($name);
    }
    
    /**
     * {@inheritDoc}
     */
    protected function registerCommands()
    {
        $finder = new Finder();
        $finder
            ->files()
            ->name('*Command.php')
            ->notName('Command.php')
            ->in(__DIR__.'/../Command');
        
        foreach ($finder as $file) {
            $class = "Maufs\\Command\\" . basename($file, ".php");
            $this->add(new $class());
        }
    }
    
    /**
     * Determines whether this user has root or not
     * @return    boolean        TRUE if user is root
     */
    protected function hasRootPrivileges()
    {
        return exec('whoami') == 'root';
    }
}