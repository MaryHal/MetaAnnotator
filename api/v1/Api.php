<?php

namespace meta;

abstract class Api
{
    protected $args = array();
    protected $endpoint = '';

    public function __construct($request)
    {
        // This is going to be a little bit ugly since I'm not running Apache
        // locally, no mod_rewrite for nice URIs

        $this->args = $_GET;

        if (!isset($this->args['endpoint']))
        {
            die('no endpoint given');
        }

        $this->endpoint = $this->args['endpoint'];
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
    }

    public function process()
    {
        if (method_exists($this, $this->endpoint))
        {
            return $this->sendResponse($this->{$this->endpoint}());
        }

        return $this->sendResponse("Invalid Endpoint: {$this->endpoint}", 404);
    }

    private function sendResponse($data, $status = 200)
    {
        header('Content-type: application/json');
        header("HTTP/1.1" . $status . " " . $status);

        if (isset($this->args['pp']))
        {
            return json_encode($data, JSON_PRETTY_PRINT);
        }

        return json_encode($data);
    }

    public function printStuff()
    {
        echo $this->endpoint, ', ', $this->requestMethod;
    }
}

?>