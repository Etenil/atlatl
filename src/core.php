<?php

/**
 * Core routing functionality.
 */

namespace atlatl;

/**
 * HTTP requests are routed from this class and the Atlatl core jump
 * started from here.
 *
 * @copyright
 * This file is part of Atlatl.
 *
 * Atlatl is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * Atlatl is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Atlatl.  If not, see <http://www.gnu.org/licenses/>.
 */
class Core {
    /** Server object. */
	protected $server;
    /** Request being handled. */
	protected $request;
    /** Container of modules. */
	protected $modules;

    /** Callable variable that handles client errors. */
    protected $error40x;
    /** Callable variable that handles server errors. */
    protected $error50x;

    /**
     * Class constructor.
     * @param string $prefix is the URL prefix to use for this application.
     * @param Server $server is a Server object. Default abstracts $_SERVER.
     * @param Request $request is the HTTP Request to handle. Default
     * is generated from superglobals.
     */
    public function __construct($prefix = "", Server $server = null, Request $request = null, ModuleContainer $mc = null)
    {
        if($server) {
            $this->server = $server;
        } else { // Backwards-compatibility
            $this->server = Injector::give('Server', $_SERVER);
        }

        // Just some syntactic sugar to avoid initialising the Server object.
        if($prefix) {
            $this->server->setPrefix($prefix);
        }

        if($request) {
            $this->request = $request;
        } else { // Backwards-compatibility
            $this->request = Injector::give('Request', $_GET, $_POST);
        }

        $this->register40x(function(\Exception $e) {
                return Injector::give('Response', '404 Error - Page not found.', 404);
            });

        $this->register50x(function(\Exception $e) {
                return Injector::give('Response', '500 Error - Server error.', 500);
            });

        if($mc) {
            $this->modules = $mc;
        } else { // Backwards-compatibility again...
            $this->modules = Injector::give('ModuleContainer', $this->server);
        }
    }

    /**
     * Sets a new handler for 404 errors.
     * @param callable $handler will be called in the event of a 404
     * error. This callable must accept one Exception parameter.
     */
    public function register40x($handler)
    {
        $this->error40x = $handler;
    }

    /**
     * Sets a new handler for 50x errors.
     * @param callable $handler will be called in the event of a 500
     * error. This callable must accept one Exception parameter.
     */
    public function register50x($handler)
    {
        $this->error50x = $handler;
    }

    /**
     * Wrapper that converts PHP errors to exceptions and passes them
     * to the standard error50x handler.
     */
    public function php_error_handler($errno, $errstr, $errfile, $errline)
    {
        $e = new \Exception($errstr, $errno);
        call_user_func($this->error50x, $e);
    }

    /**
     * Handler for PHP fatal errors.
     */
    public function php_fatal_error_handler()
    {
        $error = error_get_last();
        if($error !== NULL) {
            $e = new \Exception($error['message'], $error['type']);
            call_user_func($this->error50x, $e);
        }
    }

    /**
     * Instanciates a new module and adds it to the collection.
     * @param string $module is the module's name.
     * @param array $options is an array of options passed to the
     * module's constructor.
     */
	public function loadModule($module, $options = NULL)
	{
		$this->modules->addModule($module, $options);
	}

    /**
     * Replaces the current modules container by the provided one.
     * @param ModuleContainer $container is a container of modules.
     */
	public function setModules(ModuleContainer $container)
	{
		$this->modules = $container;
	}

    /**
     * Changes the URL prefix to work from.
     * @param string $prefix is the URL prefix to use, for instance "/glue".
     * @return this object (you can make a call chain).
     */
    public function setPrefix($prefix)
    {
        // We ensure that the prefix is properly formatted. It
        // must start with a '/' and end without one.
        if($prefix != "") {
            if($prefix[0] != '/') {
                $prefix = '/' . $prefix;
            }
            if($prefix[strlen($prefix) - 1] == '/') {
                $prefix = substr($prefix, 0, strlen($prefix) - 1);
            }
        }

        $this->prefix = $prefix;
        return $this;
    }

