<?php

use Framework\Router;
use Framework\Response;
use Models\Model;

Router::get('/', 'Frontend\HomeController@index');
Router::get('/backend/{page}/page', 'Backend\BackendController@test');
Router::get('/hello/{var1}/hi/{var2}', function ($var1, $var2) {
    return "hello $var1 hi $var2";
});

Router::get('/db', function(){
    //return Response::json(['USER' => $user, 'PASS' => $pass]);

    $data = [

        'id'      => 1,
        'name'    => 'Hatem Mohamed Elsheref',
        'address' => 'Cairo, Egypt',
        'phone'   => '123465789',
        'age'     => 24
    ];
    return (new Model())->insert($data);

});

