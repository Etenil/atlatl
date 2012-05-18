<?php

namespace atlatl;

require_once('security.php');

/**
 * Basic implementation of a Controller.
 *
 * Controllers are used to process incoming events. This is the basic
 * implementation that all controllers should extend. Some helper functions
 * are provided.
 *
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
	protected $modules;
	protected $server;
	protected $request;
	protected $sec;

	public function __construct(ModuleContainer $modules, Server $server, Request $request)
	{
		$this->modules = $modules;
		$this->server = $server;
		$this->request = $request;
		$this->sec = new Security();

        // Running the user init.
        $this->init();
	}

    protected function _init()
    {
    }

	protected function dump($var, $no_html = false)
	{
		$dump = var_export($var, true);
		if($no_html) {
			return $dump;
		} else {
			return '<pre>' . htmlentities($dump) . '</pre>' . PHP_EOL;;
		}
	}
}

?>
