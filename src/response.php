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
	protected $status_code;
	protected $body;
	protected $content_type;

    protected $sessionvars;
    protected $cookievars;
    protected $start_session;

	public function __construct($body = '', $status_code = 200,
								$content_type = 'text/html; charset=UTF-8',
                                array $cookies = null, array $session = null,
                                $start_session = false)
	{
		$this->status_code = $status_code;
		$this->content_type = $content_type;
		$this->headers = array();
		$this->body = $body;

        $this->start_session = false;
        if($session) {
            $this->sessionvars = $session;
        }
        else if($start_session) {
            $this->sessionvars = $_SESSION;
        }

        if($cookies) {
            $this->cookievars = $cookies;
        } else {
            $this->cookievars = $_COOKIE;
        }

        if($start_session) {
            $this->startSession();
        }
	}

	/**
	 * Sets the contents of the HTTP response's body.
	 */
	public function setBody($data)
	{
		$this->body = $data;
		return $this;
	}

	public function getBody()
	{
		return $this->body;
	}

	/**
	 * Appends a string to body.
	 */
	public function append($section)
	{
		$this->body .= $section;
		return $this;
	}

	/**
	 * Sets a header value.
	 */
	public function setHeader($name, $value)
	{
		$this->headers[$name] = $value;
		return $this;
	}

	public function getHeader($name)
	{
		if(isset($this->headers[$name])) {
			return $this->headers[$name];
		} else {
			return false;
		}
	}

	public function getHeaders()
	{
		return $this->headers;
	}

    /**
     * Initiates a session.
     */
    public function startSession()
    {
        $this->start_session = true;
    }

    /**
     * Sets a SESSION variable.
     * @param varname is the variable's name.
     * @param varval is the value to assign to the variable.
     */
    public function setSession($varname, $varval)
    {
        $this->sessionvars[$varname] = $varval;
    }

    /**
     * Retrieves the value of a session variable.
     * @param $varname is the variable's name
     * @param $default is the default value to be returned.
     */
    public function getSession($varname, $default = false)
    {
        if(isset($this->sessionvars[$varname])) {
            return $this->sessionvars[$varname];
        } else {
            return false;
        }
    }

    /**
     * Sets a SESSION variable.
     * @param varname is the variable's name.
     * @param varval is the value to assign to the variable.
     */
    public function setCookie($varname, $varval)
    {
        $this->sessionvars[$varname] = $varval;
    }

    /**
     * Retrieves the value of a session variable.
     * @param $varname is the variable's name
     * @param $default is the default value to be returned.
     */
    public function getCookie($varname, $default = false)
    {
        if(isset($this->sessionvars[$varname])) {
            return $this->sessionvars[$varname];
        } else {
            return false;
        }
    }

    public function hasSession()
    {
        return $this->start_session;
    }

	protected function httpStatus()
	{
		$statuses = array(
			100 => '100 Continue',
			101 => '101 Switching Protocols',
			200 => '200 OK',
			201 => '201 Created',
			202 => '202 Accepted',
			203 => '203 Non-Authoritative Information',
			204 => '204 No Content',
			205 => '205 Reset Content',
			206 => '206 Partial Content',
			300 => '300 Multiple Choices',
			301 => '301 Moved Permanently',
			302 => '302 Found',
			303 => '303 See Other',
			304 => '304 Not Modified',
			305 => '305 Use Proxy',
			306 => '306 (Unused)',
			307 => '307 Temporary Redirect',
			400 => '400 Bad Request',
			401 => '401 Unauthorized',
			402 => '402 Payment Required',
			403 => '403 Forbidden',
			404 => '404 Not Found',
			405 => '405 Method Not Allowed',
			406 => '406 Not Acceptable',
			407 => '407 Proxy Authentication Required',
			408 => '408 Request Timeout',
			409 => '409 Conflict',
			410 => '410 Gone',
			411 => '411 Length Required',
			412 => '412 Precondition Failed',
			413 => '413 Request Entity Too Large',
			414 => '414 Request-URI Too Long',
			415 => '415 Unsupported Media Type',
			416 => '416 Requested Range Not Satisfiable',
			417 => '417 Expectation Failed',
			500 => '500 Internal Server Error',
			501 => '501 Not Implemented',
			502 => '502 Bad Gateway',
			503 => '503 Service Unavailable',
			504 => '504 Gateway Timeout',
			505 => '505 HTTP Version Not Supported'
			);
		return $statuses[$this->status_code];
	}

	/**
	 * Generates the page.
	 */
	public function compile()
	{
        // Starting session first.
        if($this->start_session) {
            session_start();
            $_SESSION = array_merge($_SESSION, $this->sessionvars);
        }

		header('HTTP/1.1 ' . $this->httpStatus());
		header('Content-Type: ' . $this->content_type);
		foreach($this->headers as $hdrkey => $hdrval) {
			header($hdrkey, $hdrval);
		}

        $_COOKIE = array_merge($_COOKIE, $this->cookievars);

		echo $this->body;
	}
}

?>
