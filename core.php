<?php

namespace glue;

require('security.php');

    /**
     * glue
     *
     * Provides an easy way to map URLs to classes. URLs can be literal
     * strings or regular expressions.
     *
     * When the URLs are processed:
     *      * delimiter (/) are automatically escaped: (\/)
     *      * The beginning and end are anchored (^ $)
     *      * An optional end slash is added (/?)
     *	    * The i option is added for case-insensitive searches
     *
     * Example:
     *
     * $urls = array(
     *     '/' => 'index',
     *     '/page/(\d+)' => 'page'
     * );
     *
     * class page {
     *      function GET($matches) {
     *          echo "Your requested page " . $matches[1];
     *      }
     * }
     *
     * glue::stick($urls);
     *
     */
    class Core {
		// URL prefix.
		protected $prefix = "";

		/**
		 * Class constructor.
		 * @param string $prefix is the URL prefix to use for this application.
		 */
		public function __construct($prefix = "")
		{
			$this->setPrefix($prefix);
		}
		
		/**
		 * Changes the URL prefix to work from.
		 * @param string $prefix is the URL prefix to use, for instance "/glue".
		 * @return this object (you can make a call chain).
		 */
		public function setPrefix($prefix)
		{
			// We ensure that the prefix is properly formatted. It
			// must start with a '/' and end without one.
			if($prefix != "") {
				if($prefix[0] != '/') {
					$prefix = '/' . $prefix;
				}
				if($prefix[strlen($prefix)] == '/') {
					$prefix = substr($prefix, 0, strlen($prefix) - 1);
				}
			}
			
			$this->prefix = $prefix;
			return $this;
		}
		
        /**
         * stick
         *
         * the main method of the glue class.
         *
         * @param   array    	$urls  	    The regex-based url to class mapping
         * @throws  Exception               Thrown if corresponding class is not found
         * @throws  Exception               Thrown if no match is found
         * @throws  BadMethodCallException  Thrown if a corresponding GET,POST is not found
         *
         */
        function serve(array $urls) {

            $method = strtoupper($_SERVER['REQUEST_METHOD']);
            $path = $_SERVER['REQUEST_URI'];
			
			if($path == $this->prefix) {
				$path.= '/'; // This is necessary to match '/' with a prefix.
			}

            $found = false;

            krsort($urls);

            foreach ($urls as $regex => $class) {
                $regex = str_replace('/', '\/', $this->prefix . $regex);
                $regex = '^' . $regex . '\/?$';
                if (preg_match("/$regex/i", $path, $matches)) {
                    $found = true;
                    if (class_exists($class)) {
                        $obj = new $class;
                        if (method_exists($obj, $method)) {
							call_user_func_array(array($obj, $method),
												 array_slice($matches, 1));
                        } else {
                            throw new \BadMethodCallException("Method, $method, not supported.");
                        }
                    } else {
                        throw new \Exception("Class, $class, not found.");
                    }
                    break;
                }
            }
            if (!$found) {
                throw new \Exception("URL, $path, not found.");
            }
        }
    }
