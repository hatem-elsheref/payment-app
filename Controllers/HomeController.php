<?php

namespace Controllers;

class HomeController
{

 
    public function index($name)
    {
        echo "Hello Framework With MVC " . $name;
    }
    
}