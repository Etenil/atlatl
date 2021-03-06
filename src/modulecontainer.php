<?php

/**
 * Modules container.
 */

namespace atlatl;

/**
 * This is a module container. It contains enabled modules and can
 * chain-call them as required. It also provides a bit of magic to
 * easily access individual modules.
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
class ModuleContainer
{
    /** Contains instanciated modules that extend Module.*/
    protected $modules;
    protected $server;

    /**
     * Class constructor. Nothing to say here.
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->modules = array();
    }

	/**
	 * A getter to be able to use the modules directly.
     * @param string $name is the requested property name.
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
	 * @param string $module is the module's name to instanciate.
     * @param array $options is an array of options to be passed to
	 * the module's constructor. Default is none.
	 */
    public function addModule($module, array $options = NULL) {
		$this->add_to_list($module, new $module($this->server, $this, $options));
    }

	/**
	 * Adds an instanciated module to the list.
	 * @param string $modulename is the name of the module.
	 * @param Module $module is an instance of a module.
	 */
	public function add_to_list($modulename, Module $module)
	{
		$this->modules[$modulename] = $module;
	}

	/**
	 * Ensures a module is loaded.
	 * @param string $modname is the module's name.
	 * @return boolean TRUE if the module is here, FALSE otherwise.
	 */
	public function isLoaded($modname) {
		return isset($this->modules[$modname]);
	}

	/**
	 * Runs the same method across all modules.
	 * @param string $method_name is the method to be used on all modules.
	 * @param array $params is an array of parameters to pass to all methods.
	 */
	public function runMethod($method_name, array $params = NULL)
	{
        return $this->batchRun(false, $method_name, $params);
	}

    /**
     * Batch runs a method on all modules.
     * @param bool $is_hook specifies that this is a hook call.
	 * @param string $method_name is the method to be used on all modules.
	 * @param array $params is an array of parameters to pass to all methods.
     */
	public function batchRun($is_hook, $method_name, array $params = NULL)
    {
        // Prevents annoying notices.
		if($params == NULL) {
			$params = array();
		}

        // We collect the results into an array.
        $results = array();
        foreach($this->modules as $name => $module) {
            if(method_exists($module, $method_name)) {
                $result = call_user_func_array(array($module, $method_name), $params);
                if($is_hook) { // Hooks are pre-emptive if they return something.
                    if($result) {
                        return $result;
                    }
                } else { // Collecting
                    $results[$name] = $result;
                }
            }
		}

        return $is_hook? false : $results;
    }

	/** Mapped module function call.
     * Method called when the module gets initialised. Put custom code
     * here instead of __construct unless you're sure of what you do.
	public function init()
	{ $this->batchRun(true, '_init'); }

	/** Mapped module function call.
     * Pre-routing hook. This gets called prior to the routing
     * callback.
     * @param string $path is the application path.
     * @param string $route is the route that is being queried.
     * @param Request $request is the request object that will be
     * processed.
     */
	public function preRouting($path, $route, Request $request)
	{ return $this->batchRun(true, 'preRouting', func_get_args()); }

	/** Mapped module function call.
     * Post-routing hook. This gets called after the routing
     * callback.
     * @param string $path is the application path.
     * @param string $route is the route that is being queried.
     * @param Request $request is the request object that will be
     * processed.
     * @param Response $response is the HTTP response produced by the
     * controller.
     */
	public function postRouting($path, $route, Request $request, Response $response)
	{ return $this->batchRun(true, 'postRouting', func_get_args()); }

	/** Mapped module function call.
     * Pre-view hook. Gets called just before processing the
     * view.
     * @param string $path is the requested view's path.
     * @param Request $request is the HTTP Request object currently
     * being handled.
     */
	public function preView(Request $request, $path, $vars)
	{ return $this->batchRun(true, 'preView', func_get_args()); }

	/** Mapped module function call.
     * Post-view hook. Gets called just after having processed the
     * view.
     * @param string $path is the requested view's path.
     * @param array $vars is the HTTP Request object currently
     * being handled.
     * @param string $result response the HTTP Response produced by the
     * view.
     */
	public function postView(Request $request, $path, $vars, $result)
	{ return $this->batchRun(true, 'postView', func_get_args()); }

   	/** Mapped module function call.
     * Pre-model hook. Gets called just before loading a model.
     * @param string $model_name is the requested model's name.
     */
	public function preModel($model_name)
	{ return $this->batchRun(true, 'preModel', func_get_args()); }

	/** Mapped module function call.
     * Post-model hook. Gets called just after having loaded the
     * model.
     * @param string $model_name is the requested model's name.
     */
	public function postModel($model_name)
	{ return $this->batchRun(true, 'postModel', func_get_args()); }
}

?>
