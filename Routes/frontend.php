<?php
use Framework\Router;

Router::get('/', 'HomeController@index');
Router::get('/home/action', 'HomeController@index');
Router::get('/user', 'HomeController@user');
Router::get('/ASDGASJHGD', 'HomeController@admin');
Router::get('/offer', 'Offerontroller@offerAction');
Router::get('/callback', function($user, $pass){
    header('content-type:application/json');
    return json_encode(['USER' => $user, 'PASS' => $pass]);
});

