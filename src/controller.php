<?php

/**
 * Basic implementation of a controller.
 */

namespace atlatl;

/**
 * Controllers are used to process incoming events. This is the basic
 * implementation that all controllers should extend. Some helper functions
 * are provided.
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
class Controller
{
    /** Object that contains loaded modules. */
	protected $modules;
    /** Server state variable. */
	protected $server;
    /** Current request object. */
	protected $request;
    /** Security provider. */
	protected $sec;

    /**
     * Controller's constructor. This is meant to be called by Core.
     * @param ModuleContainer $modules is a container of loaded modules.
     * @param Server $server is the current server state.
     * @param Request $request is the current request object.
     */
	public function __construct(ModuleContainer $modules, Server $server, Request $request)
	{
		$this->modules = $modules;
		$this->server = $server;
		$this->request = $request;
		$this->sec = new Security();

        // Running the user init.
        $this->_init();
	}

    /**
     * This is run after the constructor. Implement to have custom code run.
     */
    protected function _init()
    {
    }

    /**
     * Tiny wrapper arround var_dump to ease debugging.
     * @param mixed $var is the variable to be dumped
     * @param boolean $no_html defines whether the variable contains
     * messy HTML characters or not. The given $var will be escaped if
     * set to false. Default is false.
     * @return The HTML code of a human representation of the $var.
     */
	protected function dump($var, $no_html = false)
	{
		$dump = var_export($var, true);
		if($no_html) {
			return $dump;
		} else {
			return '<pre>' . htmlentities($dump) . '</pre>' . PHP_EOL;;
		}
	}

	/**
	 * Method executed prior to any request handling.
	 */
	public function preRequest()
	{
	}

	/**
	 * Method executed following any request handling. This method is
	 * expected to return a Response object, which will then be sent
	 * back to the user.
	 * @param mixed $returned is the value that was previously returned
	 * by the routed method.
	 */
	public function postRequest($returned)
	{
		return $returned;
	}
}

?>
