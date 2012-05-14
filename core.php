<?php


namespace Movicon;

require_once('server.php');
require_once('request.php');
require_once('response.php');
require_once('controller.php');

/**
 * Core routing functionality.
 *
 * HTTP requests are routed from this class and the Movicon core jump
 * started from here.
 *
 * This file is part of Movicon.
 *
 * Movicon is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * Movicon is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Movicon.  If not, see <http://www.gnu.org/licenses/>.
 */
class Core {
    // URL prefix.
    protected $prefix = "";
	protected $server;
	protected $request;

    /**
     * Class constructor.
     * @param string $prefix is the URL prefix to use for this application.
     */
    public function __construct($prefix = "", Server $server = null, Request $request = null)
    {
        $this->setPrefix($prefix);

		if($server) {
			$this->server = $server;
		} else {
			$this->server = new Server($_SERVER);
		}
		
		if($request) {
			$this->resquet = $request;
		} else {
			$this->request = new Request($_GET, $_POST);
		}
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
	 * Serves the requests. Call this on the top-level application only.
	 */
	function serve(array $urls)
	{
		$this->route($urls)->compile();
	}
	
    /**
     * Does the actual URL routing.
     *
     * The main method of the Core class.
     *
     * @param   array    	$urls  	    The regex-based url to class mapping
     * @throws  Exception               Thrown if corresponding class is not found
     * @throws  Exception               Thrown if no match is found
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
        $method_urls = preg_grep('%^' . $this->server->getMethod() . ':%', $urls);
        foreach($method_urls as $regex => $proto) {
            if(preg_match('%^'. $this->prefix . $regex .'/?$%i',
                          $this->server->getMethod() . ':' . $path, $matches)) {
                $call = $proto;
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
            throw new \Exception("URL, $path, not found.");
        }

        list($class, $method) = explode('::', $call);

        if(class_exists($class)) {
            $obj = new $class($this->server, $this->request);
            if(method_exists($obj, $method)) {
                $response = call_user_func_array(array($obj, $method),
												 array_slice($matches, 1));
				if(gettype($response) == 'string') {
					$response = new Response($response);
				}
				else if($response === null) {
					$response = new Response();
				}
				else if(gettype($response) != 'object'
						|| (gettype($response) == 'object'
							&& get_class($response) != 'Movicon\Response')) {
					throw new \Exception('Unknown response.');
				}
				return $response;
            } else {
                throw new \BadMethodCallException("Method, $method, not supported.");
            }
        } else {
            throw new \Exception("Class, $class, not found.");
        }
    }
}
