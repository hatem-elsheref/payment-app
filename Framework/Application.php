<?php

namespace Framework;

class Application
{

    public Database $database;
    public Router   $router;
    public Request  $request;
    public $configurations;
    public static   $app;


    public function __construct(array $configurations)
    {
        $this->configurations = $configurations;
        $this->database = new Database($configurations['database']);
        $this->request  = new Request($_SERVER);
        $this->router   = new Router($this->request);

        self::$app = $this;
    }

    public function start()
    {
        // handle route here
        return $this->router->requestHandler3();
    }
}
