<?php

namespace Framework;

require_once '../Framework/bootstrap.php';

// ECHO '<PRE>';

$application = new Application($configurations);
echo $application->start();
