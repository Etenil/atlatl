<?php

/**
 * Abstraction of an HTTP response.
 */

namespace atlatl;

/**
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
    /** HTTP headers array. */
	protected $headers;
    /** HTTP status code as integer. */
	protected $status_code;
    /** HTTP response's body string. */
	protected $body;
    /** Content type header. */
	protected $content_type;

    // TODO: Deprecate those two vars.
    protected $sessionvars;
    protected $cookievars;
    protected $alteredsession = false;
    protected $alteredcookies = false;

    /**
     * Instanciates an HTTP response.
     * @param string $body initiates the body content if any. Default
     * is ''.
     * @param integer $status_code is the initial HTTP status
     * code. Default is 200.
     * @param string $content_type is the initial
     * content-type. Default is 'text/html; charset=UTF-8'.
     * @param array $cookies is a cookies array. Default is $_COOKIE.
     * @param array $session is the initial session variable. Default
     * is $_SESSION if PHP's session was started.
     */
	public function __construct($body = '', $status_code = 200,
								$content_type = 'text/html; charset=UTF-8',
                                array $cookies = null, array $session = null)
	{
		$this->status_code = $status_code;
		$this->content_type = $content_type;
		$this->headers = array();
		$this->body = $body;

        $this->sessionvars = $session ?: array();
        $this->cookievars = $cookies ?: array();
	}

    /**
     * Redirects to a different page.
     * @param string $url is the url to redirect to.
     */
    public function redirect($url)
    {
        $this->setStatus(301);
        $this->setHeader('Location', $url);
        return $this;
    }

	/**
	 * Sets the contents of the HTTP response's body.
     * @param string $data replaces body's contents.
	 */
	public function setBody($data)
	{
		$this->body = $data;
		return $this;
	}

    /**
     * Retrieves the body.
     * @return string body
     */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * Appends a string to body.
     * @param string $section will be appended to body.
	 */
	public function append($section)
	{
		$this->body .= $section;
		return $this;
	}

	/**
	 * Sets a header value.
     * @param string $name is the header variable's name.
     * @param mixed $value is the value to assign to $name.
	 */
	public function setHeader($name, $value)
	{
		$this->headers[$name] = $value;
		return $this;
	}

    /**
     * Retrieves a header's value.
     * @param string $name is the header's name.
     * @return mixed the header variable's value or FALSE if not found.
     */
	public function getHeader($name)
	{
		if(isset($this->headers[$name])) {
			return $this->headers[$name];
		} else {
			return false;
		}
	}

    /**
     * Returns an array containing all headers.
     * @return array headers.
     */
	public function getHeaders()
	{
		return $this->headers;
	}

    /**
     * Sets a SESSION variable.
     * @param varname is the variable's name.
     * @param varval is the value to assign to the variable.
     * @return FALSE if session isn't started.
     */
    public function setSession($varname, $varval)
    {
        $this->alteredsession = true;
        $this->sessionvars[$varname] = $varval;
        return $this;
    }

    /**
     * Retrieves the value of a session variable.
     * @param $varname is the variable's name
     * @param $default is the default value to be returned.
     * @return the session variable or FALSE if it can't be retrieved.
     */
    public function getSession($varname, $default = false)
    {
        if(isset($this->sessionvars[$varname])) {
            return $this->sessionvars[$varname];
        } else {
            return $default;
        }
    }

    /**
     * Clears a session variable.
     * @param $varname is the session variable's name.
     */
    public function killSession($varname)
    {
        $this->alteredsession = true;
        unset($this->sessionvars[$varname]);
        return $this;
    }

    public function alteredSession() {
        return $this->alteredsession;
    }

    public function alteredCookies() {
        return $this->alteredcookies;
    }

    /**
     * Clears a cookie variable.
     * @param $varname is the cookie variable's name.
     */
    public function killCookie($varname)
    {
        $this->alteredcookies = true;
        unset($this->cookievars[$varname]);
        return $this;
    }

    /**
     * Sets a COOKIE variable.
     * @param varname is the variable's name.
     * @param varval is the value to assign to the variable.
     */
    public function setCookie($varname, $varval)
    {
        $this->alteredcookies = true;
        $this->cookievars[$varname] = $varval;
    }

    /**
     * Retrieves the value of a cookie variable.
     * @param $varname is the variable's name
     * @param $default is the default value to be returned.
     */
    public function getCookie($varname, $default = false)
    {
        if(isset($this->cookievars[$varname])) {
            return $this->cookievars[$varname];
        } else {
            return $default;
        }
    }

    /**
     * Sets the HTTP status code.
     * @param int $statuscode is the HTTP status code to be returned.
     */
    public function setStatus($statuscode)
    {
        if($this->httpStatus($statuscode)) {
            $this->status_code = $statuscode;
        }
        return $this;
    }

    /**
     * Gets all cookies.
     */
    function getAllCookies() {
        return $this->cookievars;
    }

    /**
     * Gets all session.
     */
    function getAllSession() {
        return $this->sessionvars;
    }

    /**
     * Gets the current HTTP status code.
     */
    public function getStatus()
    {
        return $this->status_code;
    }

    /**
     * Fetches the HTTP full status as string from the current integer status.
     * @param int $statuscode is the numeric status code to fetch.
     */
	protected function httpStatus($statuscode)
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

        if(array_key_exists($statuscode, $statuses)) {
            return $statuses[$statuscode];
        } else {
            return false;
        }
	}

	/**
	 * Generates the page.
	 */
	public function compile()
	{
        $session_started = false;
        if(function_exists('session_status') && session_status() == PHP_SESSION_ACTIVE) {
          $session_started = true;
        }
        else if(isset($_SESSION)) {
          $session_started = true;
        }

        if($session_started) {
          // Session handling.
          if(!is_array($this->sessionvars)) {
            $this->sessionvars = array();
          }
          $_SESSION = @array_merge($_SESSION, $this->sessionvars);
        }

		header('HTTP/1.1 ' . $this->httpStatus($this->getStatus()));
		header('Content-Type: ' . $this->content_type);
		foreach($this->headers as $hdrkey => $hdrval) {
			header($hdrkey . ': ' . $hdrval);
		}

        $_COOKIE = @array_merge($_COOKIE, $this->cookievars);

		echo $this->body;
	}
}

?>
