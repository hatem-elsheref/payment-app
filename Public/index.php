<?php

namespace Framework;
session_start();

require_once '../Framework/bootstrap.php';
require_once '../vendor/autoload.php';
$application = new Application($configurations);
echo $application->start();
