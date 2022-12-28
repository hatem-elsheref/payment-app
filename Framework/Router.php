<?php


namespace Framework;

use function Couchbase\defaultDecoder;
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
                return call_user_func_array([$controllerInstance, $controllerParts[1]], $params);
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
          return call_user_func([$notFoundControllerInstance, self::NOT_FOUND_ACTION]);
        }
    }

    public function requestHandler3(){
        // determine where to go
        //=> www.website.com/posts/{post_id}  www.website.com/posts/101  
        $routes = Router::$routes[$this->request->getMethod()];
        $linkParts = explode('/', $this->request->getUrlWithoutQuery());
        $matchedRoute = $this->getMatchedRouteFromUrl(array_keys($routes), $linkParts);

        if (!is_null($matchedRoute)){
            $paramsNames  = $this->getRouteParams($matchedRoute); // like {post_id} and others
            $paramsValues = [];
            foreach ($paramsNames as $index => $value){
//                $value = str_replace('{', '', $value); // remove {
//                $value = str_replace('}', '', $value); // remove }
                $value = preg_replace(['/{/', '/}/'], '', $value);
                $paramsValues[$value] = $linkParts[$index];
            }

//            $paramsValues = array_merge($paramsValues, $this->request->getParams());
            //start call controller
            
            $callback = $routes[$matchedRoute];
            try {
                if (is_callable($callback)) {
                    return call_user_func_array($callback, $paramsValues);
                } else if (is_string($callback)) {
                    $controllerParts = explode('@', $callback, 2);
                    $fullControllerWithNamespace = self::CONTROLLERS_NAMESPACE . $controllerParts[0];
                    $controllerInstance = new $fullControllerWithNamespace;
                    return call_user_func_array([$controllerInstance, $controllerParts[1]], $paramsValues);
                } else {
                    return "No Callback Functoin Or Controller For This Route " . $this->request->getUrlWithoutQuery();
                }
             } catch (Error $exception) {
                dd($exception);
                return $exception->getMessage();
             }

        }else{
            $notFoundController = self::CONTROLLERS_NAMESPACE . self::NOT_FOUND_CONTROLLER;
            $notFoundControllerInstance = new $notFoundController;
            return call_user_func([$notFoundControllerInstance, self::NOT_FOUND_ACTION]);
        }
    }

    
    private function getMatchedRouteFromUrl($routesList, $linkParts)
    {
        $is_matched = false;
        $matchedRoute = null;
        foreach ($routesList as $route){
            $route_parts = explode('/', $route);
            // if route register in routes var like this /posts/{post_id} then the hit must be the same like /posts/101
            if(count($route_parts) == count($linkParts)){
                //like /posts/{id}/showAll -> return [0 => 'posts', 2 => 'showAll']
                $static_route = array_diff($route_parts, $this->getRouteParams($route));
                // confirm the static words in registered route like the static words in the hit link
                //var_dump($static_route, $this->getRouteParams($route));
                foreach ($static_route as $index => $item){
                    if ($item != $linkParts[$index]){
                     // if one is different go out => this is not the matched route
                        $is_matched = false;
                        break;
                    }else{
                        $is_matched = true;
                    }
                }
                if ($is_matched){
                    $matchedRoute  = $route;
                    break;
                }
            }
        }

        return $matchedRoute;
    }

    private function getRouteParams($route)
    {
        $matches = [];
        $parts = explode('/', $route);
        foreach ($parts as $index => $part){
            preg_match("/{[a-zA-Z_]+[a-zA-Z0-9_]*}/", $part) ? $matches[$index] = $part : null;
        }
        return $matches;
    }
}