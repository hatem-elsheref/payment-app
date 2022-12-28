<?php

namespace Framework;

class Request
{
    private $request;
    private $extractedUrl = [];
    private $isHome       = true;
   
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function getMethod()
    {
       return strtolower($this->request['REQUEST_METHOD']);
    }


    public function getUrl()
    {
        return trim($this->request['REQUEST_URI'], '/');
    }

    public function getUrlWithoutQuery()
    {
        return trim(isset($this->request['PATH_INFO']) ? $this->request['PATH_INFO'] : "", "/");
    }

    private function getQueryString()
    {

        return $_GET;
        // or
        $params = $this->request['QUERY_STRING'] ?? NULL;

        if(!isset($params)) return [];

        $extractedParams = [];

        $params= explode('&', $params);

        foreach($params as $param){
            $parts = explode("=", $param);
            $extractedParams[$parts[0]] = $parts[1] ?? NULL;
        }
        
        return $extractedParams;
    }

    public function getParams()
    {
       return $this->getQueryString();
    }

    public function isGet()
    {
        return $this->getMethod() == 'get';
    }

    public function isPost()
    {
        return $this->getMethod() == 'post';
    }

    public function inHome()
    {
        return $this->isHome;
    }

    public function body($input = null)
    {
        if (!is_null($input) and is_string($input))
            return $_REQUEST[$input];
        else
            return $_REQUEST;
    }
    public function parseUrl()
    {
        // separate url [controllers - actions - params]

        $controller = 'Home';
        $action     = 'index';
        $params     = [];

        $parts = explode('/', $this->getUrl(), 3);

        if (count($parts) == 1 && empty($parts[0])){
            $this->isHome = true;
        }else{
            $this->isHome = false;
            $controller = $parts[0];
            $action     = isset($parts[1]) ? $parts[1] : 'index';
            $params     = isset($parts[2]) ? explode('/', $parts[2]) : [];
        }

        $this->extractedUrl['controller'] = $controller;
        $this->extractedUrl['action']     = $action;
        $this->extractedUrl['params']     = $params;

        return $this->extractedUrl;
    }

    public function baseUrl()
    {
        return APP_URL;
    }
    public function appUrl()
    {
        return $this->baseUrl() . "/" . trim($this->request['REQUEST_URI'], "/");
    }

}