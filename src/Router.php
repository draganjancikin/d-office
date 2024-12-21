<?php

namespace App;

use App\Controller\HomeController;
use Exception;

/**
 * Router class.
 */
class Router {
  private $routes = [];

  // Add a route.
  public function add($route, $action) {
    $this->routes[$route] = $action;
  }

  // @todo - Remove commented code.
  // Dispatch a route.
//  public function dispatch($uri) {
//
//    foreach ($this->routes as $route => $action) {
//      if ($this->match($route, $uri, $params)) {
//        $this->executeAction($action, $params);
//        return;
//      }
//    }
//    throw new Exception("Route not found");
//  }
  public function dispatch($uri) {
    // Separate the path and query string
    $uriParts = explode('?', $uri, 2);
    $path = $uriParts[0];
    $queryString = $uriParts[1] ?? '';

    // Parse query string into an array
    parse_str($queryString, $queryParams);

    foreach ($this->routes as $route => $action) {
      if ($this->match($route, $path, $params)) {
        // Merge route parameters and query parameters
        $params = array_merge($params, $queryParams);
        $this->executeAction($action, $params);
        return;
      }
    }
    throw new Exception("Route not found");
  }

  // @todo - Remove commented code.
  // Match URI to a route.
//  private function match($route, $uri, &$params) {
//    // Convert dynamic placeholders in the route to regex patterns.
//    $routePattern = preg_replace('/{(\w+)}/', '([^/]+)', $route);
//    $routePattern = '#^' . $routePattern . '$#';
//
//    // Check if the URI matches the pattern.
//    if (preg_match($routePattern, $uri, $matches)) {
//      // The first match is the full URI, we discard it.
//      array_shift($matches);
//      // Fill the parameters array with dynamic parameters.
//      preg_match_all('/{(\w+)}/', $route, $paramNames);
//      $params = array_combine($paramNames[1], $matches);
//      return true;
//    }
//
//    return false;
//  }
  private function match($route, $uri, &$params) {
    // Convert dynamic placeholders in the route to regex patterns.
    $routePattern = preg_replace('/{(\w+)}/', '([^/]+)', $route);
    $routePattern = '#^' . $routePattern . '$#';

    // Check if the URI matches the pattern.
    if (preg_match($routePattern, $uri, $matches)) {
      // The first match is the full URI, we discard it.
      array_shift($matches);
      // Fill the parameters array with dynamic parameters.
      preg_match_all('/{(\w+)}/', $route, $paramNames);
      $params = array_combine($paramNames[1], $matches);
      return true;
    }

    return false;
  }

  // @todo - Remove commented code.
  // Execute the controller action
//  private function executeAction($action, $params) {
//    list($controller, $method) = explode('@', $action);
//
//    $namespace = '\App\Controller';
//    $fullyQualifiedName = $namespace . '\\' . $controller;
//
//    $controller = new $fullyQualifiedName();
//    if (!method_exists($controller, $method)) {
//      throw new Exception("Method $method not found in controller $controller");
//    }
//
//    // Call the method with parameters
//    call_user_func_array([$controller, $method], $params);
//  }

  private function executeAction($action, $params) {
    list($controller, $method) = explode('@', $action);

    $namespace = '\App\Controller';
    $fullyQualifiedName = $namespace . '\\' . $controller;

    $controller = new $fullyQualifiedName();
    if (!method_exists($controller, $method)) {
      throw new Exception("Method $method not found in controller $controller");
    }

    // Call the method with parameters
    call_user_func_array([$controller, $method], $params);
  }
}