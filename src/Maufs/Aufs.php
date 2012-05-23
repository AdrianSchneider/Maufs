<?php

namespace Maufs;

use Maufs\Exception\CannotResolveDirectoryException;
use Symfony\Component\Process\Process;

class Aufs
{
    const PATH_SEPARATOR = ':';

    /**
     * @var    string        Mount target
     */
    protected $target;
    
    /**
     * Sets and verifies the mount target
     * @param    string        Mount target
     */
    public function __construct($target = '.')
    {
        if (!$this->target = realpath($target)) {
            throw new CannotResolveDirectoryException($target);
        }
    }
    
    /**
     * Mounts the configuration
     * Any existing mount will be removed
     * 
     * @param    array        Configuration
     * @return   boolean      TRUE on process success
     */
    public function mount(array $config)
    {
        $process = new Process($this->generateUmount($config));
        $process->run();
        
        $process = new Process($this->generateCommand($config));
        $process->run();

        return $process->isSuccessful();
    }
    
    /**
     * Unmounts the target
     * @return    boolean        TRUE on process success
     */
    public function unmount()
    {
        $process = new Process($this->generateUmount());
        $process->run();
        
        return $process->isSuccessful();
    }

    /**
     * Returns the mount command from config
     * @param    array        Mount configuration
     * @return   string       Mount code
     */
    public function testExecute($config)
    {
        return $this->generateCommand($config);
    }
    
    /**
     * Generates the umount code for the target
     * @return    string        umount code
     */
    protected function generateUmount()
    {
        return sprintf(
            'umount -f %s',
            $this->target
        );
    }
    
    /**
     * Generates the mount command from config
     * @param    array        Mount configuration
     * @return   string       Mount code
     */
    protected function generateCommand(array $config)
    {
        return sprintf(
            'mount -t aufs -o br=%s none %s',
            $this->getPaths($config),
            $this->target
        );
    }
    
    /**
     * Returns a list of directories that aufs can understand
     * @param    array        Configuration
     * @return   string       dira:dir2:dir2...
     */
    protected function getPaths(array $config)
    {
        $paths = array();
        $configPaths = $config['libraries'];
        
        if (!empty($config['modifications'])) {
            array_unshift($configPaths, $config['modifications']);
        }
        
        foreach ($configPaths as $path) {
            if (!$realPath = realpath($path)) {
                throw new Exception\CannotResolveDirectoryException($path);
            }
            
            $paths[] = $realPath;
        }
        
        return implode(self::PATH_SEPARATOR, $paths);
    }
}