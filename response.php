<?php

namespace atlatl;

/**
 * Abstraction of an HTTP response.
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
class Response
{
	protected $headers;
	protected $body;

	public function __construct($body = '')
	{
		$this->headers = array();
		$this->body = $body;
	}
	
	/**
	 * Sets the contents of the HTTP response's body.
	 */
	public function setBody($data)
	{
		$this->body = $data;
	}

	/**
	 * Appends a string to body.
	 */
	public function append($section)
	{
		$this->body .= $section;
	}

	/**
	 * Sets a header value.
	 */
	public function setHeader($name, $value)
	{
		$this->headers[$name] = $value;
	}

	/**
	 * Generates the page.
	 */
	public function compile()
	{
		foreach($this->headers as $hdrkey => $hdrval) {
			header($hdrkey, $hdrval);
		}
		echo $this->body;
	}
}

?>
