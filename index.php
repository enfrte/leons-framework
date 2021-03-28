<?php

$config = require_once 'config.php';
$routes = require_once 'routes.php'; 

spl_autoload_register(function ($class_name) {
    include 'classes/'.$class_name . '.php';
});

//$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Check GET http method
$get = (!empty($_GET)) ? $_GET : false;
// Check POST http method 
$post = (!empty($_POST)) ? $_POST : false;

$req = [];
$req['get'] = $get;
$req['post'] = $post;
$req['host'] = $_SERVER['HTTP_HOST'];
$req['uri'] = $_SERVER['REQUEST_URI'];
$_SESSION['req'] = $req; // This is probably a bad idea

// Remove the subfolder from the path if there is a subfolder and return the rest of the path as an array
$uri_exp = explode('/', $_SERVER['REQUEST_URI']);
$sub_folder_index = array_search($config['sub_folder_path'], $uri_exp);
$uri_array = array_values(array_filter(array_slice($uri_exp, $sub_folder_index + 1)));

$route_results = [];
$found_match = "";

// First, try to match a route in routes.php
foreach ($routes as $r_k => $r_v) {
    $args = [];
    if ($found_match === "") {
        $r_uri = array_filter(explode('/', $r_v));
        $match_count = 0; 
        foreach ($r_uri as $r_uri_k => $r_uri_v) {
            if (count($uri_array) != count($r_uri)) {
                break;
            }
            if ($r_uri_v == $uri_array[$r_uri_k]) {
                $match_count++;
            }
            if (strpos($r_uri_v, ":") !== false) {
                $match_count++;
                $args[] = $uri_array[$r_uri_k]; // add to args
            }
            if ($match_count == count($r_uri)) {
                $found_match = $r_k;
                $route_results['uri'] = $r_k;
                $route_results['args'] = $args;
                break;
            }
        }
    } 
    else {
        break;
    }   
}

$result = false;

if (!empty($route_results)) {
    $uri_exp = array_filter(explode('/',$route_results['uri']));
    $controller = $uri_exp[0];
    $method = (isset($uri_exp[1])) ? $uri_exp[1] : 'index';
    $c = new $controller();
    $result = $c->$method(...$args);
}
elseif (!empty($uri_array)) { // If no route was found in routes.php, try to directly match the URI to a controller/method
    $controller = $uri_array[0];
    $c = new $controller();
    // Get method
    if (count($uri_array) > 1) $method = $uri_array[1];
    // Get arguments
    if (count($uri_array) > 2) $arguments = array_slice($uri_array, 2);
    // Create object instance
    try {
        if (isset($method) && isset($arguments)) {
            $result = $c->$method(...$arguments);
        }
        elseif (isset($method)) {
            $result = $c->$method();
        }
        else {
            $result = $c->index();
        }
    } catch (\Throwable $th) {
        throw $th;
    }

}
else { // No URI called. Run default controller action
    $c = new $config['default_controller']();
    $result = $c->index();
}

return $result; // the result of the controller output
