<?php

/**
 * Abstraction of an HTTP request.
 */

namespace atlatl;


/**
 * This is an object-orientated abstraction of an HTTP request.
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
class Request
{
    /** Stores the GET variables. */
	protected $getvars;
    /** Stores the POST variables. */
	protected $postvars;

	/**
	 * Constructor, loads up GET and POST variables.
	 * @param array    $get        $_GET array.
	 * @param array    $post       $_POST array.
	 */
	public function __construct(array $get, array $post)
	{
		$this->getvars = $get;
		$this->postvars = $post;
	}

	/**
	 * Retrieves a GET variable.
	 * @param string    $varname         The variable to fetch.
	 * @param mixed     $default         Default value to return,
	 * FALSE is the default.
	 */
	public function get($varname, $default = false)
	{
		if(isset($this->getvars[$varname])) {
			return $this->getvars[$varname];
		} else {
			return $default;
		}
	}

	/**
	 * Retrieves a POST variable.
	 * @param string    $varname         The variable to fetch.
	 * @param mixed     $default         Default value to return,
	 * FALSE is the default.
	 */
	public function post($varname, $default = false)
	{
		if(isset($this->postvars[$varname])) {
			return $this->postvars[$varname];
		} else {
			return $default;
		}
	}

    /**
     * Retrieves all POST variables.
     */
    public function allPost()
    {
        return $this->postvars;
    }

    /**
     * Retrieves all GET variables.
     */
    public function allGet()
    {
        return $this->getvars;
    }
}

?>
