<?php

require_once 'Application.php';

spl_autoload_register(function ($className){
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);

    $file_name = APP_PATH . DIRECTORY_SEPARATOR . $className . '.php';
    if (file_exists($file_name)){
        require_once $file_name;
    }else{
        throw new Exception("Class Not Found");
        exit(0);
    }

});


$configurations = [];

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'paths.php';

function loadConfigurations()
{
    global $configurations;

    foreach (glob(CONFIG_PATH . DIRECTORY_SEPARATOR . '*.php') as $config_file){
        $base_name = pathinfo($config_file, PATHINFO_FILENAME);
        if ($base_name === 'paths')
            continue;

        $configurations[$base_name] = include_once $config_file;
    }
}

function loadHelpers()
{
    foreach (glob(HELPER_PATH . DIRECTORY_SEPARATOR . '*.php') as $helper_file){
        include_once $helper_file;
    }
}

function loadRoutes()
{
    foreach (glob(ROUTES_PATH . DIRECTORY_SEPARATOR . '*.php') as $route_file){
        require_once  $route_file;
    }
}

(function (){
    loadConfigurations();
    loadRoutes();
    loadHelpers();
})();

