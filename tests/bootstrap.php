<?php
require_once(dirname(__FILE__).'/../src/Templater.php');

// register vendors if possible
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require_once(__DIR__.'/../vendor/autoload.php');
}
