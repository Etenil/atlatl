<?php

namespace atlatl;

/**
 * A collection of static functions to ease many tasks.
 */
class Utils
{
    /**
     * Joins two paths together. Works for operating system paths
     * or for URLs.
     */
    public static function joinPaths($path1, $path2)
    {
        $abspath = $path1[0] == '/';

        $paths = array_map(function($path) {
                $path = preg_replace('%^/%', '', $path);
                $path = preg_replace('%/$%', '', $path);
                return $path;
            },
            func_get_args());

        $path = implode('/', $paths);

        if($abspath) {
            return '/' . $path;
        } else {
            return $path;
        }
    }
}
