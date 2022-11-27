<?php

namespace Controllers\Frontend;

use Framework\View;

class HomeController
{

    public function index()
    {
        $data = ['x' => 'this is layout var', 'z' => 'this is view var'];
        return View::renderBackendView('Backend.User.addUser', $data);
    }
    
}