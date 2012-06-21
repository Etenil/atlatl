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
    /** URL prefix. */
    protected $prefix = "";
    /** Server object. */
	protected $server;
    /** Request being handled. */
	protected $request;
    /** Container of modules. */
	protected $modules;

    /** Callable variable that handles 404 errors. */
    protected $error404;
    /** Callable variable that handles 500 errors. */
    protected $error500;

    /**
     * Class constructor.
     * @param string $prefix is the URL prefix to use for this application.
     * @param Server $server is a Server object. Default abstracts $_SERVER.
     * @param Request $request is the HTTP Request to handle. Default
     * is generated from superglobals.
     */
    public function __construct($prefix = "", Server $server = null, Request $request = null)
    {
		if($server) {
			$this->server = $server;
		} else {
			$this->server = new Server($_SERVER, $prefix);
		}

        if(!$prefix) {
            $this->setPrefix($server->getPrefix());
        } else {
            $this->setPrefix($prefix);
            $this->server->setPrefix($prefix);
        }

		if($request) {
			$this->resquet = $request;
		} else {
			$this->request = new Request($_GET, $_POST, $_COOKIE);
		}

        $this->register404(function(\Exception $e) {
                return new Response('404 Error - Page not found.', 404);
            });

        $this->register500(function(\Exception $e) {
                return new Response('500 Error - Server error.', 500);
            });

		$this->modules = new ModuleContainer($this->server);
    }

    /**
     * Sets a new handler for 404 errors.
     * @param callable $handler will be called in the event of a 404
     * error. This callable must accept one Exception parameter.
     */
    public function register404($handler)
    {
        $this->error404 = $handler;
    }

    /**
     * Sets a new handler for 500 errors.
     * @param callable $handler will be called in the event of a 500
     * error. This callable must accept one Exception parameter.
     */
    public function register500($handler)
    {
        $this->error500 = $handler;
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

        try {
            $response = $this->route($urls);
        }
        catch(HttpRedirect $r) {
            $response = new Response();
            $response->setHeader('Location', $r->getUrl());
        }
        catch(NoRouteException $e) {
            $response = call_user_func($this->error404, $e);
        }
        catch(NoViewException $e) {
            $response = call_user_func($this->error404, $e);
        }
        catch(\Exception $e) {
            $response = call_user_func($this->error500, $e);
        }

        $response->compile();
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

        if($path == $this->prefix) {
            $path.= '/'; // This is necessary to match '/' with a prefix.
        }

		// We revert-sort the keys to match more specific routes first.
        krsort($urls);

        $call = false;        // This will store the controller and method to call
        $matches = array();   // And this the extracted parameters.

        // First we search for specific method routes.
        $method_routes = preg_grep('/^' . $this->server->getMethod() . ':/i', array_keys($urls));
        foreach($method_routes as $route) {
            $method = $this->server->getMethod() . ':';
            $clean_route = substr($route, strlen($method));
            if(preg_match('%^'. $this->prefix . $clean_route .'/?$%i',
                          $path, $matches)) {
                $call = $urls[$route];
				break;
            }
        }

        // Do we need to try generic routes?
        if(!$call) {
            foreach($urls as $regex => $proto) {
                if(preg_match('%^'. $this->prefix . $regex .'/?$%i',
                              $path, $matches)) {
                    $call = $proto;
					break;
                }
            }
        }


        // If we don't have a call at this point, that's a 404.
        if(!$call) {
            throw new NoRouteException("URL, $path, not found.");
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
            $params = array_merge(array((object)array('modules' => $this->modules,
                                                      'server'  => $this->server,
                                                      'request' => $this->request,
                                                      'sec'     => new Security())),
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
            $response = new Response($response);
        }
        else if($response === null) {
            $response = new Response();
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
