<?php

namespace Framework;
class View
{
    public static $templateExtension = '.tpl.php';
    public static $mainContent       = '{{content}}';


    public static function renderBackendView(string $viewName, $data = [])
    {
        return View::make($viewName, $data, true);
    }
    public static function renderFrontendView(string $viewName, $data = [])
    {
        return View::make($viewName, $data, false);
    }

    public static function make(string $viewName, $data = [], $isBackend = true)
    {
        $view_path = self::prepareViewPath($viewName);

        if(file_exists($view_path))
        {
            return self::renderView($view_path, $isBackend, $data);
        }else{
            throw new \Exception("view not found");
        }
    }

    public static function renderView($view, $isBackend = false, $data = [])
    {
       $mainLayout  = self::renderLayout($isBackend, $data);
       $mainContent = self::renderContent(self::prepareViewPath($view), $data);
        flush_session();
        return str_replace(self::$mainContent, $mainContent, $mainLayout);
    }

    public static function renderContent($view, $data)
    {
        extract($data);
        ob_start();
        include_once $view;
        $mainContent = ob_get_contents();
        ob_end_clean();
        return $mainContent;
    }

    public static function renderLayout($isBackend, $data)
    {
        if ($isBackend)
            return self::renderBackendLayout($data);
        else
            return self::renderFrontendLayout($data);
    }
    public static function renderBackendLayout($data)
    {
        extract($data);
        ob_start();
        include_once VIEWS_PATH . DIRECTORY_SEPARATOR . 'Backend' . DIRECTORY_SEPARATOR . 'Layouts' . DIRECTORY_SEPARATOR . 'master' . self::$templateExtension;
        $mainLayout = ob_get_contents();
        ob_end_clean();
        return  $mainLayout;

    }

    public static function renderFrontendLayout($data)
    {
        extract($data);
        ob_start();
        include_once VIEWS_PATH . DIRECTORY_SEPARATOR . 'Frontend' . DIRECTORY_SEPARATOR . 'Layouts' . DIRECTORY_SEPARATOR . 'master' . self::$templateExtension;
        $mainLayout = ob_get_contents();
        ob_end_clean();
        return  $mainLayout;  
    }


    public static function prepareViewPath($view){
      return VIEWS_PATH . DIRECTORY_SEPARATOR .
            str_replace('.', DIRECTORY_SEPARATOR, $view) . self::$templateExtension;

    }

}