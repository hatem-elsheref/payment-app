<?php

use Framework\Router;
use Framework\Response;

Router::get('/', 'Frontend\HomeController@index');
Router::get('/backend', 'Backend\BackendController@test');
Router::get('/dgaskjdgas', function () {
    return "hi";
});

Router::get('/callback', function($user, $pass){
    return Response::json(['USER' => $user, 'PASS' => $pass]);
});

