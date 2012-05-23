<?php

if (!file_exists($file = __DIR__.'/../vendor/autoload.php')) {
    die('You must first run composer.');
}

$loader = include $file;
$loader->add('Maufs', __DIR__);