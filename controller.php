<?php

namespace Movicon;

require('security.php');

/**
 * Basic implementation of a Controller.
 *
 * Controllers are used to process incoming events. This is the basic
 * implementation that all controllers should extend. Some helper functions
 * are provided.
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
class Controller
{
	protected $server;
	protected $sec;

	public function __construct(Server $server)
	{
		$this->server = $server;
		$this->sec = new Security();
	}
}

?>