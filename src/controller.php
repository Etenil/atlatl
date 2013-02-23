<?php

namespace atlatl;

/**
 * Basic controller class for Atlatl.
 *
 * The only thing it does is store the given contructor
 * parameters within the properties.
 */
class Controller
{
    protected $modules;
    protected $server;
    protected $request;
    protected $security;

    function __construct($modules, $server, $request, $security)
    {
        $this->modules = $modules;
        $this->server = $server;
        $this->request = $request;
        $this->security = $security;
    }
}

