<?php

namespace Maufs\Config;

class Loader
{
    public function load($target)
    {
        return include $target.'/.maufs';
    }
}