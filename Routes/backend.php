<?php

use Framework\Router;

Router::get('/posts',                 'Backend\PostController@index');
Router::get('/posts/create',          'Backend\PostController@create');
Router::get('/posts/{post_id}',       'Backend\PostController@show');
Router::post('/posts',                'Backend\PostController@store');
Router::get('/posts/{post_id}/edit',  'Backend\PostController@edit');
Router::post('/posts/{post_id}/edit', 'Backend\PostController@update');
Router::post('/destroy/{post_id}',    'Backend\PostController@destroy');

