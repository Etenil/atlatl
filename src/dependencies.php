<?php

namespace atlatl;

// Core.
Injector::register('Core', function($prefix = '') {
        $server = Injector::give('Server', $_SERVER, $prefix);
        $request = Injector::give('Request', $_GET, $_POST);
        $mc = Injector::give('ModuleContainer', $server);
        return new Core($prefix, $server, $request, $mc);
    });

// Server
Injector::register('Server', function(array $data, $prefix = '') {
        return new Server($data, $prefix);
    });

// Request
Injector::register('Request', function(array $get, array $post) {
        return new Request($get, $post);
    });

// ModuleContainer
Injector::register('ModuleContainer', function(Server $server) {
        return new ModuleContainer($server);
    });

// Response
Injector::register('Response',
    function($body = '', $status_code = 200,
        $content_type = 'text/html; charset=UTF-8',
        array $cookies = null, array $session = null) {
        if(!$session) {
            // PHP 5.4+ first.
            if((function_exists('session_status')
                    && session_status() == PHP_SESSION_ACTIVE)
                || isset($_SESSION)) {
                $session = $_SESSION;
            }
            else if(session_id() && isset($_SESSION)) {
                $session = $_SESSION;
            }
            else {
                $session = array();
            }
        }

        if(!$cookies) {
            $cookies = $_COOKIE;
        }

        return new Response($body, $status_code, $content_type, $cookies, $session);
    });

// Security
Injector::register('Security', function() {
        return new Security();
    });