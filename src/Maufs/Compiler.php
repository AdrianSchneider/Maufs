<?php

namespace Maufs;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

class Compiler
{
    protected $version = '1.0';
    
    public function compile($pharFile = 'maufs.phar') 
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }
        
        $phar = new \Phar($pharFile, 0, 'maufs.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);
        $phar->startBuffering();
        
        $finder = new Finder();
        $finder
            ->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->notName('Compiler.php')
            ->in(__DIR__.'/..');
        
        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }
        
        $finder = new Finder();
        $finder
            ->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->in(__DIR__ . '/../../vendor');
        
        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }
        
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/autoload.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/autoload_namespaces.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/autoload_classmap.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/ClassLoader.php'));
        $this->addBin($phar);
        
        $phar->setStub($this->getStub());
        $phar->stopBuffering();
    }
    
    protected function addFile($phar, $file, $strip = true)
    {
        $path = str_replace(dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR, '', $file->getRealPath());
        
        $content = file_get_contents($file);
        if ($strip) {
            $content = $this->stripWhitespace($content);
        } elseif ('LICENSE' === basename($file)) {
            $content = "\n".$content."\n";
        }
        
        $content = str_replace('@package_version@', $this->version, $content);
        
        $phar->addFromString($path, $content);    }
    
    protected function addBin($phar)
    {
        $content = file_get_contents(__DIR__.'/../../bin/maufs');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('bin/maufs', $content);
    }
    
    protected function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }
        
        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }
        
        return $output;
    }
    
    protected function getStub()
    {
        $stub = <<<EOF
#!/usr/bin/env php
<?php

Phar::mapPhar('maufs.phar');
        
EOF;
        
        return $stub .= <<<'EOF'
require 'phar://maufs.phar/bin/maufs';
        
__HALT_COMPILER();
EOF;
    }
}