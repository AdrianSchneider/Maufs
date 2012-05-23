<?php

namespace Maufs\Config;

class Dumper
{
    protected $target;
    
    public function __construct($target)
    {
        $this->target = $target;
    }
    
    public function dump(array $config)
    {
        file_put_contents(
            $this->target . '/.maufs',
            '<?php return ' . var_export($config, true) . ';'
        );
    }
}