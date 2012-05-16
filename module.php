<?php

namespace atlatl;

/**
 * Atlatl module interface.
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
class Module
{
	/**
	 * Default module constructor. Loads options into properties.
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
	
	public function init() {}
	public function preRouting($path, $route, Request $request) {}
	public function postRouting($path, $route, Request $request, Response $response) {}
	public function preView($path, Request $request) {}
	public function postView($path, Request $request, Response $response) {}
}

?>