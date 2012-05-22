<?php

namespace atlatl;

/**
 * Request dispatcher.
 *
 * This is the main class of Atlatl and a routing wrapper around
 * Atlatl. The principle is simple; the request is routed to the
 * correct Atlatl appliction, then the output is processed by the
 * global settings.
 *
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
class ModuleContainer
{
    protected $modules;

    public function __construct()
    {
        $this->modules = array();
    }

	/**
	 * A getter to be able to use the modules directly.
	 */
	public function __get($name) {
		if(isset($this->modules[$name])) {
			return $this->modules[$name];
		} else {
			return false;
		}
	}

	/**
	 * Adds a module to the list.
	 * @param $module is the module's name to instanciate.
	 */
    public function addModule($module, $options = NULL) {
		$this->add_to_list($module, new $module($options));
    }

	/**
	 * Adds an instanciated module to the list.
	 * @param $modulename is the name of the module.
	 * @param $module is an instance of a module.
	 */
	public function add_to_list($modulename, Module $module)
	{
		$this->modules[$modulename] = $module;
	}

	/**
	 * Ensures a module is loaded.
	 * @param $modname is the module's name.
	 * @return TRUE if the module is here, FALSE otherwise.
	 */
	public function isLoaded($modname) {
		return isset($this->modules[$modname]);
	}

	/**
	 * Runs the same method across all modules.
	 * @param $method_name is the method to be used on all modules.
	 * @param $params is an array of parameters to pass to all methods.
	 */
	public function runMethod($method_name, array $params = NULL)
	{
		if($params == NULL) {
			$params = array();
		}

        foreach($this->modules as $module) {
			call_user_func_array(array($module, $method_name), $params);
		}
	}

	// Mapped standard function calls
	public function init()
	{ $this->runMethod('init'); }

	public function preRouting($path, $route, Request $request)
	{ $this->runMethod('preRouting', func_get_args()); }

	public function postRouting($path, $route, Request $request, Response $response)
	{ $this->runMethod('postRouting', func_get_args()); }

	public function preView($path, Request $request)
	{ $this->runMethod('preView', func_get_args()); }

	public function postView($path, Request $request, Response $response)
	{ $this->runMethod('postView', func_get_args()); }
}

?>