	/**
	 * Serves the requests.
     * @param array $urls is an associative arrays of regexes and
     * callbacks for routing.
	 */
	function serve(array $urls)
	{
        $response = null;

        // Registering PHP error handlers.
        set_error_handler(array($this, 'php_error_handler'), E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR);
        register_shutdown_function(array($this, 'php_fatal_error_handler'));

        try {
            $response = $this->route($urls);
        }
        catch(HttpRedirect $r) {
            $response = Injector::give('Response');
            $response->setHeader('Location', $r->getUrl());
        }
        catch(HTTPClientError $e) {
            $response = call_user_func($this->error40x, $e);
        }
        catch(HTTPServerError $e) {
            $response = call_user_func($this->error50x, $e);
        }
        // Generic HTTP status response.
        catch(HTTPStatus $s) {
            $response = Injector::give('Response', $s->getMessage(), $s->getCode());
        }
        // Generic error.
        catch(\Exception $e) {
            $response = call_user_func($this->error50x, $e);
        }

        if(is_object($response)) {
            $response->compile();
        } else {
            echo $response;
        }
	}

    /**
     * Does the actual URL routing.
     *
     * The main method of the Core class.
     *
     * @param   array    	$urls  	    The regex-based url to class mapping
     * @throws  NoHandlerException      Thrown if corresponding class is not found
     * @throws  NoRouteException        Thrown if no match is found
     * @throws  BadMethodCallException  Thrown if a corresponding GET,POST is not found
     *
     */
    function route(array $urls) {
        $path = $this->server->getRoute();

		// We revert-sort the keys to match more specific routes first.
        krsort($urls);

        $call = false;        // This will store the controller and method to call
        $matches = array();   // And this the extracted parameters.

        // First we search for specific method routes.
        $method_routes = preg_grep('/^' . $this->server->getMethod() . ':/i', array_keys($urls));
        foreach($method_routes as $route) {
            $method = $this->server->getMethod() . ':';
            $clean_route = substr($route, strlen($method));
            if(preg_match('%^'. $clean_route .'/?$%i',
                          $this->server->getRoute(), $matches)) {
                $call = $urls[$route];
				break;
            }
        }

        // Do we need to try generic routes?
        if(!$call) {
            foreach($urls as $regex => $proto) {
                if(preg_match('%^'. $regex .'/?$%i',
                              $this->server->getRoute(), $matches)) {
                    $call = $proto;
					break;
                }
            }
        }


        // If we don't have a call at this point, that's a 404.
        if(!$call) {
            throw new NoRouteException("URL, ".$this->server->getWholeRoute().", not found.");
        }

        /* We're accepting different types of handler declarations. It can be
         * anything PHP defines as a 'callable', or in the form class::method. */
        $class = '';
        $method = '';


        if(is_string($call) && preg_match('/^.+::.+$/', trim($call))) {
            list($class, $method) = explode('::', $call);
        }
        else if(is_array($call)) {
            $class = $call[0];
            $method = $call[1];
        }
        else if(is_callable($call)) {
            $method = $call;
        }

        $response = null;

        if(!$class) { // Just a function call (or a closure?). Less hooks obviously.
            // Mounting system stuff into an object and generating the parameters.
            $params = array_merge(array((object)array(
                              'modules' => $this->modules,
                              'server'  => $this->server,
                              'request' => $this->request,
                              'sec'     => Injector::give('Security'))),
                      array_slice($matches, 1));
            $response = call_user_func_array($method, $params);
        }
        else if(class_exists($class)) {
            $obj = new $class($this->modules, $this->server,
                              $this->request, new Security());

            if(method_exists($obj, 'preRequest'))
                $obj->preRequest();

            if(method_exists($obj, $method)) {
                $response = call_user_func_array(array($obj, $method),
												 array_slice($matches, 1));
                if(method_exists($obj, 'postRequest'))
                    $response = $obj->postRequest($response);
            } else {
                throw new \BadMethodCallException("Method, $method, not supported.");
            }
        } else {
            throw new NoHandlerException("Class, $class, not found.");
        }

        // Cleaning up the response...
        if(gettype($response) == 'string') {
            $response = Injector::give('Response', $response);
        }
        else if($response === null) {
            $response = Injector::give('Response');
        }
        else if(gettype($response) != 'object'
                || (gettype($response) == 'object'
                    && (get_class($response) != 'atlatl\Response'
                        && !is_subclass_of($response, 'atlatl\Response')))) {
            throw new IllegalResponseException('Unknown response.');
        }

        return $response;
    }
}
