<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Commands\CanvasPrint;

$app = new Application("Test App canvas", "1.0.0");
// loader canvasCommand
$app->add(new CanvasPrint());
$app->run();