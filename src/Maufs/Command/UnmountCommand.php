<?php

namespace Maufs\Command;

use Maufs\Aufs;
use Maufs\Config\Loader;
use Maufs\Exception\CannotResolveDirectoryException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UnmountCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('mount:unmount')
            ->setRequiresRoot()
            ->setDescription('Mount paths to create a stacked filesystem.')
            ->addArgument('target', InputArgument::OPTIONAL, 'The target of the new mounted directory.', '.')
            ->addOption('test', null,  InputOption::VALUE_OPTIONAL, 'If enabled, it will print out the resulting mount command', false);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $target = $input->getArgument('target');
        if (!$this->dialog->askConfirmation($output, "Are you sure you want to unmount '$target'? ", false)) {
            return $output->writeln("<error>Aborted</error>");
        }

        $config = $this->loadConfig($input, $output);
        
        $aufs = new Aufs($target);
        $aufs->unmount();
        
        if ($this->dialog->askConfirmation($output, 'Do you also want to delete your maufs configuration? ', false)) {
            unlink("$target/.maufs");
        }
    }
}