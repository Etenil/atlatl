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
	
	public function __construct()
	{
		$this->parsevars($_SERVER);
	}
	
	/**
	 * Parses server variables into this class.
	 */
	protected function parsevars(array $s)
	{
		$this->method = $this->clean($s['REQUEST_METHOD']);
		$this->host = $this->clean($s['HTTP_HOST']);
		$this->user_agent = $this->clean($s['HTTP_USER_AGENT']);
		$this->accept = explode(',', $this->clean($s['HTTP_ACCEPT']));
		$this->languages = explode(',', $this->clean($s['HTTP_ACCEPT_LANGUAGE']));
		$this->encodings = explode(',', $this->clean($s['HTTP_ACCEPT_ENCODING']));
		$this->connection = $this->clean($s['HTTP_CONNECTION']);
		$this->referer = $this->clean($s['HTTP_REFERER']);
		$this->path = $this->clean($s['PATH']);
		$this->software = $this->clean($s['SERVER_SOFTWARE']);
		$this->name = $this->clean($s['SERVER_NAME']);
		$this->address = $this->clean($s['SERVER_ADDR']);
		$this->port = $this->clean($s['SERVER_PORT']);
		$this->remote_addr = $this->clean($s['REMOTE_ADDR']);
		$this->remote_port = $this->clean($s['REMOTE_PORT']);
		$this->admin = $this->clean($s['SERVER_ADMIN']);
		$this->filename = $this->clean($s['SCRIPT_FILENAME']);
		$this->scriptname = $this->clean($s['SCRIPT_NAME']);
		$this->protocol = $this->clean($s['SERVER_PROTOCOL']);
		$this->uri = $this->clean($s['REQUEST_URI']);
		$this->time = $this->clean($s['REQUEST_TIME']);
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
}

?>
