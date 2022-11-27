<?php


namespace Framework;

use Error;

class Router
{

    public static $routes            = [];
    public $urlParts                 = [];
    public Request $request;

    const NOT_FOUND_CONTROLLER       = "NotFoundController";
    const NOT_FOUND_ACTION           = "index";
    const CONTROLLERS_NAMESPACE      = "\Controllers\\";

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public static function get($link, $controller)
    {
        self::$routes['get'][trim($link, '/')] = $controller;
    }

    public static function post($link, $controller)
    {
        self::$routes['post'][trim($link, '/')] = $controller;
    }


    // here you dont need to register all routes in file - finally we know the controller , action and params directly from url
    public function requestHandler(){
        // determine where to go
        $this->urlParts = $this->request->parseUrl();
        $controller     = ucwords(strtolower($this->urlParts['controller'])) . 'Controller';
        $action         = $this->urlParts['action'];
        $params         = $this->urlParts['params'];

        // check if  controller exist or not

        $controller_path    = CONTROLLERS_PATH . DIRECTORY_SEPARATOR . $controller . '.php';
        $notFoundController = self::CONTROLLERS_NAMESPACE . self::NOT_FOUND_CONTROLLER;
        $controllerInstance = new $notFoundController;


        if (file_exists($controller_path)){
            $fullClassNameWithNamespace = self::CONTROLLERS_NAMESPACE . $controller;
            $controllerInstance         = new $fullClassNameWithNamespace;
        }



        // then check if action exist or not
        if (!method_exists($controllerInstance, $action)){
            $action = self::NOT_FOUND_ACTION;
            $params = [];
        }

        return call_user_func_array([$controllerInstance, $action], $params);
    }

    public function requestHandler2(){
        // determine where to go
        //=> www.website.com/posts/show/1   posts/show/{post_id}/last


        $routes = Router::$routes[$this->request->getMethod()];
        if (in_array($this->request->getUrlWithoutQuery(), array_keys($routes))) {
            //start separate controller and actions
           
            $params = $this->request->getParams();
            $callback = $routes[$this->request->getUrlWithoutQuery()];

         try {
            if (is_callable($callback)) {
                return call_user_func_array($callback, $params);
            } else if (is_string($callback)) {
                $controllerParts = explode('@', $callback, 2);
                $fullControllerWithNamespace = self::CONTROLLERS_NAMESPACE . $controllerParts[0];
                $controllerInstance = new $fullControllerWithNamespace;
                call_user_func_array([$controllerInstance, $controllerParts[1]], $params);
            } else {
                return "No Callback Functoin Or Controller For This Route " . $this->request->getUrlWithoutQuery();
                exit;
            }
         } catch (Error $exception) {
            return $exception->getMessage();
         }

        }else{
          //notFoundController
          $notFoundController = self::CONTROLLERS_NAMESPACE . self::NOT_FOUND_CONTROLLER;
          $notFoundControllerInstance = new $notFoundController;
          call_user_func([$notFoundControllerInstance, self::NOT_FOUND_ACTION]);
        }
    }
}