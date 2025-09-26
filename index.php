<?php

@include_once __DIR__ . '/utils/load.php';

use Plain\Helpers\Plugin;
use Kirby\Exception\Exception;

if (option('microman.formblock.from_email')) {
    throw new Exception('Deprecation error: Option prefix microman.formblock changed to plain.formblock in config.php');
};

Plugin::load('plain/formblock', autoloader: true);