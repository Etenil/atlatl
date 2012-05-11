<?php

namespace Movicon;

/**
 * Server abstraction class.
 *
 * Parses the $_SERVER variable and abstracts/escapes elements.
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
class Server
{
	protected $method;
	protected $host;
	protected $user_agent;
	protected $accept;
	protected $languages;
	protected $encodings;
	protected $connection;
	protected $referer;
	protected $path;
	protected $software;
	protected $name;
	protected $address;
	protected $port;
	protected $remote_addr;
	protected $remote_port;
	protected $admin;
	protected $filename;
	protected $scriptname;
	protected $protocol;
	protected $uri;
	protected $time;
	protected $route;

	public function __construct(array $server)
	{
		$this->parsevars($server);
	}

	/**
	 * Parses server variables into this class.
	 */
	protected function parsevars(array $s)
	{
		$this->method      = $this->arrayGet('REQUEST_METHOD', $s);
		$this->host        = $this->arrayGet('HTTP_HOST', $s);
		$this->user_agent  = $this->arrayGet('HTTP_USER_AGENT', $s);
		$this->accept      = explode(',', $this->arrayGet('HTTP_ACCEPT', $s));
		$this->languages   = explode(',', $this->arrayGet('HTTP_ACCEPT_LANGUAGE', $s));
		$this->encodings   = explode(',', $this->arrayGet('HTTP_ACCEPT_ENCODING', $s));
		$this->connection  = $this->arrayGet('HTTP_CONNECTION', $s);
		$this->referer     = $this->arrayGet('HTTP_REFERER', $s);
		$this->path        = $this->arrayGet('PATH', $s);
		$this->software    = $this->arrayGet('SERVER_SOFTWARE', $s);
		$this->name        = $this->arrayGet('SERVER_NAME', $s);
		$this->address     = $this->arrayGet('SERVER_ADDR', $s);
		$this->port        = $this->arrayGet('SERVER_PORT', $s);
		$this->remote_addr = $this->arrayGet('REMOTE_ADDR', $s);
		$this->remote_port = $this->arrayGet('REMOTE_PORT', $s);
		$this->admin       = $this->arrayGet('SERVER_ADMIN', $s);
		$this->filename    = $this->arrayGet('SCRIPT_FILENAME', $s);
		$this->scriptname  = $this->arrayGet('SCRIPT_NAME', $s);
		$this->protocol    = $this->arrayGet('SERVER_PROTOCOL', $s);
		$this->uri         = $this->arrayGet('REQUEST_URI', $s);
		$this->time        = $this->arrayGet('REQUEST_TIME', $s);

		// Stripping GET and anchor from the URI.
		$this->route = $this->uri;
		$get_id = strpos($this->route, '?');
		if($get_id !== false) {
			$this->route = substr($this->route, 0, $get_id);
		}
		$anchor_id = strpos($this->route, '#');
		if($anchor_id !== false) {
			$this->route = substr($this->route, 0, $anchor_id);
		}
	}

    /**
     * Gets, checks and cleans an array entry. Avoids warnings and
     * simplifies use.
     */
    protected function arrayGet($key, array $array)
    {
        if(isset($array[$key])) {
            return $this->clean($array[$key]);
        } else {
            return false;
        }
    }

	/**
	 * Cleans up server entries.
	 */
	protected function clean($serverstring)
	{
		// Cleaning the `...;q=x.x' and in general anything after ';'.
		$scol_id = strpos($serverstring, ';');
		if($scol_id !== false) {
			return substr($serverstring, 0, $scol_id);
		} else {
			return $serverstring;
		}
	}

// Accessors
	public function getMethod()
		{ return $this->method; }
	public function getHost()
		{ return $this->host; }
	public function getUserAgent()
		{ return $this->user_agent; }
	public function getAccept()
		{ return $this->accept; }
	public function getLanguages()
		{ return $this->languages; }
	public function getEncodings()
		{ return $this->encodings; }
	public function getConnection()
		{ return $this->connection; }
	public function getReferer()
		{ return $this->referer; }
	public function getPath()
		{ return $this->path; }
	public function getSoftware()
		{ return $this->software; }
	public function getName()
		{ return $this->name; }
	public function getAddress()
		{ return $this->address; }
	public function getPort()
		{ return $this->port; }
	public function getRemoteAddr()
		{ return $this->remote_addr; }
	public function getRemotePort()
		{ return $this->remote_port; }
	public function getAdmin()
		{ return $this->admin; }
	public function getFilename()
		{ return $this->filename; }
	public function getScriptname()
		{ return $this->scriptname; }
	public function getProtocol()
		{ return $this->protocol; }
	public function getUri()
		{ return $this->uri; }
	public function getTime()
		{ return $this->time; }
	public function getRoute()
	    { return $this->route; }
}

?>
