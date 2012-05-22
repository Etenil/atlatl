<?php

/**
 * Atlatl basic module implementation.
 */

namespace atlatl;

/**
 * This is a basic blank module for Atlatl. It needs to be extended in
 * order to write new modules.
 *
 * @copyright
 * This file is part of Atlatl
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
class Module
{
	/**
	 * Default module constructor. Loads options into properties.
     * @param array $options is an associative array whose keys will
     * be mapped to properties for speed populating of the object.
	 */
	public function __construct($options = NULL)
	{
		if(is_array($options)) {
			foreach($options as $opt_name => $opt_val) {
				if(property_exists($this, $opt_name)) {
					$this->$opt_name = $opt_val;
				}
			}
		}
	}

    /**
     * Method called when the module gets initialised. Put custom code
     * here instead of __construct unless you're sure of what you do.
     */
	public function init() {}

    /**
     * Pre-routing hook. This gets called prior to the routing
     * callback.
     * @param string $path is the application path.
     * @param string $route is the route that is being queried.
     * @param Request $request is the request object that will be
     * processed.
     */
	public function preRouting($path, $route, Request $request) {}

    /**
     * Post-routing hook. This gets called after the routing
     * callback.
     * @param string $path is the application path.
     * @param string $route is the route that is being queried.
     * @param Request $request is the request object that will be
     * processed.
     * @param Response $response is the HTTP response produced by the
     * controller.
     */
	public function postRouting($path, $route, Request $request, Response $response) {}

    /**
     * Pre-view hook. Gets called just before processing the
     * view.
     * @param string $path is the requested view's path.
     * @param Request $request is the HTTP Request object currently
     * being handled.
     */
	public function preView($path, Request $request) {}

    /**
     * Post-view hook. Gets called just after having processed the
     * view.
     * @param string $path is the requested view's path.
     * @param Request $request is the HTTP Request object currently
     * being handled.
     * @param Response response is the HTTP Response produced by the
     * view.
     */
	public function postView($path, Request $request, Response $response) {}
}

?>