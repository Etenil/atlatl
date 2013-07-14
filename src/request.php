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
    /** Stores Session vars. */
    protected $sessionvars;
    /** Stores Cookie vars. */
    protected $cookievars;

	/**
	 * Constructor, loads up GET and POST variables.
	 * @param array    $get        $_GET array.
	 * @param array    $post       $_POST array.
	 */
	public function __construct(array $get, array $post, array $session, array $cookies)
	{
		$this->getvars = $get;
		$this->postvars = $post;
        $this->sessionvars = $session;
        $this->cookievars = $cookies;
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
     * Returns a string with all GET parameters as they appear in the URL.
     */
    public function getToString()
    {
        $get = $this->allGet();
        $formatted = array();
        foreach($get as $name => $val) {
            $formatted[] = "$name=$val";
        }

        return implode('&', $formatted);
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

    /**
     * Sets a SESSION variable.
     * @param varname is the variable's name.
     * @param varval is the value to assign to the variable.
     * @return FALSE if session isn't started.
     */
    public function setSession($varname, $varval)
    {
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
        unset($this->sessionvars[$varname]);
        return $this;
    }

    public function setAllSession(array $session) {
        $this->sessionvars = $session;
    }

    public function setAllCookies(array $cookies) {
        $this->cookievars = $cookies;
    }

    /**
     * Clears a cookie variable.
     * @param $varname is the cookie variable's name.
     */
    public function killCookie($varname)
    {
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
     * Does the request contain files?
     */
    function hasFiles()
    {
        return (count($_FILES) > 0);
    }

    function _getAndMoveFile($file, $target, $exts = null)
    {
        $target_filename = basename($target);
        $target_dir = dirname($target);
        $target = Utils::joinPaths($target_dir, Utils::cleanFilename($target_filename));

        if($file['error'] > 0) {
            throw new FileUploadError($file['error']);
        }

        if($exts != NULL && count($exts > 0)) {
            $allowed = false;
            foreach($exts as $ext) {
                if(preg_match("#".$ext.'$#', $target)) {
                    $allowed = true;
                    break;
                }
            }

            if(!$allowed) {
                throw new \Exception("The uploaded file is not allowed.");
            }
        }

        if(move_uploaded_file($file['tmp_name'], $target)) {
            return $target;
        } else {
            return false;
        }
    }

    /**
     * Gets an uploaded file.
     */
    function getFile($slot_name, $target, $exts = NULL, $generate_names = false)
    {
        if(!$target) {
            throw new \Exception("Can't move file without destination.");
        }

        if(!array_key_exists($slot_name, $_FILES)) {
            return false;
        }

        $return = false;

        // Several files uploaded with the same name like uploads[].
        if(is_array($_FILES[$slot_name]['name'])) {
            if(!is_dir($target)) {
                throw new Exception("Target `$target' must be a directory for several files.");
            }

            $return = array();
            for($file_num = 0; $file_num < count($_FILES[$slot_name]['name']); $file_num++) {
                $file = array(
                    'name' => $_FILES[$slot_name]['name'][$file_num],
                    'type' => $_FILES[$slot_name]['type'][$file_num],
                    'tmp_name' => $_FILES[$slot_name]['tmp_name'][$file_num],
                    'error' => $_FILES[$slot_name]['error'][$file_num],
                    'size' => $_FILES[$slot_name]['size'][$file_num]
                    );

                $looptarget = '';
                if($generate_names) {
                    // Getting the ext. I don't use pathinfo() because the file may not exist...
                    $ext = substr(strrchr($file['name'], '.'), 0);
                    $looptarget = Utils::uniqueFilename($target, '', $ext);
                } else {
                    $looptarget = Utils::joinPaths($target, basename($file['name']));
                }

                try {
                    $return[] = $this->_getAndMoveFile($file, $looptarget, $exts);
                }
                catch(FileUploadError $e) {
                    $return[] = $e;
                }
            }
        } else {
            if(is_dir($target)) {
                if($generate_names) {
                    $ext = substr(strrchr($_FILES[$slot_name]['name'], '.'), 0);
                    $target = Utils::uniqueFilename($target, '', $ext);
                } else {
                    $target = Utils::joinPaths($target, basename($_FILES[$slot_name]['name']));
                }
            }

            try {
                $return = $this->_getAndMoveFile($_FILES[$slot_name], $target, $exts);
            }
            catch(FileUploadError $e)
            {
                return $e;
            }
        }

        return $return;
    }

    function commitSessionAndCookies() {
        $_SESSION = $this->sessionvars;
        $_COOKIE = $this->cookievars;
    }
}

