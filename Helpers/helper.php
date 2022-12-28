<?php

use Framework\Application;


if (!function_exists('dd')){
    function dd(...$data){
        echo '<pre>';
        var_dump($data);
        exit(0);
    }
}

if (!function_exists('view')){
    function view($view, $data = []){
        return \Framework\View::renderView($view, false, $data);
    }
}
if (!function_exists('config')){
    function config($key, $default = null){
        $keys = explode('.', $key);
        $currentConfigArray = Application::$app->configurations;

        foreach ($keys as $index => $value){
            if (is_array($currentConfigArray[$value]))
                $currentConfigArray = $currentConfigArray[$value];
            elseif(is_null($currentConfigArray[$value]))
                return $default;
            else
                return $currentConfigArray[$value];
        }
    }
}

if (!function_exists('url')){
    function url(){
        return Application::$app->request->baseUrl();
    }
}

if (!function_exists('load_frontend_assets')){
    function load_frontend_assets($asset){
        return trim(url(), "/") . "/assets/frontend/" . trim($asset, "/");
    }
}


if (!function_exists('request')){
    function request($input = null){
        return Application::$app->request->body($input);
    }
}

if (!function_exists('alert')){
    function alert(){
        require_once VIEWS_PATH . DIRECTORY_SEPARATOR ."Frontend" . DIRECTORY_SEPARATOR . "Layouts" . DIRECTORY_SEPARATOR . "alert.tpl.php";
    }
}

if (!function_exists('redirectTo')){
    function redirectTo($url){
        header("Location: "  . $url);
    }
}

if (!function_exists('session_has')){
    function session_has($key, $isFlash = false){
        return $isFlash ? isset($_SESSION['flash'][$key]) : isset($_SESSION[$key]);
    }
}
if (!function_exists('session')){
    function session($key, $default = null, $isFlash = false){
        $_SESSION[$key] = $isFlash ?
            (isset($_SESSION['flash'][$key]) ? $_SESSION['flash'][$key] : $default)
            : (isset($_SESSION[$key]) ? $_SESSION[$key] : $default);
        return $_SESSION[$key];
    }
}
if (!function_exists('session_remove')){
    function session_remove($key){
        unset($_SESSION[$key]);
    }
}
if (!function_exists('session_set')){
    function session_set($key, $value){
        $_SESSION[$key] = $value;
    }
}
if (!function_exists('flash')){
    function flash($key, $value){
        $_SESSION['flash'][$key] = $value;
    }
}
if (!function_exists('flush_session')){
    function flush_session(){
        unset($_SESSION['flash']);
    }
}