<?php

namespace Framework;

require_once '../Framework/bootstrap.php';

$application = new Application($configurations);
echo $application->start();
