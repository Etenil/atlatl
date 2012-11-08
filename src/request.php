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

    /**
     * Does the request contain files?
     */
    function hasFiles()
    {
        return (count($_FILES) > 0);
    }

    /**
     * Gets an uploaded file.
     */
    function getFile($slot_name, $target, $exts = NULL)
    {
        if(!$target) {
            throw \Exception("Can't move file without destination.");
        }

        if(!array_key_exists($slot_name, $_FILES)) {
            return false;
        }

        $normalizeChars = array(
            'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A',
            'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A',
            'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I',
            'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O',
            'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
            'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a',
            'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a',
            'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i',
            'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o',
            'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
            'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y',
            'ƒ'=>'f',
            );

        if(is_dir($target)) {
            $filename = basename($_FILES[$slot_name]['name']);
            $filename = preg_replace('#[^a-zA-Z0-9._-]#', '_', strtr($filename, $normalizeChars));
            $target = Utils::joinPaths($target, $filename);
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
                mvc_add_error(t("The uploaded file is not allowed."));
                return false;
            }
        }

        var_dump($target); exit;

        if(move_uploaded_file($_FILES[$slot_name]['tmp_name'], $target)) {
            return $target;
        } else {
            return false;
        }
    }
}

?>
