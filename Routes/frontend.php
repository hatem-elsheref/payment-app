<?php

use Framework\Router;

// show products page
Router::get('/', 'Frontend\HomeController@index');
Router::get('/products', 'Frontend\HomeController@products');
Router::get('/buy-now/{product}', 'Frontend\HomeController@showCheckoutForm');
Router::get('/add-to-cart/{product}', 'Frontend\HomeController@addToCart');
Router::post('/checkout', 'Frontend\HomeController@checkout');
Router::get('/processing-to-payment', 'Frontend\HomeController@process');
Router::get('/pay', 'Frontend\HomeController@completePayment');
Router::post('/pay', 'Frontend\HomeController@createStripePaymentIntent');
Router::get('/payment/{orderId}', 'Frontend\HomeController@paymentCallback');

