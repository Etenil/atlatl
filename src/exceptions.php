<?php

/**
 * Multiple exceptions for Atlatl.
 *
 * @package exceptions
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

namespace atlatl;

class HTTPStatus extends \Exception
{
    // Error code becomes mandatory and let's reorder them.
    function __construct($status_code, $description)
    {
        parent::__construct($description, $status_code);
    }
}

/**
 * Redirect (probably the most used exception).
 */
class HttpRedirect extends HTTPStatus
{
    /** The URL to redirect to. */
    protected $url;

    /**
     * Redirects the visitor to some URL.
     * @param string $url is the URL to send the visitor to.
     */
    public function __construct($url, $code = 301)
    {
        $this->code = $code;
        $this->url = $url;
    }

    /**
     * Gets the url to redirect to.
     */
    public function getUrl()
    {
        return $this->url;
    }
}

class HTTPSuccess extends HTTPStatus
{}

/**
 * Client-side HTTP error.
 */
class HTTPClientError extends HTTPStatus
{}

/**
 * Server-side HTTP error.
 */
class HTTPServerError extends HTTPStatus
{}

/**
 * Exception for a route that doesn't exist.
 */
class NoRouteException extends HTTPClientError
{}

/**
 * No handler to a route.
 */
class NoHandlerException extends HTTPClientError
{}

/**
 * View doesn't exist, file not found.
 */
class NoViewException extends HTTPClientError
{}

/**
 * The object returned by a controller cannot be converted to a
 * Response.
 */
class IllegalResponseException extends HTTPServerError
{}

/**
 * Pre-empts execution of other modules.
 */
class ModulePreemptException extends \Exception
{}
